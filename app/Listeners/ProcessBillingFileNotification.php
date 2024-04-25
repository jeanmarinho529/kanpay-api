<?php

namespace App\Listeners;

use App\Events\BatchFileUploaded;
use App\Jobs\BatchFileProcessJob;
use App\Models\Charge;

class ProcessBillingFileNotification
{
    public function handle(BatchFileUploaded $event): void
    {
        BatchFileProcessJob::dispatch($event->batchFile, Charge::class);
    }
}
