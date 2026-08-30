<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubmitEstimateFieldVisitRequest extends FormRequest
{
    public function authorize() { return true; }

    public function rules()
    {
        return [
            'estimated_cost' => 'required|numeric|min:0',
            'scheduled_at'   => 'required|date',
        ];
    }
}