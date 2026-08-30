<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCategoryRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        // استخراج المعرف من الرابط (Route Parameter)
        $categoryId = $this->route('category') ?? $this->route('id');

        return [
            'name' => 'sometimes|string|max:255',
            'slug' => 'sometimes|string|max:255|unique:categories,slug,' . $categoryId,
            'type' => 'sometimes|string|max:50',
        ];
    }
}