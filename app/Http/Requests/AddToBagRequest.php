<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\InStock;

class AddToBagRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // log the action..
        logger()->info('[app/Http/Requests/AddToBagRequest@authorize] Check if customer is allowed to add to bag!');

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
        logger()->info('[app/Http/Requests/AddToBagRequest@rules] Cart data validation initiated');

        // apply rules.. (constraints)
        return [
            // 'bail' -> to ensure if one rule fails return error and rest won't run unecessarily.
            'variant_id' => ['bail', 'required', 'integer', 'exists:product_variants,id', new InStock()],
            'qty' => ['required', 'integer', 'min:1', 'max:5']
        ];
    }
}
