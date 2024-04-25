<?php

namespace App\Jobs;

use App\Models\BatchFile\BatchFile;
use App\Services\BatchFileItemService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class BatchFileProcessJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private BatchFile $file;

    private string $modelClass;

    private string $validatorClass;

    public function __construct(BatchFile $file, string $modelClass, string $validatorClass)
    {
        $this->file = $file;
        $this->modelClass = $modelClass;
        $this->validatorClass = $validatorClass;
    }

    public function handle(): void
    {
        (new BatchFileItemService($this->modelClass, $this->validatorClass))->processItems($this->file);
    }
}
