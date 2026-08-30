<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    public function authorize() { return true; }

    public function rules()
    {
        $userId = $this->route('id') ?? $this->route('user');
        return [
            'name'              => 'sometimes|string|max:255', //[cite: 31]
            'phone'             => 'nullable|string|max:20|unique:users,phone,' . $userId, //[cite: 31]
            'email'             => 'nullable|email|max:255|unique:users,email,' . $userId, //[cite: 31]
            'password'          => 'nullable|string|min:8', //[cite: 31]
            'avatar'            => 'nullable|string|max:255', //[cite: 31]
            'district'          => 'nullable|string|max:100', //[cite: 31]
            'membership_tier'   => 'nullable|string|max:50', //[cite: 31]
            'status'            => 'nullable|string|max:50', //[cite: 31]
            'identity_verified' => 'nullable|boolean', //[cite: 31]
            'role_id'           => 'nullable|exists:roles,id', //[cite: 31]
            'region_id'         => 'nullable|exists:regions,id', //[cite: 31]
        ];
    }
}