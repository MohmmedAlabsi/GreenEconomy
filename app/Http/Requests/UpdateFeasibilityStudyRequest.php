<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFeasibilityStudyRequest extends FormRequest
{
    public function authorize() { return true; }

    public function rules()
    {
        return [
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'nullable|exists:categories,id',
            'region_id' => 'nullable|exists:regions,id',
            'cover_image' => 'nullable|string|max:255',
            'capital_required' => 'nullable|numeric',
            'expected_roi' => 'sometimes|numeric',
            'payback_period' => 'nullable|integer',
            'risk_level' => 'nullable|string|max:50',
            'status' => 'nullable|string|max:50',
            'pdf_file' => 'nullable|string|max:255',
            'user_id' => 'sometimes|exists:users,id',
        ];
    }
}