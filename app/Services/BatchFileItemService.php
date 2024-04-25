<?php

namespace App\Services;

use App\Enums\BatchFileStatusEnum;
use App\Jobs\BatchFilePersistItemsJob;
use App\Models\BatchFile\BatchFile;
use App\Models\BatchFile\BatchFileStatus;
use Generator;
use Illuminate\Bus\Batch;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Log;

class BatchFileItemService extends BatchFileItemErrorService
{
    protected const CHUNK_SIZE = 1000;

    protected string $modelClass;

    public function __construct(string $modelClass, ?string $validatorClass = null)
    {
        $this->modelClass = $modelClass;
        parent::__construct($validatorClass);
    }

    public function processItems(BatchFile $file): BatchFile
    {
        try {
            $batch = Bus::batch([]);

            $count = 0;
            foreach ($this->chunkFile($file) as $chunk) {
                $batch->add(new BatchFilePersistItemsJob($this->modelClass, $chunk, $file));

                if ($count >= 10) {
                    $batch->finally(function (Batch $batch) use ($file) {
                        $this->updateBatchFile($file->refresh(), false);
                    })->dispatch();

                    $count = 0;
                    $batch = Bus::batch([]);
                }
                $count++;
            }

            $batch->finally(function (Batch $batch) use ($file) {
                $this->updateBatchFile($file->refresh());
            })->dispatch();

            $file->update(['total_items' => $this->getLastRowNumber($chunk ?? [])]);

            return $file->refresh();

        } catch (\Exception $exception) {
            Log::error($exception);
            $file->update([
                'batch_file_status_id' => BatchFileStatus::where('name', BatchFileStatusEnum::ERROR->value)->first()->id,
            ]);
            throw $exception;
        }
    }

    public function getStatus(int $totalItemsDone, BatchFile $file): BatchFileStatus
    {
        $statusName = BatchFileStatusEnum::ERROR->value;
        if ($totalItemsDone === $file->total_items) {
            $statusName = BatchFileStatusEnum::DONE->value;
        } elseif ($totalItemsDone > 0) {
            $statusName = BatchFileStatusEnum::PARTIAL->value;
        }

        return BatchFileStatus::where('name', $statusName)->first();
    }

    public function updateBatchFile(BatchFile $file, bool $updateStatus = true): BatchFile
    {
        $totalItemsDone = $file->charges()->count();
        $totalItemsError = $file->batchFileItemError()->count();

        if ($updateStatus) {
            $file->batch_file_status_id = $this->getStatus($totalItemsDone, $file)->id;
        }

        $file->total_done = $totalItemsDone;
        $file->total_failed = $totalItemsError;
        $file->save();

        return $file;
    }

    public function getLastRowNumber(array $chunk): int
    {
        return $chunk[count($chunk) - 1]['row_number'] ?? 0;
    }

    public function convertHeadersToSnakeCase(array $headers): array
    {
        $headersSnakeCase = [];
        foreach ($headers as $item) {
            $headersSnakeCase[] = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $item));
        }

        return $headersSnakeCase;
    }

    public function addHeaderInRow(array $headers, array $row, int $rowNumber, BatchFile $file): array
    {
        $row = array_combine($headers, $row);
        $row['row_number'] = $rowNumber;
        $row['batch_file_id'] = $file->id;
        $row['created_at'] = now()->format('Y-m-d H:i:s');
        $row['updated_at'] = now()->format('Y-m-d H:i:s');

        return $row;
    }

    public function chunkFile(BatchFile $file): Generator
    {
        try {
            $handle = fopen((storage_path('app/'.$file->path)), 'r');

            $headers = fgetcsv($handle, 0, ',');
            $headers = $this->convertHeadersToSnakeCase($headers);

            $chunkData = [];
            $chunkSize = 0;
            $rowNumber = 1;

            while (($row = fgetcsv($handle, 0, ',')) !== false) {
                $chunkData[] = $this->addHeaderInRow($headers, $row, $rowNumber, $file);
                $chunkSize++;
                $rowNumber++;

                if ($chunkSize >= self::CHUNK_SIZE) {
                    yield $chunkData;
                    $chunkData = [];
                    $chunkSize = 0;
                }
            }

            if (! empty($chunkData)) {
                info(count($chunkData));
                yield $chunkData;
            }
            fclose($handle);

        } catch (\Exception $exception) {
            Log::error($exception);
            if (isset($handle)) {
                fclose($handle);
            }
        }
    }
}
