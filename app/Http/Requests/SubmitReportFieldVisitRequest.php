<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubmitReportFieldVisitRequest extends FormRequest
{
    public function authorize() { return true; }

    public function rules()
    {
        return [
            'diagnosis'         => 'required|string',
            'recommendations'   => 'required|string',
            'prescribed_inputs' => 'nullable|string',
            'notes'             => 'nullable|string',
        ];
    }
}