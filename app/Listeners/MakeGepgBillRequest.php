<?php

namespace App\Listeners;

use App\Events\BillCreated;
use App\Gepg\XmlRequestHelper;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Log;

class MakeGepgBillRequest implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(BillCreated $event): void
    {
        $bill = $event->bill;

        $response = XmlRequestHelper::GepgSubmissionRequest($bill);
        Log::info($response);
    }
}
