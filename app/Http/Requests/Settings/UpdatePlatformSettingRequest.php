<?php

namespace App\Http\Requests\Settings;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePlatformSettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'platform_name'    => 'required|string|max:191',
            'support_email'    => 'nullable|email|max:191',
            'support_phone'    => 'nullable|string|max:50',
            'maintenance_mode' => 'required|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'platform_name.required'   => 'اسم المنصة مطلوب',
            'support_email.email'      => 'يرجى إدخال بريد إلكتروني صالح للدعم الفني',
            'maintenance_mode.boolean' => 'قيمة وضع الصيانة غير صالحة',
        ];
    }
}