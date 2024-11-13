<?php

namespace App\Listeners;

use App\Events\ReconciliationRequested;
use App\Gepg\XmlRequestHelper;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class RequestReconciliationReport implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
    }

    /**
     * Handle the event.
     */
    public function handle(ReconciliationRequested $event): void
    {
        XmlRequestHelper::GepgReconciliationRequest(
            $event->dateTime->format('Y-m-d')
        );
    }
}
