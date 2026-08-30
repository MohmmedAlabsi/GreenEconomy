<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRegionRequest extends FormRequest
{
    public function authorize() { return true; }

    public function rules()
    {
        return [
            'name' => 'required|string|max:150', //[cite: 28]
        ];
    }
}