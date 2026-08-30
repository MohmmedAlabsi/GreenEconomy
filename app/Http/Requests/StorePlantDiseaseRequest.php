<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePlantDiseaseRequest extends FormRequest
{
    public function authorize() { return true; }

    public function rules()
    {
        return [
            'name'              => 'required|string|max:255',
            'scientific_name'   => 'nullable|string|max:255',
            'plant_type'        => 'nullable|string|max:255',
            'plant_ids'         => 'nullable|array',               
            'plant_ids.*'       => 'integer|exists:plants,id',
            'type'              => 'required|string|max:100',
            'severity_level'    => 'nullable|string|max:50',
            'spread_rate'       => 'nullable|string|max:50',
            'farmer_visibility' => 'nullable|string|max:100',
            'symptoms'          => 'required|string',
            'cause_description' => 'nullable|string',
            'image'             => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
        ];
    }
}