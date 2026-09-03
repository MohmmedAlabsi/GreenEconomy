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
            'user_id'             => 'nullable|exists:users,id',
            'specialization_id'   => 'nullable|exists:specializations,id',
            'years_of_experience' => 'nullable|integer|min:0',
            'qualification'       => 'nullable|string|max:255',
            'bio'                 => 'nullable|string',
            'avatar'              => 'nullable|image|max:5120',
            'cv_file'             => 'nullable|file|mimes:pdf|max:10240', 
        ];
    }
}