<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFieldVisitReportRequest extends FormRequest
{
    public function authorize() { return true; }

    protected function prepareForValidation()
    {
        if ($this->route('id')) {
            $this->merge(['field_visit_id' => $this->route('id')]);
        }
    }

    public function rules()
    {
        return [
            'field_visit_id'    => 'required|exists:field_visits,id',
            'diagnosis'         => 'required|string',
            'recommendations'   => 'required|string',
            'prescribed_inputs' => 'nullable|string',
            'notes'             => 'nullable|string',
            'attachment'        => 'nullable|file|max:10240',
        ];
    }
}