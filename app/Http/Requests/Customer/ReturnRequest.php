<?php

namespace App\Http\Requests\Customer;

use Illuminate\Foundation\Http\FormRequest;

class ReturnRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // log the action.
        logger()->info("[app\Http\Requests\Customer\ReturnRequest@authorize] Authorization initiated!");

        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // log the action
        logger()->info("[app\Http\Requests\Customer\ReturnRequest@rules] Validation initiated!");

        return [
            'reason' => 'required|string|max:500'
        ];
    }
}
