<?php

namespace App\Http\Requests\Vendor;

use Illuminate\Foundation\Http\FormRequest;

class VendorProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // log the status
        logger()->info('[app\Http\Requests\Vendor\VendorProfileRequest@authorize] Authorization initiated.');

        // check if not authorized vendor?
        if ($this->user()->role !== 'vendor') {
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
        // log the status
        logger()->info('[app\Http\Requests\Vendor\VendorProfileRequest@rules] validation initiated.');

        // validate
        return [
            "name" => "required|string|max:255",
            "shop_name" => "required|string|max:255",
            "shop_address" => "required|string",
        ];
    }

    /**
     * messages for error
     */
    public function messages(): array
    {
        // custom error messages
        return [
            "name.required" => "Name is required.",

            "shop_name.required" => "Shop Name is required.",
            "shop_name.string" => "Shop name must be valid text.",

            "shop_address.required" => "Shop address is required.",
            "shop_address.string" => "Shop address must be valid text.",
        ];
    }
}
