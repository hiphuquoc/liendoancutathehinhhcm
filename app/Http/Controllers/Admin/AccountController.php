<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Trainer;
use App\Http\Requests\SubAdminTrainerProfileRequest;
use App\Helpers\Upload;
use App\Models\Seo;
use App\Models\TrainerAchievement;
use App\Models\TrainerSkill;
use App\Models\TrainerExperience;
use App\Models\TrainerExperienceContent;
use App\Models\TrainerDegree;
use App\Models\TrainerDegreeContent;

class AccountController extends Controller
{
    /**
     * Hiển thị trang thông tin cá nhân
     */
    public function profile()
    {
        $user = Auth::user();
        // Get trainer code if user is sub-admin and has trainer profile
        $trainerCode = null;
        if ($user->hasRole('sub-admin') && !$user->hasRole('admin')) {
            $trainer = Trainer::where('user_id', $user->id)->first();
            if ($trainer && !empty($trainer->trainer_code)) {
                $trainerCode = $trainer->trainer_code;
            }
        }
        return view('admin.account.profile', compact('user', 'trainerCode'));
    }

    /**
     * Cập nhật thông tin cá nhân
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $isSubAdmin = $user->hasRole('sub-admin') && !$user->hasRole('admin');

        // Sub-admin cannot update name, but can update email
        if ($isSubAdmin) {
            // Validate that name hasn't changed, but allow email update
            $validator = Validator::make($request->all(), [
                'name' => [
                    'required',
                    function($attribute, $value, $fail) use ($user) {
                        if ($user->name !== $value) {
                            $fail('Bạn không có quyền thay đổi họ và tên.');
                        }
                    }
                ],
                'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            ], [
                'name.required' => 'Vui lòng nhập họ tên',
                'email.required' => 'Vui lòng nhập email',
                'email.email' => 'Email không hợp lệ',
                'email.unique' => 'Email này đã được sử dụng',
            ]);
        } else {
            // Admin can update name and email
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255|min:2',
                'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            ], [
                'name.required' => 'Vui lòng nhập họ tên',
                'name.min' => 'Họ tên phải có ít nhất 2 ký tự',
                'email.required' => 'Vui lòng nhập email',
                'email.email' => 'Email không hợp lệ',
                'email.unique' => 'Email này đã được sử dụng',
            ]);
        }

        if ($validator->fails()) {
            $message = [
                'type' => 'danger',
                'message' => '<strong>Lỗi!</strong> ' . $validator->errors()->first()
            ];
            $request->session()->put('message', $message);
            return redirect()->route('admin.account.profile');
        }

        try {
            DB::beginTransaction();
            
            // Update name (only for admin)
            if (!$isSubAdmin) {
                $user->name = trim($request->name);
            }
            // All users (admin and sub-admin) can update email
            $user->email = trim($request->email);
            // Sub-admin can update other fields like address, phone, position if needed
            if ($request->has('address')) {
                $user->address = trim($request->address);
            }
            if ($request->has('phone')) {
                $user->phone = trim($request->phone);
            }
            if ($request->has('position')) {
                $user->position = trim($request->position);
            }
            $user->save();
            
            // Sync to trainer_info if user has trainer profile
            $this->syncUserToTrainer($user);
            
            DB::commit();

            $message = [
                'type' => 'success',
                'message' => '<strong>Thành công!</strong> Đã cập nhật thông tin cá nhân!'
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error updating user profile', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            $message = [
                'type' => 'danger',
                'message' => '<strong>Lỗi!</strong> Có lỗi xảy ra: ' . $e->getMessage()
            ];
        }

        $request->session()->put('message', $message);
        return redirect()->route('admin.account.profile');
    }

    /**
     * Hiển thị trang đổi mật khẩu
     */
    public function changePassword()
    {
        return view('admin.account.changePassword');
    }

