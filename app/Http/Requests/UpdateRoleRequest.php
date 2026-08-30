<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRoleRequest extends FormRequest
{
    public function authorize() { return true; }

    public function rules()
    {
        $roleId = $this->route('id') ?? $this->route('role');
        return [
            'name' => 'required|string|max:255|unique:roles,name,' . $roleId, //[cite: 29]
        ];
    }
}