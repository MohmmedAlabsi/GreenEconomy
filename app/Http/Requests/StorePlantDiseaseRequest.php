<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StorePlantDiseaseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        return [
            'plant_id'         => 'required|exists:plants,id',
            'name'             => 'required|string|max:255',
            'scientific_name'  => 'nullable|string|max:255',
            'symptoms'         => 'required|string',
            'cause'            => 'nullable|string',
            'prevention'       => 'nullable|string',
            'severity_level'   => 'nullable|in:low,medium,high,critical',
            'attachment_ids'   => 'nullable|array',
            'attachment_ids.*' => 'exists:attachments,id',
        ];
    }

    /**
     * Custom messages for validation errors.
     */
    public function messages(): array
    {
        return [
            'plant_id.required'  => 'يرجى تحديد النبات المصاب.',
            'plant_id.exists'    => 'النبات المحدد غير مدرج في النظام.',
            'name.required'      => 'اسم المرض النباتي مطلوب.',
            'symptoms.required'  => 'يرجى كتابة أراض المرض المعاينة.',
            'severity_level.in'  => 'مستوى الخطورة يجب أن يكون أحد الخيارات التالية: low, medium, high, critical.',
        ];
    }
}
