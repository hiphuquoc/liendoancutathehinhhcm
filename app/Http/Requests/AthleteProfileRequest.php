<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AthleteProfileRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'phone' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'description' => 'nullable|string|max:2000',
        ];
    }

    public function messages()
    {
        return [
            'phone.max' => 'Số điện thoại không được vượt quá 255 ký tự!',
            'email.email' => 'Email không hợp lệ!',
            'email.max' => 'Email không được vượt quá 255 ký tự!',
            'description.max' => 'Giới thiệu ngắn không được vượt quá 2000 ký tự!',
        ];
    }
}
