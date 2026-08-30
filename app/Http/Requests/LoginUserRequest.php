<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginUserRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'email'    => 'required|email', // كما هو في الكود الأصلي[cite: 4]
            'password' => 'required',
        ];
    }
}