<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\Upload;
use App\Http\Requests\TrainerRequest as RequestsTrainerRequest;
use App\Models\Trainer;
use App\Models\Seo;
use App\Models\RelationSeoTrainerInfo;
use App\Models\Prompt;
use App\Models\Page;
use App\Models\TrainerAchievement;
use App\Models\TrainerExperience;
use App\Models\TrainerExperienceContent;
use App\Models\TrainerSkill;
use App\Models\TrainerDegree;
use App\Models\TrainerDegreeContent;
use App\Services\BuildInsertUpdateModel;
use App\Http\Requests\TrainerRequest;
// use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class TrainerController extends Controller {

    public function __construct(BuildInsertUpdateModel $BuildInsertUpdateModel){
        $this->BuildInsertUpdateModel  = $BuildInsertUpdateModel;
    }

    public function list(Request $request){
        $params             = [];
        /* Search theo tên */
        if(!empty($request->get('search_name'))) $params['search_name'] = $request->get('search_name');
        /* paginate */
        $viewPerPage        = Cookie::get('viewTrainerInfo') ?? 50;
        $params['paginate'] = $viewPerPage;
        if(auth()->user()->hasRole('admin')){
            $list               = Trainer::getList($params);
            return view('admin.trainer.list', compact('list', 'params', 'viewPerPage'));
        }else if(auth()->user()->hasRole('sub-admin')){
            $userId = auth()->user()->id;
            $list       = Trainer::select('*')
                                    ->where('user_id', $userId)
                                    ->with('seo')
                                    ->paginate(1);
            return view('admin.trainer.list', compact('list', 'params', 'viewPerPage'));
        }
        
    }

    public function view(Request $request){
        Log::info('=== TrainerController::view() DEBUG START ===');
        
        // Clear message from session if it's not from trainer action (e.g., from account profile)
        $sessionMessage = $request->session()->get('message');
        if (!empty($sessionMessage) && !empty($sessionMessage['message'])) {
            // Check if message is from trainer action (contains "Huấn luyện viên" or "trainer")
            $messageText = $sessionMessage['message'] ?? '';
            if (stripos($messageText, 'huấn luyện viên') === false && 
                stripos($messageText, 'trainer') === false &&
                stripos($messageText, 'thông tin cá nhân') !== false) {
                // Clear message if it's from account profile
                $request->session()->forget('message');
            }
        }
        
        $message            = $request->get('message') ?? null;
        $id                 = $request->get('id') ?? 0;
        $language           = $request->get('language') ?? 'vi';
        
        Log::info('Request params', [
            'id' => $id,
            'language' => $language,
            'message' => $message
        ]);
        
        $user = auth()->user();
        Log::info('Current user info', [
            'user_id' => $user->id ?? 'N/A',
            'user_name' => $user->name ?? 'N/A',
            'user_email' => $user->email ?? 'N/A',
            'user_role_column' => $user->role ?? 'N/A',
            'hasRole_admin' => $user->hasRole('admin') ? 'YES' : 'NO',
            'hasRole_sub-admin' => $user->hasRole('sub-admin') ? 'YES' : 'NO',
        ]);
        
        // Check roles via relation if available
        if (method_exists($user, 'roles')) {
            try {
                $userRoles = $user->roles;
                Log::info('User roles (via relation)', [
                    'roles_count' => $userRoles ? $userRoles->count() : 0,
                    'roles' => $userRoles ? $userRoles->pluck('slug', 'name')->toArray() : []
                ]);
            } catch (\Exception $e) {
                Log::warning('Error getting user roles via relation', ['error' => $e->getMessage()]);
            }
        }
        
        /* kiểm tra xem ngôn ngữ có nằm trong danh sách không */
        $flagView           = false;
        foreach(config('language') as $ld){
            if($ld['key']==$language) {
                $flagView   = true;
                break;
            }
        }
        Log::info('Language check', ['flagView' => $flagView ? 'YES' : 'NO']);
        
        /* tìm theo ngôn ngữ */
        $item               = Trainer::select('*')
                                ->where('id', $id)
                                ->with('seo.contents', 'seos.infoSeo.contents', 'achievements', 'skills', 'experiences.contents', 'degrees.contents')
                                ->first();
        
        Log::info('Trainer found', [
            'trainer_exists' => !empty($item) ? 'YES' : 'NO',
            'trainer_id' => $item->id ?? 'N/A',
            'trainer_user_id' => $item->user_id ?? 'NULL',
            'trainer_name' => $item->name ?? 'N/A',
            'trainer_seo_id' => $item->seo_id ?? 'N/A',
        ]);
        
        if(empty($item)) {
            $flagView = false;
            Log::warning('Trainer not found, setting flagView to false');
        }
        
        // Only admin can access trainer view
        if(!$user->hasRole('admin')){
            abort(403, 'Bạn không có quyền truy cập trang này.');
        }
        
        if($flagView && !empty($item)){
            Log::info('Permission granted, loading view');
            /* lấy item seo theo ngôn ngữ được chọn */
            $itemSeo = [];
            if (!empty($item->seos)) {
                foreach ($item->seos as $s) {
                    if ($s->infoSeo->language == $language) {
                        $itemSeo = $s->infoSeo;
                        break;
                    }
                }
            }
            /* prompts */
            $prompts = Prompt::select('*')
                        ->where('reference_table', 'trainer_info')
                        ->get();
            /* type */
            $type = !empty($itemSeo) ? 'edit' : 'create';
            $type = $request->get('type') ?? $type;
            /* trang cha */
            $parents = Page::all();
            
            // Get message from session and then remove it (flash message - only show once)
            $sessionMessage = $request->session()->get('message');
            if (!empty($sessionMessage)) {
                // Check if message is from trainer action (contains "Huấn luyện viên" or "trainer")
                $messageText = $sessionMessage['message'] ?? '';
                if (stripos($messageText, 'huấn luyện viên') !== false || 
                    stripos($messageText, 'trainer') !== false) {
                    // This is a trainer message, will be displayed once and then removed
                    // Message will be removed after view renders (handled in view)
                } else {
                    // Not a trainer message, remove it
                    $request->session()->forget('message');
                }
            }
            
            Log::info('=== TrainerController::view() DEBUG END - SUCCESS ===');
            return view('admin.trainer.view', compact('item', 'itemSeo', 'prompts', 'type', 'language', 'parents', 'message'));
        } else {
            Log::warning('=== TrainerController::view() DEBUG END - PERMISSION DENIED ===');
            Log::warning('Redirecting to trainer list due to permission denial');
            return redirect()->route('admin.trainer.list');
        }
    }

    public function createAndUpdate(TrainerRequest $request){
        // Only admin can access this method
        if(!auth()->user()->hasRole('admin')){
            abort(403, 'Bạn không có quyền thực hiện thao tác này.');
        }
        
        try {
            DB::beginTransaction();
            /* ngôn ngữ */
            $idSeo              = $request->get('seo_id');
            $idSeoVI            = $request->get('seo_id_vi') ?? 0;
            $idTrainer          = $request->get('trainer_info_id');
            $typePage           = 'trainer_info';
            $type               = $request->get('type');
            /* check xem là create seo hay update seo */
            $action             = !empty($idSeo)&&$type=='edit' ? 'edit' : 'create';
            /* upload image */
            $dataPath           = [];
            if($request->hasFile('image')) {
                $name           = !empty($request->get('slug')) ? $request->get('slug') : time();
                $fileName       = $name.'.'.config('image.extension');
                $folderUpload   =  config('main_'.env('APP_NAME').'.google_cloud_storage.wallpapers');
                $dataPath       = Upload::uploadWallpaper($request->file('image'), $fileName, $folderUpload);
            }
            /* update page */
            // For trainer_info, sync description from trainer_info to seo.description
            $requestData = $request->all();
            if($typePage === 'trainer_info' && !empty($requestData['description'])) {
                $requestData['trainer_description'] = $requestData['description'];
            }
            $seo                = $this->BuildInsertUpdateModel->buildArrayTableSeo($requestData, $typePage, $dataPath);
            if($action=='edit'){
                Seo::updateItem($idSeo, $seo);
            }else {
                $idSeo = Seo::insertItem($seo, $idSeoVI);
            }
            /* kiểm tra insert thành công không */
            if(!empty($idSeo)){
                /* insert hoặc update trainer_info */
                if(empty($idTrainer)){ /* check xem create hay update */
                    $trainerData = [
                        'seo_id'        => $idSeo,
                        'phone'         => $request->get('phone'),
                        'email'         => $request->get('email'),
                        'name'          => $request->get('name'),
                        'position'      => $request->get('position'),
                        'description'   => $request->get('description'),
                    ];
                    // Set user_id for sub-admin when creating new trainer
                    if(auth()->user()->hasRole('sub-admin') && !auth()->user()->hasRole('admin')){
                        $trainerData['user_id'] = auth()->user()->id;
                    }
                    $idTrainer  = Trainer::insertItem($trainerData);
                    // Generate trainer_code automatically for new trainer
                    if (!empty($idTrainer)) {
                        $trainerCode = Trainer::generateTrainerCode($idTrainer, $idSeo);
                        if (!empty($trainerCode)) {
                            Trainer::updateItem($idTrainer, ['trainer_code' => $trainerCode]);
                        }
                        // Sync description from trainer_info to seo.description for new trainer
                        if(!empty($trainerData['description'])) {
                            $seoData = ['description' => $trainerData['description']];
                            Seo::updateItem($idSeo, $seoData);
                        }
                        
                        // Sync trainer_info to users (for new trainer)
                        $trainer = Trainer::find($idTrainer);
                        if (!empty($trainer) && !empty($trainer->user_id)) {
                            $user = User::find($trainer->user_id);
                            if (!empty($user)) {
                                $hasChanges = false;
                                
                                // Sync name
                                if (!empty($trainerData['name']) && $user->name !== $trainerData['name']) {
                                    $user->name = $trainerData['name'];
                                    $hasChanges = true;
                                }
                                
                                // Sync position
                                if (isset($trainerData['position']) && $user->position !== $trainerData['position']) {
                                    $user->position = $trainerData['position'];
                                    $hasChanges = true;
                                }
                                
                                // Sync phone
                                if (isset($trainerData['phone']) && $user->phone !== $trainerData['phone']) {
                                    $user->phone = $trainerData['phone'];
                                    $hasChanges = true;
                                }
                                
                                // Sync email
                                if (!empty($trainerData['email']) && $user->email !== $trainerData['email']) {
                                    $user->email = $trainerData['email'];
                                    $hasChanges = true;
                                }
                                
                                // Update user if there are changes
                                if ($hasChanges) {
                                    $user->save();
                                }
                            }
                        }
                    }
                }else {
                    $dataTrainer    = [];
                    if(!empty($request->get('phone'))) $dataTrainer['phone'] = $request->get('phone');
                    if(!empty($request->get('email'))) $dataTrainer['email'] = $request->get('email');
                    if($request->has('name')) $dataTrainer['name'] = $request->get('name');
                    if($request->has('position')) $dataTrainer['position'] = $request->get('position');
                    if($request->has('description')) $dataTrainer['description'] = $request->get('description');
                    // Generate trainer_code if not exists
                    $trainer = Trainer::find($idTrainer);
                    if (!empty($trainer) && empty($trainer->trainer_code)) {
                        $trainerCode = Trainer::generateTrainerCode($idTrainer, $idSeo);
                        if (!empty($trainerCode)) {
                            $dataTrainer['trainer_code'] = $trainerCode;
                        }
                    }
                    Trainer::updateItem($idTrainer, $dataTrainer);
                    
                    // Sync description from trainer_info to seo.description
                    if(isset($dataTrainer['description'])) {
                        // Get existing SEO to preserve slug
                        $existingSeo = Seo::find($idSeo);
                        $seoData = [
                            'description' => $dataTrainer['description']
                        ];
                        // Preserve slug if exists
                        if($existingSeo && !empty($existingSeo->slug)) {
                            $seoData['slug'] = $existingSeo->slug;
                        }
                        Seo::updateItem($idSeo, $seoData);
                    }
                    
                    // Reload trainer to get latest data after update
                    $trainer = Trainer::find($idTrainer);
                    
                    // Sync trainer_info to users (for name, position, phone, email)
                    if (!empty($trainer) && !empty($trainer->user_id)) {
                        $user = User::find($trainer->user_id);
                        if (!empty($user)) {
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
                }
                /* relation_seo_trainer_info */
                $relationSeoTrainerInfo = RelationSeoTrainerInfo::select('*')
                                        ->where('seo_id', $idSeo)
                                        ->where('trainer_info_id', $idTrainer)
                                        ->first();
                if(empty($relationSeoTrainerInfo)) RelationSeoTrainerInfo::insertItem([
                    'seo_id'            => $idSeo,
                    'trainer_info_id'   => $idTrainer,
                ]);
                /* insert thành tích (trainer_achievenment) */
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
                /* insert kỹ năng (trainer_skill) */
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
                /* insert kinh nghiệm (trainer_experience) */
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
                            /* insert thêm content => ở đây chỉ insert và không xóa content cũ (chấp nhận phình dữ liệu) */
                            $tmp                    = explode("\r\n", $exper['content']);
                            foreach($tmp as $t){
                                TrainerExperienceContent::insertItem([
                                    'trainer_experience_id' => $idTrainerExperience,
                                    'content'               => trim($t),
                                ]);
                            }
                        }
                    }
                }
                /* insert bằng cấp (trainer_degree) */
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
                            /* insert thêm content => ở đây chỉ insert và không xóa content cũ (chấp nhận phình dữ liệu) */
                            $tmp                    = explode("\r\n", $degree['content']);
                            foreach($tmp as $t){
                                TrainerDegreeContent::insertItem([
                                    'trainer_degree_id'     => $idTrainerDegree,
                                    'content'               => trim($t),
                                ]);
                            }
                        }
                    }
                }
                DB::commit();
                /* Message */
                $message        = [
                    'type'      => 'success',
                    'message'   => '<strong>Thành công!</strong> Đã cập nhật Huấn luyện viên!'
                ];
                /* nếu có tùy chọn index => gửi google index */
                if(!empty($request->get('index_google'))&&$request->get('index_google')=='on') {
                    $flagIndex = IndexController::indexUrl($idSeo);
                    if($flagIndex==200){
                        $message['message'] = '<strong>Thành công!</strong> Đã cập nhật Huấn luyện viên và Báo Google Index!';
                    }else {
                        $message['message'] = '<strong>Thành công!</strong> Đã cập nhật Huấn luyện viên <span style="color:red;">nhưng báo Google Index lỗi</span>';
                    }
                }
            }
        } catch (\Exception $exception){
            DB::rollBack();
            Log::error('TrainerController::createAndUpdate() ERROR', [
                'message' => $exception->getMessage(),
                'trace' => $exception->getTraceAsString(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine()
            ]);
        }
        /* có lỗi mặc định Message */
        if(empty($message)){
            $message        = [
                'type'      => 'danger',
                'message'   => '<strong>Thất bại!</strong> Có lỗi xảy ra, vui lòng thử lại'
            ];
        }
        $request->session()->put('message', $message);
        return redirect()->route('admin.trainer.view', ['id' => $idTrainer]);
    }
    
    public function createUser(){
        $teachers = Trainer::select('*')->get();
        $count = 0;

        foreach ($teachers as $teacher) {
            $slug = $teacher->seo->slug ?? '';
            $email = str_replace('-', '', $slug);

            $infoUser = User::where('email', $email)->first();

            if (!empty($slug) && empty($infoUser)) {
                $idUser = User::create([
                    'name' => $slug,
                    'email' => $email,
                    'password' => Hash::make($email)
                ]);

                UserRole::insertItem([
                    'user_id' => $idUser->id,
                    'role_id' => 2,
                ]);

                if ($idUser) ++$count;
            }
        }

        return response()->json([
            'status' => true,
            'message' => '👋 Đã tạo thành công <span class="highLight_500">' . $count . '</span> tài khoản HLV mới.',
            'count' => $count,
        ]);
    }

    public function delete(Request $request){
        if(!empty($request->get('id'))){
            try {
                DB::beginTransaction();
                $id         = $request->get('id');
                $info       = Trainer::select('*')
                                ->where('id', $id)
                                ->with('seo', 'seos')
                                ->first();
                /* xóa ảnh đại diện trên google_clouds */ 
                if(!empty($info->seo->image)) Upload::deleteWallpaper($info->seo->image);
                /* delete relation */
                $info->achievements()->delete();
                $info->skills()->delete();
                // $info->experiences()->contents()->delete();
                foreach($info->experiences as $e){
                    $e->contents()->delete();
                }
                $info->experiences()->delete();
                // $info->degrees()->contents()->delete();
                foreach($info->degrees as $d){
                    $d->contents()->delete();
                }
                $info->degrees()->delete();
                /* delete các trang seos ngôn ngữ */
                foreach($info->seos as $s){
                    /* xóa ảnh đại diện trên google_clouds */ 
                    if(!empty($s->infoSeo->image)) Upload::deleteWallpaper($s->infoSeo->image);
                    if(!empty($s->infoSeo->contents)) foreach($s->infoSeo->contents as $c) $c->delete();
                    $s->infoSeo()->delete();
                    $s->delete();
                }
                /* Xóa user tương ứng */
                $slug = $info->seo->slug ?? null;
                if (!empty($slug)) {
                    $email = str_replace('-', '', $slug);
                    $user = User::where('email', $email)->first();
                    
                    if (!empty($user)) {
                        // Xóa tất cả vai trò của user
                        UserRole::where('user_id', $user->id)->delete();
                        // Xóa user
                        $user->delete();
                    }
                }

                $info->delete();
                DB::commit();
                return true;
            } catch (\Exception $exception){
                DB::rollBack();
                return false;
            }
        }
    }
}
