<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFieldVisitRequest extends FormRequest
{
    public function authorize() { return true; }

    public function rules()
    {
        return [
            'user_id'             => 'required|exists:users,id',
            'contact_name'        => 'required|string|max:255',
            'contact_phone'       => 'required|string|max:20',
            'governorate'         => 'required|string|max:100',
            'district'            => 'required|string|max:100',
            'village_or_area'     => 'required|string|max:255',
            'nearest_landmark'    => 'nullable|string|max:255',
            'crop_type'           => 'required|string|max:100',
            'area_size'           => 'required|numeric',
            'infestation_type'    => 'required|string|max:150',
            'priority_level'      => 'required|string|max:50',
            'problem_description' => 'required|string',
            'status'              => 'nullable|string|max:50',
            'scheduled_at'        => 'nullable|date',
            'estimated_cost'      => 'nullable|numeric',
        ];
    }
}