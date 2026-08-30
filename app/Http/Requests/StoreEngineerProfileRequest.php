<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEngineerProfileRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'specialization_id'     => 'nullable|exists:specializations,id',
            'years_of_experience'   => 'nullable|integer|min:0',
            'qualification'         => 'nullable|string|max:255',
            'bio'                   => 'nullable|string',
            'cv_file'               => 'nullable|sometimes|file|mimes:pdf|max:5120', 
        ];
    }
}