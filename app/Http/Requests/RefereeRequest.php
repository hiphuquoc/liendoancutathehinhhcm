<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;
use App\Helpers\SpokenLanguage;
use Illuminate\Validation\Rule;

class RefereeRequest extends FormRequest {
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
            'name'                      => 'required',
            'position'                  => 'nullable',
            'phone'                     => 'required',
            'email'                     => 'required',
            'ordering'                  => 'min:0',
            'seo_title'                 => 'required',
            'seo_description'           => 'required',
            'slug'                      => [
                'required',
                function($attribute, $value, $fail){
                    $slug           = !empty(request('slug')) ? request('slug') : null;
                    if(!empty($slug)){
                        $flag       = false;
                        $dataCheck  = DB::table('seo')
                                        ->join('referee_info', 'referee_info.seo_id', '=', 'seo.id')
                                        ->select('seo.slug', 'referee_info.id')
                                        ->where('slug', $slug)
                                        ->first();
                        if(!empty($dataCheck)){
                            if(empty(request('referee_info_id'))){
                                $flag = true;
                            }else {
                                if(request('referee_info_id')!=$dataCheck->id) $flag = true;
                            }
                        }
                        if($flag==true) $fail('Dường dẫn tĩnh đã trùng với một trang khác trên hệ thống!');
                    }
                }
            ],
            'rating_aggregate_count'    => 'required',
            'rating_aggregate_star'     => 'required',
            'area'                      => 'nullable|string|max:255',
            'years_experience'          => 'nullable|integer|min:0|max:100',
            'languages'                 => 'nullable|array',
            'languages.*'               => ['string', Rule::in(SpokenLanguage::allowedValues())],
        ];
    }

    public function messages() {
        return [
            'name.required'             => 'Họ và tên không được để trống!',
            'phone.required'            => 'Số điện thoại không được để trống!',
            'email.required'            => 'Email không được để trống!',
            'seo_title.required'        => 'Tiêu đề SEO không được để trống!',
            'seo_description.required'  => 'Mô tả SEO không được để trống!',
            'slug.required'             => 'Đường dẫn tĩnh không được để trống!',
            'rating_aggregate_count'    => 'Số lượt đánh giá không được để trống!',
            'rating_aggregate_star'     => 'Điểm đánh giá không được để trống!'
        ];
    }
}
