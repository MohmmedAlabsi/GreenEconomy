<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateKnowledgeBaseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize()
    {
        return true; // السماح بمرور الطلب
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules()
    {
        return [
            'title'           => 'sometimes|string|max:255',
            'summary'         => 'nullable|string',
            'content'         => 'nullable|string',
            'type'            => 'sometimes|string|max:50',
            'status'          => 'nullable|string|max:50',
            'category_id'     => 'sometimes|exists:categories,id',
            'media_url'       => 'nullable|string|max:255',
            'file_size_bytes' => 'nullable|integer',
        ];
    }
}