<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BillCreationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'sp_code' => 'required|string|max:10',
            'description' => 'required|string',

            // Customer details
            'customer' => 'required|array:tin_number,id_number,id_type,name,phone,email',
            'customer.tin_number' => 'required|string|max:50',
            'customer.id_number' => 'required|string|max:50',
            'customer.id_type' => 'required|integer',
            'customer.name' => 'required|string|max:200',
            'customer.phone' => 'string|max:12',
            'customer.email' => 'string|max:30',

            'expires_on' => 'required|date',
            'amount' => 'required|numeric',
            'callback_url' => 'required|url',

            // Bill Items Details
            'items' => 'required|array',
            'items.*.reference' => 'required|string|max:50',
            'items.*.sub_sp_code' => 'string|max:10',
            'items.*.gfs_code' => 'required|string|max:20',
            'items.*.amount' => 'required|numeric',

        ];
    }
}
