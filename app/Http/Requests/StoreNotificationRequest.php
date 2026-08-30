<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\User;

class StoreNotificationRequest extends FormRequest
{
    public function authorize() { return true; }

    protected function prepareForValidation()
    {
        // نقل المنطق الذي كان في المتحكم لمعالجة البريد الإلكتروني إلى هنا
        if ($this->audience === 'specific' && !is_numeric($this->user_id)) {
            $user = User::where('email', $this->user_id)->first();
            
            if ($user) {
                $this->merge(['user_id' => $user->id]);
            } else {
                // إذا لم يكن موجوداً، ندمج قيمة خاطئة ليفشل التحقق في الـ Rules
                $this->merge(['user_id' => 'invalid_email']); 
            }
        }
    }

    public function rules()
    {
        return [
            'audience' => 'required|string',
            'title'    => 'required|string|max:255',
            'body'     => 'required|string',
            'priority' => 'nullable|string',
            'user_id'  => 'required_if:audience,specific|integer', // تأكيد أنه integer
        ];
    }

    public function messages()
    {
        return [
            'user_id.integer' => 'البريد الإلكتروني المدخل غير مسجل في النظام أو المعرف غير صالح.'
        ];
    }
}