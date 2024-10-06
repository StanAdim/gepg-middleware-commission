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
        Http::system()
            ->post(
                config('app.system.controlNoUpdatePath') . "/{$event->bill->payment_order_id}",
                [
                    'message' => 'Updated Control Number',
                    'control_number' => $event->bill->customer_cntr_num,
                ]
            );
    }
}
