<?php

namespace App\Listeners;

use App\Events\PaymentStatusUpdated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Http;

class NotifySystemOnPaymentStatusUpdate implements ShouldQueue
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
    public function handle(PaymentStatusUpdated $event): void
    {
        Http::system()
            ->post(
                config('app.system.paymentNotificationPath') . "/{$event->bill->payment_order_id}",
                [
                    'message' => 'Updated Payment Status',
                    'control_number' => $event->bill->customer_cntr_num,
                    'status' => 1,
                    'paid_amount' => $event->bill->paid_amt,
                    'data' => $event->bill->toJson(),
                ]
            );
    }
}
