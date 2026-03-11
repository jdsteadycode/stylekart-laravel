<?php

namespace App\Http\Requests\Vendor;


use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductVariantRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // get models.
        $product = $this->route('product');
        $variant = $this->route('variant');

        // if not authorized vendor?
        if ($this->user()->role !== 'vendor') return false;

        // product doesn't belong to vendor?
        if ($product->vendor_id !== $this->user()->id) return false;

        // when updating, variant doesn't belong to product
        if ($variant && $variant->product_id !== $product->id) return false;

        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // get variant id from variant model
        $variantId = $this->route('variant')?->id;

        return [
            "size"     => "required|string|max:100",
            "color_id" => "required|exists:product_colors,id",
            "price"    => "required|numeric|min:0",
            "stock"    => "required|integer|min:0",
            "sku"      => [
                "nullable",
                "string",
                "max:100",
                Rule::unique('product_variants', 'sku')->ignore($variantId) // ignore if not variant-id
            ],
        ];
    }

    /**
     * error messages
     */
    public function messages(): array
    {
        return [
            "color_id.required" => "Please select a color for this variant.",
            "color_id.exists"   => "The selected color is invalid.",
            "price.min"         => "Price cannot be a negative value.",
        ];
    }
}
