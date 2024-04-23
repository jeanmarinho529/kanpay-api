<?php

namespace App\Services;

use App\Enums\BatchFileStatusEnum;
use App\Events\BatchFileUploaded;
use App\Models\BatchFile\BatchFile;
use App\Models\BatchFile\BatchFileStatus;
use App\Models\BatchFile\BatchFileType;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Ramsey\Collection\Collection;

class BatchFileService
{
    public function index(array $data): BatchFile|Paginator
    {
        return BatchFile::with(['batchFileStatus', 'batchFileType'])->simplePaginate($data['items'] ?? 10);
    }

    public function show(string $id): BatchFile|Model|Collection|static
    {
        return BatchFile::with(['batchFileStatus', 'batchFileType'])->findOrFail($id);
    }

    public function uploadFile(object $file, array $data): BatchFile
    {
        try {
            $type = BatchFileType::where('name', $data['file_type_name'])->first();

            $filePath = $file->store('batch_file/'.$type->name);
            $status = BatchFileStatus::where('name', BatchFileStatusEnum::PROGRESS->value)->first();

        } catch (\Exception $exception) {
            Log::error($exception);
            $status = BatchFileStatus::where('name', BatchFileStatusEnum::ERROR->value)->first();
        }

        $file = BatchFile::create([
            'batch_file_status_id' => $status->id,
            'batch_file_type_id' => $type->id,
            'name' => $file->getClientOriginalName(),
            'path' => $filePath ?? '',
            'total_items' => 0,
        ]);

        if ($status->name === BatchFileStatusEnum::PROGRESS->value) {
            event(new BatchFileUploaded($file));
        }

        return $file;
    }
}
