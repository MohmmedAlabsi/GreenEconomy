<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFeasibilityRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'user_id' => 'required|exists:users,id',
            'project_title' => 'required|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'region_id' => 'nullable|exists:regions,id',
            'estimated_budget' => 'nullable|numeric',
            'land_area' => 'nullable|numeric',
            'description' => 'nullable|string',
            'status' => 'nullable|string|max:50',
        ];
    }
}