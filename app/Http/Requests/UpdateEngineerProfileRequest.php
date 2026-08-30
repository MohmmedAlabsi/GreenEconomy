<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEngineerProfileRequest extends FormRequest
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
            'bio'                   => 'nullable|string',
            'cv_file'               => 'nullable|string|max:255',
        ];
    }
}