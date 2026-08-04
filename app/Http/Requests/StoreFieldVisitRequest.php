<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreFieldVisitRequest extends FormRequest
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
            'consultant_id'    => 'nullable|exists:users,id',
            'region_id'        => 'required|exists:regions,id',
            'visit_date'       => 'required|date|after_or_equal:today',
            'location_address' => 'required|string|max:550',
            'latitude'         => 'nullable|numeric|between:-90,90',
            'longitude'        => 'nullable|numeric|between:-180,180',
            'purpose'          => 'required|string|min:10',
            'notes'            => 'nullable|string',
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
            'region_id.required'     => 'تحديد المنطقة مطلوب لتنظيم الزيارة.',
            'visit_date.required'    => 'تاريخ الزيارة مطلوب.',
            'visit_date.after_or_equal' => 'تاريخ الزيارة يجب أن يكون اليوم أو في المستقبل.',
            'location_address.required' => 'يرجى إدخال عنوان أو موقع المزرعة بشكل واضح.',
            'purpose.required'       => 'يرجى تبيان الغرض من الزيارة الميدانية.',
            'purpose.min'            => 'شرح الغرض من الزيارة يجب أن لا يقل عن 10 أحرف.',
        ];
    }
}
