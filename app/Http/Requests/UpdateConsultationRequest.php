<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateConsultationRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'user_id' => 'sometimes|exists:users,id',
            'issue_title' => 'sometimes|string|max:255',
            'crop_type' => 'sometimes|string|max:100',
            'crop_age' => 'sometimes|string|max:100',
            'issue_duration' => 'sometimes|string|max:100',
            'description' => 'sometimes|string',
            'status' => 'nullable|string|max:50',
            'assigned_expert_id' => 'nullable|exists:users,id',
        ];
    }
}