    /**
     * Cập nhật mật khẩu
     */
    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'password' => 'required|string|min:6|max:100|confirmed',
            'password_confirmation' => 'required|string|min:6',
        ], [
            'current_password.required' => 'Vui lòng nhập mật khẩu hiện tại',
            'password.required' => 'Vui lòng nhập mật khẩu mới',
            'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự',
            'password.confirmed' => 'Mật khẩu xác nhận không khớp',
            'password_confirmation.required' => 'Vui lòng xác nhận mật khẩu',
        ]);

        if ($validator->fails()) {
            $message = [
                'type' => 'danger',
                'message' => '<strong>Lỗi!</strong> ' . $validator->errors()->first()
            ];
            $request->session()->put('message', $message);
            return redirect()->route('admin.account.changePassword');
        }

        // Kiểm tra mật khẩu hiện tại
        if (!Hash::check($request->current_password, $user->password)) {
            $message = [
                'type' => 'danger',
                'message' => '<strong>Lỗi!</strong> Mật khẩu hiện tại không chính xác'
            ];
            $request->session()->put('message', $message);
            return redirect()->route('admin.account.changePassword');
        }

        try {
            $user->password = Hash::make($request->password);
            $user->save();

            $message = [
                'type' => 'success',
                'message' => '<strong>Thành công!</strong> Đã đổi mật khẩu thành công!'
            ];
        } catch (\Exception $e) {
            $message = [
                'type' => 'danger',
                'message' => '<strong>Lỗi!</strong> Có lỗi xảy ra, vui lòng thử lại'
            ];
        }

        $request->session()->put('message', $message);
        return redirect()->route('admin.account.changePassword');
    }

    /**
     * Hiển thị trang chỉnh sửa hồ sơ HLV của sub-admin
     */
    public function trainerProfile(Request $request)
    {
        $user = Auth::user();
        
        // Chỉ cho phép sub-admin (không phải admin)
        if ($user->hasRole('admin') || !$user->hasRole('sub-admin')) {
            $message = [
                'type' => 'danger',
                'message' => '<strong>Lỗi!</strong> Bạn không có quyền truy cập trang này.'
            ];
            $request->session()->put('message', $message);
            return redirect()->route('admin.account.profile');
        }
        
        // Tìm trainer có user_id = user hiện tại
        $trainer = Trainer::where('user_id', $user->id)
            ->with('seo.contents', 'achievements', 'skills', 'experiences.contents', 'degrees.contents')
            ->first();
        
        if (empty($trainer)) {
            $message = [
                'type' => 'warning',
                'message' => '<strong>Thông báo!</strong> Bạn chưa có hồ sơ HLV. Vui lòng liên hệ quản trị viên để được tạo hồ sơ.'
            ];
            $request->session()->put('message', $message);
            return redirect()->route('admin.account.profile');
        }
        
        $trainerCode = $trainer->trainer_code ?? null;
        
        return view('admin.account.trainerProfile', compact('trainer', 'trainerCode'));
    }
    
    /**
     * Cập nhật hồ sơ HLV của sub-admin (chỉ update các field được phép)
     */
    public function updateTrainerProfile(SubAdminTrainerProfileRequest $request)
    {
        $user = Auth::user();
        
        // Chỉ cho phép sub-admin (không phải admin)
        if ($user->hasRole('admin') || !$user->hasRole('sub-admin')) {
            abort(403, 'Bạn không có quyền thực hiện thao tác này.');
        }
        
        // Tìm trainer có user_id = user hiện tại
        $trainer = Trainer::where('user_id', $user->id)->first();
        
        if (empty($trainer)) {
            $message = [
                'type' => 'danger',
                'message' => '<strong>Lỗi!</strong> Không tìm thấy hồ sơ HLV.'
            ];
            $request->session()->put('message', $message);
            return redirect()->route('admin.account.trainerProfile');
        }
        
        try {
            DB::beginTransaction();
            
            // Upload image if provided
            $dataPath = [];
            if($request->hasFile('image')) {
                $name = !empty($trainer->seo->slug) ? $trainer->seo->slug : time();
                $fileName = $name.'.'.config('image.extension');
                $folderUpload = config('main_'.env('APP_NAME').'.google_cloud_storage.wallpapers');
                $dataPath = Upload::uploadWallpaper($request->file('image'), $fileName, $folderUpload);
            }
            
            // Chỉ update các field có trong request (phone, email, description, image)
            // Các field khác giữ nguyên - KHÔNG gọi buildArrayTableSeo
            $updateData = [];
            
            if ($request->has('phone')) {
                $updateData['phone'] = trim($request->phone) ?: null;
            }
            
            if ($request->has('email')) {
                $updateData['email'] = trim($request->email) ?: null;
            }
            
            if ($request->has('description')) {
                $updateData['description'] = trim($request->description) ?: null;
            }
            
            // Update trainer_info nếu có thay đổi
            if (!empty($updateData)) {
                Trainer::updateItem($trainer->id, $updateData);
            }
            
            // Reload trainer to get latest data after update
            $trainer = Trainer::find($trainer->id);
            
            // Sync trainer_info to users (for name, position, phone, email)
            if (!empty($trainer)) {
                $this->syncTrainerToUser($trainer);
            }
            
            // Update SEO chỉ với các field có giá trị (không dùng buildArrayTableSeo)
            if (!empty($trainer->seo_id)) {
                $seoUpdateData = [];
                
                // Sync description to seo.description (chỉ nếu có giá trị)
                if ($request->has('description') && !empty(trim($request->description))) {
                    $seoUpdateData['description'] = trim($request->description);
                }
                
                // Update image if uploaded
                if (!empty($dataPath)) {
                    $seoUpdateData['image'] = $dataPath;
                }
                
                // Chỉ update SEO nếu có dữ liệu cần update
                if (!empty($seoUpdateData)) {
                    // Preserve existing slug when updating
                    $existingSeo = Seo::find($trainer->seo_id);
                    if($existingSeo && !empty($existingSeo->slug)) {
                        $seoUpdateData['slug'] = $existingSeo->slug;
                    }
                    Seo::updateItem($trainer->seo_id, $seoUpdateData);
                }
            }
            
            // Handle repeater fields (achievements, skills, experiences, degrees)
            $idTrainer = $trainer->id;
            
            /* insert thành tích (trainer_achievement) */
            if($request->has('repeater_trainer_achievement')) {
                TrainerAchievement::select('*')
                    ->where('trainer_info_id', $idTrainer)
                    ->delete();
                if(!empty($request->get('repeater_trainer_achievement'))){
                    foreach($request->get('repeater_trainer_achievement') as $index => $achi){
                        if(!empty($achi['content'])){
                            TrainerAchievement::insertItem([
                                'trainer_info_id'   => $idTrainer,
                                'content'           => $achi['content'],
                                'ordering'          => $achi['ordering'] ?? $index,
                            ]);
                        }
                    }
                }
            }
            
            /* insert kỹ năng (trainer_skill) */
            if($request->has('repeater_trainer_skill')) {
                TrainerSkill::select('*')
                    ->where('trainer_info_id', $idTrainer)
                    ->delete();
                if(!empty($request->get('repeater_trainer_skill'))){
                    foreach($request->get('repeater_trainer_skill') as $index => $skill){
                        if(!empty($skill['skill'])&&!empty($skill['percent'])){
                            TrainerSkill::insertItem([
                                'trainer_info_id'   => $idTrainer,
                                'skill'             => $skill['skill'],
                                'percent'           => $skill['percent'],
                                'ordering'          => $skill['ordering'] ?? $index,
                            ]);
                        }
                    }
                }
            }
            
            /* insert kinh nghiệm (trainer_experience) */
            if($request->has('repeater_trainer_experience')) {
                TrainerExperience::select('*')
                    ->where('trainer_info_id', $idTrainer)
                    ->delete();
                if(!empty($request->get('repeater_trainer_experience'))){
                    foreach($request->get('repeater_trainer_experience') as $index => $exper){
                        if(!empty($exper['title'])&&!empty($exper['company'])&&!empty($exper['content'])){
                            $idTrainerExperience    = TrainerExperience::insertItem([
                                'trainer_info_id'   => $idTrainer,
                                'title'             => $exper['title'],
                                'company'           => $exper['company'],
                                'ordering'          => $exper['ordering'] ?? $index,
                            ]);
                            /* insert thêm content */
                            $tmp                    = explode("\r\n", $exper['content']);
                            foreach($tmp as $t){
                                if(!empty(trim($t))) {
                                    TrainerExperienceContent::insertItem([
                                        'trainer_experience_id' => $idTrainerExperience,
                                        'content'               => trim($t),
                                    ]);
                                }
                            }
                        }
                    }
                }
            }
            
            /* insert bằng cấp (trainer_degree) */
            if($request->has('repeater_trainer_degree')) {
                TrainerDegree::select('*')
                    ->where('trainer_info_id', $idTrainer)
                    ->delete();
                if(!empty($request->get('repeater_trainer_degree'))){
                    foreach($request->get('repeater_trainer_degree') as $index => $degree){
                        if(!empty($degree['title'])&&!empty($degree['school'])&&!empty($degree['content'])){
                            $idTrainerDegree    = TrainerDegree::insertItem([
                                'trainer_info_id'   => $idTrainer,
                                'title'             => $degree['title'],
                                'school'            => $degree['school'],
                                'ordering'          => $degree['ordering'] ?? $index,
                            ]);
                            /* insert thêm content */
                            $tmp                    = explode("\r\n", $degree['content']);
                            foreach($tmp as $t){
                                if(!empty(trim($t))) {
                                    TrainerDegreeContent::insertItem([
                                        'trainer_degree_id'     => $idTrainerDegree,
                                        'content'               => trim($t),
                                    ]);
                                }
                            }
                        }
                    }
                }
            }
            
            DB::commit();
            
            $message = [
                'type' => 'success',
                'message' => '<strong>Thành công!</strong> Đã cập nhật hồ sơ HLV!'
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error updating trainer profile (sub-admin)', [
                'user_id' => $user->id,
                'trainer_id' => $trainer->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            $message = [
                'type' => 'danger',
                'message' => '<strong>Lỗi!</strong> Có lỗi xảy ra: ' . $e->getMessage()
            ];
        }
        
        $request->session()->put('message', $message);
        return redirect()->route('admin.account.trainerProfile');
    }
    
    /**
     * Sync user data to trainer_info
     * Called when user profile is updated
     */
    private function syncUserToTrainer(User $user)
    {
        $trainer = Trainer::where('user_id', $user->id)->first();
        if (empty($trainer)) {
            return; // No trainer profile to sync
        }
        
        $updateData = [];
        
        // Sync name
        if (!empty($user->name) && $trainer->name !== $user->name) {
            $updateData['name'] = $user->name;
        }
        
        // Sync position
        if ($user->position !== $trainer->position) {
            $updateData['position'] = $user->position;
        }
        
        // Sync phone
        if ($user->phone !== $trainer->phone) {
            $updateData['phone'] = $user->phone;
        }
        
        // Sync email
        if (!empty($user->email) && $trainer->email !== $user->email) {
            $updateData['email'] = $user->email;
        }
        
        // Update trainer_info if there are changes
        if (!empty($updateData)) {
            Trainer::updateItem($trainer->id, $updateData);
        }
    }
    
    /**
     * Sync trainer_info data to user
     * Called when trainer profile is updated
     */
    private function syncTrainerToUser(Trainer $trainer)
    {
        if (empty($trainer->user_id)) {
            return; // No user to sync
        }
        
        $user = User::find($trainer->user_id);
        if (empty($user)) {
            return;
        }
        
        $hasChanges = false;
        
        // Sync name
        if (!empty($trainer->name) && $user->name !== $trainer->name) {
            $user->name = $trainer->name;
            $hasChanges = true;
        }
        
        // Sync position
        if ($user->position !== $trainer->position) {
            $user->position = $trainer->position;
            $hasChanges = true;
        }
        
        // Sync phone
        if ($user->phone !== $trainer->phone) {
            $user->phone = $trainer->phone;
            $hasChanges = true;
        }
        
        // Sync email
        if (!empty($trainer->email) && $user->email !== $trainer->email) {
            $user->email = $trainer->email;
            $hasChanges = true;
        }
        
        // Update user if there are changes
        if ($hasChanges) {
            $user->save();
        }
    }
}

