<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class DeliveryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // log the info
        logger()->info("[app\Http\Requests\Admin\DeliveryRequest@authorize] Authorization initiated");
        // check if authorized
        if ($this->user()->role !== 'admin') {
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
        // log the info
        logger()->info("[app\Http\Requests\Admin\DeliveryRequest@rules] Validation initiated");

        // validate it according to rules set
        return [
            'order_id' => 'required|exists:orders,id',
            'delivery_person_id' => 'required|exists:users,id',
        ];
    }
}
