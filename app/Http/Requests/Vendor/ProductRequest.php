<?php

namespace App\Http\Requests\Vendor;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // log the status
        logger()->alert('[app\Http\Requests\Vendor\ProductRequest@authorize] Authorization initiated');

        // get model
        $product = $this->route('product');

        // check if vendor?
        if ($this->user()->role !== 'vendor') return false;

        // during update / delete, check if vendor owns product?
        if ($product && $product->vendor_id !== $this->user()->id) {
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
        logger()->info('[app\Http\Requests\Vendor\ProductRequest@rules] Validation initiated');
        return [
            "name"            => "required|string|max:255",
            "description"     => "nullable|string",
            "category_id"     => "required",
            "sub_category_id" => "required|exists:sub_categories,id",
            "base_price"      => "required|numeric|min:0",
            'brand_id' => 'nullable|exists:brands,id'
        ];
    }
}
