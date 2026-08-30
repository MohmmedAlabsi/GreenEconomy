<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RejectEngineerJoinRequest extends FormRequest
{
    public function authorize()
    {
        return true; // يمكن إضافة تحقق من الصلاحيات هنا (مثلاً، إذا كان المستخدم مسؤولاً)
    }

    public function rules()
    {
        return [
            'notes' => 'required|string|max:500' // كما هو موجود في الكود الأصلي[cite: 2]
        ];
    }
}