<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterUserRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name'                => 'required|string|max:255', // كما هو في الكود الأصلي[cite: 4]
            'email'               => 'required|string|email|max:255|unique:users',
            'password'            => 'required|string|min:8|confirmed',
            'phone'               => 'nullable|string|max:20',
            'role_id'             => 'required|exists:roles,id',
            'region_id'           => 'required|exists:regions,id',
            'district'            => 'required|string|max:255',
            'specialization_id'   => 'required_if:role_id,3|nullable|exists:specializations,id',
            'qualification'       => 'required_if:role_id,3|nullable|string|max:255',
            'years_of_experience' => 'required_if:role_id,3|nullable|integer|min:0',
            'cv_file'             => 'nullable|file|mimes:pdf|max:5120',
        ];
    }
}