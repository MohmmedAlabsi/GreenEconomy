<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDiseaseTreatmentRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'disease_id' => 'sometimes|exists:plant_diseases,id',
            'treatment_type' => 'sometimes|string|max:50',
            'title' => 'sometimes|string|max:255',
            'instructions' => 'sometimes|string',
        ];
    }
}