<?php

namespace App\Events;

use App\Models\BatchFile\BatchFile;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BatchFileUploaded
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public BatchFile $batchFile;

    public function __construct(BatchFile $batchFile)
    {
        $this->batchFile = $batchFile;
    }
}
