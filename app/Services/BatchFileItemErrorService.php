<?php

namespace App\Services;

use App\Jobs\BatchFilePersistItemErrorsJob;
use App\Models\BatchFile\BatchFile;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\MessageBag;

class BatchFileItemErrorService
{
    protected mixed $validatorClass;

    public function __construct(mixed $validatorClass = null)
    {
        $this->validatorClass = $validatorClass;
    }

    public function validate(array $data, BatchFile $file): array
    {
        if (! $this->validatorClass) {
            return $data;
        }

        $this->validatorClass = new $this->validatorClass();

        $validator = Validator::make($data, $this->validatorClass->rules(), $this->validatorClass->messages());

        if ($validator->fails()) {
            return $this->processValidationErrors($validator->errors(), $data, $file);
        }

        return $data;
    }

    public function rowErrors(array $errors, array $indexErrors, int $index, BatchFile $file): array
    {
        $batchFileErrors = [];
        foreach ($errors as $error) {
            $batchFileErrors[] = [
                'batch_file_id' => $file->id,
                'row_number' => $indexErrors[$index],
                'error' => $error,
                'created_at' => now()->format('Y-m-d H:i:s'),
                'updated_at' => now()->format('Y-m-d H:i:s'),
            ];
        }

        return $batchFileErrors;
    }

    public function processValidationErrors(MessageBag $validateErrors, array $data, BatchFile $file): array
    {
        $indexErrors = [];
        $batchFileErrors = [];

        foreach ($validateErrors->toArray() as $key => $errors) {

            if (preg_match('/^(\d+)\./', $key, $matches)) {
                $index = $matches[1];

                $rowNumber = $data[$index]['row_number'] ?? $indexErrors[$index];

                if (! array_key_exists($index, $indexErrors)) {
                    unset($data[$index]);
                    $indexErrors[$index] = $rowNumber;
                }

                $batchFileErrors = array_merge($batchFileErrors, $this->rowErrors($errors, $indexErrors, $index, $file));
            }
        }

        BatchFilePersistItemErrorsJob::dispatch($batchFileErrors);

        return $data;
    }
}
