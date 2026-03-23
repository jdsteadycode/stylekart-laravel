<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class CheckoutRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // check if no customer
        $customer = auth()->user();
        if (! $customer) {
            // log the status
            logger()->alert('No customer authenticated found! Order Placement terminated.');

            return false;
        }

        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // log the action..
        logger()->info('[app\Http\Requests\CheckoutRequest@rules] Validating the data before checkout!');

        // validate according to rules.
        return [
            // if default address opted check?
            'address_id' => 'nullable|exists:addresses,id',

            // If new address is opted?
            'name' => 'required_without:address_id|nullable|string|max:255',
            'phone' => 'required_without:address_id|nullable|string|max:15',
            'address_line' => 'required_without:address_id|nullable|string|max:500',
            'city' => 'required_without:address_id|nullable|string|max:255',
            'pincode' => 'required_without:address_id|nullable|string|max:10',
            'state' => 'required_without:address_id|nullable|string|max:255',
            'address_type' => 'required_without:address_id|nullable|in:home,office,other',

            // payment method / mode.
            'pay' => 'required|in:cod,online',
        ];
    }
}
