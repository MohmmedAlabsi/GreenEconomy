<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubmitRatingFieldVisitRequest extends FormRequest
{
    public function authorize() { return true; }

    public function rules()
    {
        return [
            'rating'         => 'required|integer|min:1|max:5',
            'rating_comment' => 'nullable|string',
        ];
    }
}