<?php

namespace App\Listeners;

use App\Events\ControlNoResponseReceived;
use App\Http\Resources\BillResource;
use Http;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class CallbackWithControlNo implements ShouldQueue
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
    public function handle(ControlNoResponseReceived $event): void
    {
        Http::post($event->bill->callback_url, ['updated_bill' => new BillResource($event->bill)]);
    }
}
