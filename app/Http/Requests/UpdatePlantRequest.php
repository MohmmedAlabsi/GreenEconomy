<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePlantRequest extends FormRequest
{
    public function authorize() { return true; }

    public function rules()
    {
        return [
            'common_name'         => 'sometimes|string|max:255',
            'scientific_name'     => 'nullable|string|max:255',
            'description'         => 'nullable|string',
            'climate_requirements'=> 'nullable|string',
            'irrigation_schedule' => 'nullable|string',
            'planting_season'     => 'nullable|string|max:100',
            'image_url'           => 'nullable|string|max:255',
        ];
    }
}