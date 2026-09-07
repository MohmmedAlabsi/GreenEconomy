<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFeasibilityStudyRequest extends FormRequest
{
    public function authorize() 
    { 
        return true; 
    }

    public function rules()
    {
        return [
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'nullable|exists:categories,id',
            'region_id' => 'nullable|exists:regions,id',
            'cover_image' => 'nullable|file|image|mimes:jpeg,png,jpg,webp|max:5120',
            'image' => 'nullable|file|image|mimes:jpeg,png,jpg,webp|max:5120',
            'capital_required' => 'nullable|numeric',
            'expected_roi' => 'sometimes|required|numeric',
            'payback_period' => 'nullable|integer',
            'risk_level' => 'nullable|string|max:50',
            'status' => 'nullable|string|max:50',
            'pdf_file' => 'nullable|file|mimes:pdf|max:10240',
            'file' => 'nullable|file|mimes:pdf|max:10240',
            'user_id' => 'nullable|exists:users,id',
        ];
    }
}