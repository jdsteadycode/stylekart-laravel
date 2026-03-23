<?php

namespace App\Http\Requests\Vendor;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreDiscountRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // log the action..
        logger()->info("[app\Http\Requests\Vendor\StoreDiscountRequest@authorize] Authorization Initiated");

        // if legit
        if (auth()->check() && auth()->user()->role === 'vendor') {

            // log the status
            logger()->info('Authorization Granted! | Validating request shortly');

            return true;
        }

        // log the error
        logger()->alert('un-authorized to proceed further | Terminating further process');

        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {

        // log the action..
        logger()->info("[app\Http\Requests\Vendor\StoreDiscountRequest@rules] Validation Initiated");

        return [
            'name' => ['required', 'string', 'max:255'],

            'discount_type' => ['required', 'in:percentage,fixed_amount'],

            // Value must be a positive number. If percentage, ideally between 1 and 100.
            'discount_value' => ['required', 'numeric', 'min:1'],

            'target_type' => ['required', 'in:product,sub_category'],

            // 🚀 The Magic Rules: Only require the ID based on what they selected!
            'product_id' => ['required_if:target_type,product', 'nullable', 'exists:products,id'],
            'sub_category_id' => ['required_if:target_type,sub_category', 'nullable', 'exists:sub_categories,id'],

            // Timer Rules: Start date must be valid, End date must be AFTER start date
            'starts_at' => ['required', 'date', 'after_or_equal:today'],
            'ends_at' => ['required', 'date', 'after:starts_at'],
        ];
    }
}
