<?php

namespace App\Http\Requests;

use App\Helpers\SpokenLanguage;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TrainerProfileRequest extends FormRequest {
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules() {
        return [
            'phone'          => 'nullable|string|max:255',
            'email'          => 'nullable|email|max:255',
            'description'   => 'nullable|string|max:2000',
            'area'           => 'nullable|string|max:255',
            'years_experience' => 'nullable|integer|min:0|max:100',
            'languages'      => 'nullable|array',
            'languages.*'    => ['string', Rule::in(SpokenLanguage::allowedValues())],
        ];
    }

    public function messages() {
        return [
            'phone.max'         => 'Số điện thoại không được vượt quá 255 ký tự!',
            'email.email'       => 'Email không hợp lệ!',
            'email.max'         => 'Email không được vượt quá 255 ký tự!',
            'description.max'   => 'Giới thiệu ngắn không được vượt quá 2000 ký tự!',
            'area.max'          => 'Khu vực không được vượt quá 255 ký tự!',
            'years_experience.integer' => 'Số năm kinh nghiệm phải là số nguyên!',
            'years_experience.min' => 'Số năm kinh nghiệm không hợp lệ!',
            'years_experience.max' => 'Số năm kinh nghiệm không hợp lệ!',
            'languages.array'   => 'Ngôn ngữ không hợp lệ!',
            'languages.*.in'    => 'Ngôn ngữ được chọn không hợp lệ!',
        ];
    }
}
