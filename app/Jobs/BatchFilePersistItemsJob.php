<?php

namespace App\Jobs;

use App\Models\BatchFile\BatchFile;
use App\Services\BatchFileItemErrorService;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class BatchFilePersistItemsJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private string $modelClass;

    private ?string $validatorClass;

    private array $data;

    private BatchFile $file;

    public function __construct(string $modelClass, array $data, BatchFile $file, ?string $validatorClass = null)
    {
        $this->modelClass = $modelClass;
        $this->validatorClass = $validatorClass;
        $this->data = $data;
        $this->file = $file;
    }

    public function handle(): void
    {
        $validate = (new BatchFileItemErrorService($this->validatorClass))->validate($this->data, $this->file);
        (new $this->modelClass())->insert($validate);
    }
}
