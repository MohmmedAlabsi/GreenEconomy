<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFieldVisitRequest extends FormRequest
{
    public function authorize() { return true; }

    public function rules()
    {
        return [
            'user_id'             => 'sometimes|exists:users,id',
            'engineer_id'         => 'nullable|exists:users,id',
            'contact_name'        => 'sometimes|string|max:255',
            'contact_phone'       => 'sometimes|string|max:20',
            'governorate'         => 'sometimes|string|max:100',
            'district'            => 'sometimes|string|max:100',
            'village_or_area'     => 'sometimes|string|max:255',
            'nearest_landmark'    => 'nullable|string|max:255',
            'crop_type'           => 'sometimes|string|max:100',
            'area_size'           => 'sometimes|numeric',
            'infestation_type'    => 'sometimes|string|max:150',
            'priority_level'      => 'sometimes|string|max:50',
            'problem_description' => 'sometimes|string',
            'status'              => 'nullable|string|max:50',
            'current_step'        => 'nullable|integer|min:1|max:9',
            'scheduled_at'        => 'nullable|date',
            'estimated_cost'      => 'nullable|numeric',
            'rating'              => 'nullable|integer|min:1|max:5',
            'rating_comment'      => 'nullable|string',
        ];
    }
}