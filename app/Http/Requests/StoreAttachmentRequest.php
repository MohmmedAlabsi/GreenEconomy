<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAttachmentRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'file' => 'required|file|mimes:jpeg,png,jpg,webp,pdf,doc,docx|max:10240', // كما هو في الكود الأصلي[cite: 3]
            'file_type' => 'nullable|string|max:50',
            'attachable_type' => 'nullable|string|max:255',
            'attachable_id' => 'nullable|integer',
        ];
    }

    // هنا نقوم بنقل الرسائل المخصصة
    public function messages()
    {
        return [
            'file.required' => 'يرجى اختيار ملف لرفعه.',
            'file.file' => 'المدخل يجب أن يكون ملفاً صحيحاً.',
            'file.mimes' => 'صيغ الملفات المدعومة هي: jpeg, png, jpg, webp, pdf, doc, docx.',
            'file.max' => 'حجم الملف يجب ألا يتجاوز 10 ميجابايت.',
        ]; // كما هو في الكود الأصلي[cite: 3]
    }
}