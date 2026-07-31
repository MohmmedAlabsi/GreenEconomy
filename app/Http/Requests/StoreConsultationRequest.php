<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreConsultationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        return [
                    'title'       => 'required|string|max:255',
                    'crop_type'   => 'required|string',
                    'description' => 'required|string|min:15',
                    'images'      => 'nullable|array|max:4',
                    'images.*'    => 'image|mimes:jpeg,png,jpg,webp|max:3072', // تحقق من صيغة وحجم الصور[span_3](start_span)[span_3](end_span)
                ];
    }

    public function messages(): array
    {
        return [
            'title.required'       => 'عنوان الطلب أو الاستشارة مطلوب.',
            'crop_type.required'   => 'يرجى تحديد نوع المحصول الزراعي.',
            'description.required' => 'يرجى كتابة شرح مفصل للمشكلة الزراعية.',
            'description.min'      => 'الوصف يجب أن لا يقل عن 15 حرفاً.',
            'images.*.image'       => 'يجب أن تكون المرفقات صوراً فقط.',
            'images.*.max'         => 'حجم الصورة الواحدة يجب ألا يتجاوز 3 ميجابايت.',
        ];
    }
}
