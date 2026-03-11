<?php

namespace App\Http\Requests;

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
        if (!$customer) {
            //log the status
            logger()->alert('No customer authenticated found! Order Placement terminated.');
            return false;
        }
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // log the action..
        logger()->info('[app\Http\Requests\CheckoutRequest@rules] Validating the data before checkout!');

        // validate according to rules.
        return [
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:15',
            'address_line' => 'required|string|max:500',
            'city' => 'required|string|max:255',
            'pincode' => 'required|string|max:10',
            'pay' => 'required|in:cod,online'
        ];
    }
}
