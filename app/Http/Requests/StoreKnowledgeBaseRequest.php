<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreKnowledgeBaseRequest extends FormRequest
{
    public function authorize() { return true; }

    public function rules()
    {
        return [
            'title'           => 'required|string|max:255',
            'summary'         => 'nullable|string',
            'content'         => 'nullable|string',
            'type'            => 'required|string|max:50',
            'status'          => 'nullable|string|max:50',
            'category_id'     => 'required|exists:categories,id',
            'media_url'       => 'nullable|string|max:255',
            'file_size_bytes' => 'nullable|integer',
            'view_count'      => '0',
        ];
    }
}