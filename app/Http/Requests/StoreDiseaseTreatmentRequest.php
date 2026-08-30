<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDiseaseTreatmentRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'disease_id' => 'required|exists:plant_diseases,id',
            'treatment_type' => 'required|string|max:50',
            'title' => 'required|string|max:255',
            'instructions' => 'required|string',
        ];
    }
}