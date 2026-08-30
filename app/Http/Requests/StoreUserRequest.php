<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    public function authorize() { return true; }

    public function rules()
    {
        return [
            'name'              => 'required|string|max:255', //
            'phone'             => 'nullable|string|max:20|unique:users,phone', //[cite: 31]
            'email'             => 'nullable|email|max:255|unique:users,email', //[cite: 31]
            'password'          => 'required|string|min:8', //[cite: 31]
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