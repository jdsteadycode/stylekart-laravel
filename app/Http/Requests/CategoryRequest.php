<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CategoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {

        // log the status
        logger()->info('[app\Http\Requests\CategoryRequest@authorize] Check Category Request Authorization!');

        // check if authorized?
        if ($this->user()->role !== 'admin') {
            // log the status
            logger()->alert('Not authorized! Terminating request!');

            return false;
        }

        // otherwise, allow!
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // check if during edit category id is given.
        $categoryId = $this->route('category')?->id ? $this->route('category')->id : null;

        // validate the data..
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('categories', 'name')->ignore($categoryId), // ignore rule when category id is null
            ],
        ];
    }

    /**
     * messages for validation errors..
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Category name is needed!',
            'name.string' => 'Category must be strictly text',
            'name.max' => 'Category must be within 255 characters',
        ];
    }
}
