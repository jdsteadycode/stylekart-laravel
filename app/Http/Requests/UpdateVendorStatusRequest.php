<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateVendorStatusRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // log the status
        logger()->info('[app\Http\Requests\UpdateVendorStatusRequest@authorize] Check Authorization!');

        // check if authorized!
        if ($this->user()->role !== 'admin') {
            logger()->alert('Not authorized! Terminating request.');

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
        // log the status
        logger()->info('[app\Http\Requests\UpdateVendorStatusRequest@rules] Validation initiated!');

        return [
            'status' => 'bail|required|in:pending,approved,rejected',
            'rejection_reason' => 'required_if:status,rejected',
        ];
    }

    /**
     * messages for validation errors..
     */
    public function messages(): array
    {
        return [
            // messages for error..
            'rejection_reason.required_if' => 'You must provide a reason when rejecting a vendor.',
        ];
    }
}
