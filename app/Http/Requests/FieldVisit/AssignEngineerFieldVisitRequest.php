<?php

namespace App\Http\Requests\FieldVisit;
use Illuminate\Foundation\Http\FormRequest;

class AssignEngineerFieldVisitRequest extends FormRequest
{
    public function authorize() { return true; }

    public function rules()
    {
        return [
            'engineer_id' => 'required|exists:users,id',
        ];
    }
}