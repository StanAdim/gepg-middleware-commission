<?php

namespace App\Http\Controllers;

use App\Enums\BillState;
use App\Http\Requests\BillCreationRequest;
use App\Http\Resources\BillResource;
use App\Models\Bill;
use App\Models\BillItem;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Str;

class BillController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(BillCreationRequest $request)
    {
        // Retrieve the validated data
        $validated = $request->validated();

        $bill = Bill::create([
            'req_id' => Str::uuid(),
            'sp_code' => $validated['sp_code'],
            'bill_desc' => $validated['description'],
            'cust_tin' => $validated['customer']['tin_number'] ?? null,
            'cust_id' => $validated['customer']['id_number'],
            'cust_id_typ' => $validated['customer']['id_type'],
            'cust_name' => $validated['customer']['name'],
            'cust_cell_num' => $validated['customer']['phone'] ?? null,
            'cust_email' => $validated['customer']['email'] ?? null,
            'expires_on' => Carbon::parse($validated['expires_on']),
            'bill_gen_by' => 'system',
            'bill_appr_by' => 'system',
            'bill_amt' => $validated['amount'],
            'bill_eqv_amt' => $validated['amount'],
            'ccy' => 'TZS',
            'state' => BillState::NEW ,
            'external_callback_url' => $validated['callback_url'],
        ]);

        if ($bill) {
            foreach ($validated['items'] as $item) {
                BillItem::create([
                    'bill_id' => $bill->id,
                    'sub_sp_code' => $item['sub_sp_code'] ?? $validated['sp_code'],
                    'gfs_code' => $item['gfs_code'],
                    'bill_item_ref' => $item['reference'],
                    'bill_item_amt' => $item['amount'],
                    'bill_item_eqv_amt' => $item['amount'],
                ]);
            }
        }

        return new BillResource($bill);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
