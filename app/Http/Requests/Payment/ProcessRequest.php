<?php

namespace App\Http\Requests\Payment;

use Illuminate\Foundation\Http\FormRequest;

class ProcessRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // log the status
        logger()->info("[app\Http\Requests\Payment\ProcessRequest@authorize] Authorization initiated");
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // log the action
        logger()->info("[app\Http\Requests\Payment\ProcessRequest@rules] Validation initiated!");

        // validate against rules..
        return [
            'card_number' => 'required|string',
            'expiry' => 'required|string',
            'cvv' => 'required|string',
            'card_name' => 'required|string',
        ];
    }
}
