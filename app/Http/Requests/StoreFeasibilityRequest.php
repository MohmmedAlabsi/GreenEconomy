<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreFeasibilityRequest extends FormRequest
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
            'title'              => 'required|string|max:255',
            'description'        => 'nullable|string',
            'category_id'        => 'required|exists:categories,id',
            'region_id'          => 'nullable|exists:regions,id',
            'estimated_budget'   => 'nullable|numeric|min:0',
            'expected_roi'       => 'nullable|numeric',
            'crop_type'          => 'nullable|string|max:100',
            'land_area'          => 'nullable|numeric|min:0',
            'attachment_ids'     => 'nullable|array',
            'attachment_ids.*'   => 'exists:attachments,id',
        ];
    }

    /**
     * Custom messages for validation errors.
     */
    public function messages(): array
    {
        return [
            'title.required'       => 'عنوان دراسة الجدوى مطلوب.',
            'category_id.required' => 'يرجى تحديد القسم الزراعي المناسب.',
            'category_id.exists'   => 'القسم المحدد غير موجود.',
            'region_id.exists'     => 'المنطقة المحددة غير موجودة.',
            'estimated_budget.numeric' => 'الميزانية التقديرية يجب أن تكون رقماً.',
        ];
    }
}
