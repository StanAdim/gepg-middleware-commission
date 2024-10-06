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
            'uuid' => 'required|uuid',
            'description' => 'required|string',
            'phone_number' => 'required|string|max:20',
            'customer_name' => 'required|string|max:200',
            'customer_email' => 'required|email|max:46',
            'approved_by' => 'required|string',
            'amount' => 'required|numeric',
            'ccy' => 'required',
            'payment_option' => 'required|numeric',
            'status_code' => 'required|numeric',
            'expires_at' => 'required|date',
        ];
    }
}
