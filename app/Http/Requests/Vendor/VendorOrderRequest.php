<?php

namespace App\Http\Requests\Vendor;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class VendorOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // log the status
        logger()->info('[app\Http\Requests\Vendor\VendorOrderRequest@authorize] Authorization initiated');

        // get model from route
        $item = $this->route('item');

        // get vendor
        $user = $this->user();

        // check if ordered item belongs to vendor
        if ($item->vendor_id !== $user->id) {
            logger()->alert("Vendor ID {$user->id} attempted to access OrderItem ID {$item->id} | Terminating the request");

            return false;
        }

        // check if payment was done!
        if (! $item->order->wasStockReduced()) {
            // log the status
            logger()->alert("Stock was reduced when order was placed! {$item->id} | Terminating the request");

            return false;
        }

        // log the success
        logger()->info('Authorization Success');

        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // log the status
        logger()->info('[app\Http\Requests\Vendor\VendorOrderRequest@rules] Validation initiated');

        // get current route name
        $routeName = $this->route()->getName();

        // when route is for order cancellation
        if ($routeName === 'vendor.orders.cancel') {
            return [
                'cancel_reason' => 'required|string|max:255',
            ];
        }

        // otherwise,
        return [
            'order_status' => [
                'required',
                'in:processing,ready_for_pickup',
            ],
        ];
    }

    /**
     * messages for error
     */
    public function messages(): array
    {
        return [
            'order_status.required' => 'Status is to be selected',
            'order_status.in' => 'Can only set processing or ready for pickup.',
        ];
    }
}
