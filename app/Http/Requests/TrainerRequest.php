<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\DB;
use App\Helpers\SpokenLanguage;
use Illuminate\Validation\Rule;

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
        // Ensure repeater fields are always present (even if empty)
        $this->merge([
            'repeater_trainer_achievement' => $this->input('repeater_trainer_achievement', []),
            'repeater_trainer_skill' => $this->input('repeater_trainer_skill', []),
            'repeater_trainer_experience' => $this->input('repeater_trainer_experience', []),
            'repeater_trainer_degree' => $this->input('repeater_trainer_degree', []),
        ]);
        
        return [
            'name'                          => 'required',
            'position'                      => 'nullable',
            'phone'                         => 'nullable',
            'email'                         => 'required',
            'description'                  => 'nullable|string|max:2000',
            'ordering'                      => 'min:0',
            'seo_title'                     => 'required',
            'seo_description'               => 'required',
            'slug'                          => [
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
            'rating_aggregate_count'        => 'required',
            'rating_aggregate_star'         => 'required',
            'area'                          => 'nullable|string|max:255',
            'years_experience'              => 'nullable|integer|min:0|max:100',
            'languages'                     => 'nullable|array',
            'languages.*'                   => ['string', Rule::in(SpokenLanguage::allowedValues())],
            // Validate repeater fields - at least 1 valid record required
            'repeater_trainer_achievement' => [
                function($attribute, $value, $fail) {
                    $validCount = 0;
                    $value = $value ?? [];
                    if (is_array($value) && count($value) > 0) {
                        foreach ($value as $item) {
                            if (!empty($item['content']) && trim($item['content']) !== '') {
                                $validCount++;
                            }
                        }
                    }
                    if ($validCount < 1) {
                        $fail('Vui lòng nhập ít nhất một thành tích hợp lệ (có nội dung thành tích).');
                    }
                }
            ],
            'repeater_trainer_achievement.*.content' => 'required_with:repeater_trainer_achievement',
            'repeater_trainer_skill' => [
                function($attribute, $value, $fail) {
                    $validCount = 0;
                    $value = $value ?? [];
                    if (is_array($value) && count($value) > 0) {
                        foreach ($value as $item) {
                            if (!empty($item['skill']) && trim($item['skill']) !== '' 
                                && !empty($item['percent']) && trim($item['percent']) !== '') {
                                $validCount++;
                            }
                        }
                    }
                    if ($validCount < 1) {
                        $fail('Vui lòng nhập ít nhất một kỹ năng hợp lệ (có tên kỹ năng và phần trăm).');
                    }
                }
            ],
            'repeater_trainer_skill.*.skill' => 'required_with:repeater_trainer_skill',
            'repeater_trainer_skill.*.percent' => 'required_with:repeater_trainer_skill',
            'repeater_trainer_experience' => [
                function($attribute, $value, $fail) {
                    $validCount = 0;
                    $value = $value ?? [];
                    if (is_array($value) && count($value) > 0) {
                        foreach ($value as $item) {
                            if (!empty($item['title']) && trim($item['title']) !== '' 
                                && !empty($item['company']) && trim($item['company']) !== ''
                                && !empty($item['content']) && trim($item['content']) !== '') {
                                $validCount++;
                            }
                        }
                    }
                    if ($validCount < 1) {
                        $fail('Vui lòng nhập ít nhất một kinh nghiệm hợp lệ (có chức vụ, đơn vị và kỹ năng).');
                    }
                }
            ],
            'repeater_trainer_experience.*.title' => 'required_with:repeater_trainer_experience',
            'repeater_trainer_experience.*.company' => 'required_with:repeater_trainer_experience',
            'repeater_trainer_experience.*.content' => 'required_with:repeater_trainer_experience',
            'repeater_trainer_degree' => [
                function($attribute, $value, $fail) {
                    $validCount = 0;
                    $value = $value ?? [];
                    if (is_array($value) && count($value) > 0) {
                        foreach ($value as $item) {
                            if (!empty($item['title']) && trim($item['title']) !== '' 
                                && !empty($item['school']) && trim($item['school']) !== ''
                                && !empty($item['content']) && trim($item['content']) !== '') {
                                $validCount++;
                            }
                        }
                    }
                    if ($validCount < 1) {
                        $fail('Vui lòng nhập ít nhất một bằng cấp hợp lệ (có tiêu đề, trường học và kỹ năng).');
                    }
                }
            ],
            'repeater_trainer_degree.*.title' => 'required_with:repeater_trainer_degree',
            'repeater_trainer_degree.*.school' => 'required_with:repeater_trainer_degree',
            'repeater_trainer_degree.*.content' => 'required_with:repeater_trainer_degree',
        ];
    }

    public function messages() {
        return [
            'name.required'             => 'Họ và tên không được để trống!',
            'email.required'            => 'Email không được để trống!',
            'seo_title.required'        => 'Tiêu đề SEO không được để trống!',
            'seo_description.required'  => 'Mô tả SEO không được để trống!',
            'slug.required'             => 'Đường dẫn tĩnh không được để trống!',
            'rating_aggregate_count'    => 'Số lượt đánh giá không được để trống!',
            'rating_aggregate_star'     => 'Điểm đánh giá không được để trống!',
            // Repeater validation messages
            'repeater_trainer_achievement.*.content.required_with' => 'Nội dung thành tích không được để trống.',
            'repeater_trainer_skill.*.skill.required_with' => 'Tên kỹ năng không được để trống.',
            'repeater_trainer_skill.*.percent.required_with' => 'Phần trăm kỹ năng không được để trống.',
            'repeater_trainer_experience.*.title.required_with' => 'Chức vụ không được để trống.',
            'repeater_trainer_experience.*.company.required_with' => 'Đơn vị không được để trống.',
            'repeater_trainer_experience.*.content.required_with' => 'Kỹ năng (kinh nghiệm) không được để trống.',
            'repeater_trainer_degree.*.title.required_with' => 'Tiêu đề bằng cấp không được để trống.',
            'repeater_trainer_degree.*.school.required_with' => 'Trường học không được để trống.',
            'repeater_trainer_degree.*.content.required_with' => 'Kỹ năng (bằng cấp) không được để trống.',
        ];
    }
}
