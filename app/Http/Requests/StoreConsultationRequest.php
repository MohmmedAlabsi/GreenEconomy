<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreConsultationRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'user_id' => 'required|exists:users,id',
            'issue_title' => 'required|string|max:255',
            'crop_type' => 'required|string|max:100',
            'crop_age' => 'required|string|max:100',
            'issue_duration' => 'required|string|max:100',
            'description' => 'required|string',
            'status' => 'nullable|string|max:50',
            'assigned_expert_id' => 'nullable|exists:users,id',
        ];
    }
}