<?php

namespace App\Listeners;

use App\Events\BatchFileUploaded;
use App\Http\Requests\BillingRequest;
use App\Jobs\BatchFileProcessJob;
use App\Models\Billing;

class ProcessBillingFileNotification
{
    public function handle(BatchFileUploaded $event): void
    {
        BatchFileProcessJob::dispatch($event->batchFile, Billing::class, BillingRequest::class);
    }
}
