<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRegionRequest extends FormRequest
{
    public function authorize() { return true; }

    public function rules()
    {
        return [
            'name' => 'required|string|max:150', //
        ];
    }
}