<?php

namespace App\Http\Requests\FieldVisit;
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
            'attachment'        => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240', // إلزامي وبحد أقصى 10MB
        ];
    }
}