<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSpecializationRequest extends FormRequest
{
    public function authorize() { return true; }

    public function rules()
    {
        return [
            'role_id' => 'required|exists:roles,id', //
            'name'    => 'required|string|max:150', //[cite: 30]
        ];
    }
}