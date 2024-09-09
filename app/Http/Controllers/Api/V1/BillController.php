<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\BillCreationRequest;
use App\Http\Resources\BillResource;
use App\Models\Bill;
use Carbon\Carbon;

class BillController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(BillCreationRequest $request)
    {
        // Retrieve the validated data
        $validated = $request->validated();

        $bill = Bill::create([
            'description' => $validated['description'],
            'user_id' => $validated['user_id'],
            'phone_number' => $validated['phone_number'],
            'customer_name' => $validated['customer_name'],
            'customer_email' => $validated['customer_email'],
            'approved_by' => $validated['approved_by'],
            'amount' => $validated['amount'],
            'ccy' => $validated['ccy'],
            'payment_option' => $validated['payment_option'],
            'status_code' => $validated['status_code'],
            'expires_at' => Carbon::parse($validated['expires_at']),
            'payment_order_id' => $validated['payment_order_id'],
            'callback_url' => $validated['callback_url'],
        ]);

        return new BillResource($bill);
    }

}
