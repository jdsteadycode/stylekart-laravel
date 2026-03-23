<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class SubcategoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // log the status
        logger()->info('[app\Http\Requests\SubcategoryRequest@authorize] Check Authorization!');

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
        // log the action
        logger()->info('[app\Http\Requests\SubCategoryRequest@rules] Validation initiated!');

        // sub-category rules..
        $rules = [
            'name' => 'bail|required|string|max:255',
        ];

        // if method is post?
        if ($this->isMethod('post')) {
            // attach category check also..
            $rules['category_id'] = 'bail|required|exists:categories,id';
        }

        return $rules;
    }

    /**
     * messages for validation errors..
     */
    public function messages(): array
    {
        return [
            // messages for error..
            'category_id.required' => 'Please select a parent category.',
            'name.required' => 'Enter a sub category name.',
        ];
    }
}
