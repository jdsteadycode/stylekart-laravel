<?php

// fix: updated folder path
namespace Modules\Product\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductColorRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // get models from route..
        $product = $this->route('product');
        $color = $this->route('color');


        // log the action
        logger()->info('[Modules\Product\app\Http\Requests\ProductColorRequest@authorization] Authorization check');

        // if not vendor's product?
        if ($this->user()->id !== $product->vendor_id) {
            // log the action
            logger()->alert('Product doesnot belong to vendor! Terminated Request.');
            return false;
        }

        // if, color is not related to product?
        if ($color && $color->product_id !== $product->id) {
            // log the action
            logger()->alert("Mismatched Product/Color relationship for Product: {$product->id} and Color: {$color->id}");
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
        // log the action
        logger()->info('[Modules\Product\app\Http\Requests\ProductColorRequest@rules] Validation initiated');

        // current route name!
        $routeName = $this->route()->getName();

        // when color actions!
        if (in_array($routeName, ['module.vendor.products.colors.store', 'module.vendor.products.colors.update'])) {
            return [
                "name" => "required|string|max:50",
            ];
        }

        // for multiple images store action
        if ($routeName === 'module.vendor.products.colors.images.store') {
            return [
                "images"   => "required|array",
                "images.*" => "image|mimes:jpg,jpeg,png,webp,avif|max:10240",
            ];
        }

        // for single image update action
        if ($routeName === 'module.vendor.products.colors.images.update') {
            return [
                "image" => "required|image|mimes:jpg,jpeg,png,webp,avif|max:5120",
            ];
        }

        return [];
    }

    /**
     * error messages
     */
    public function messages(): array
    {
        return [
            "name.required" => "Color name is required.",
            "images.required" => "No images selected!",
            "images.*.image" => "Each file must be a valid image.",
            "images.*.max"   => "Images cannot exceed 10MB.",
        ];
    }
}
