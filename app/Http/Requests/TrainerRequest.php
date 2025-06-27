<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;

class TrainerRequest extends FormRequest {
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
            'title'                     => 'required',
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
                                        ->join('trainer_info', 'trainer_info.seo_id', '=', 'seo.id')
                                        ->select('seo.slug', 'trainer_info.id')
                                        ->where('slug', $slug)
                                        ->first();
                        if(!empty($dataCheck)){
                            if(empty(request('trainer_info_id'))){
                                $flag = true;
                            }else {
                                if(request('trainer_info_id')!=$dataCheck->id) $flag = true;
                            }
                        }
                        if($flag==true) $fail('Dường dẫn tĩnh đã trùng với một trang khác trên hệ thống!');
                    }
                }
            ],
            'rating_aggregate_count'    => 'required',
            'rating_aggregate_star'     => 'required'
        ];
    }

    public function messages() {
        return [
            'title.required'            => 'Họ và tên & Chức vụ không được để trống!',
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
