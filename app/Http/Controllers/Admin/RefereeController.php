<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\Upload;
use App\Models\Referee;
use App\Models\Seo;
use App\Models\RelationSeoRefereeInfo;
use App\Models\Prompt;
use App\Models\Page;
use App\Models\RefereeAchievement;
use App\Models\RefereeExperience;
use App\Models\RefereeExperienceContent;
use App\Models\RefereeSkill;
use App\Models\RefereeDegree;
use App\Models\RefereeDegreeContent;
use App\Models\ProfileActivityImage;
use App\Services\BuildInsertUpdateModel;
use App\Http\Requests\RefereeRequest;
// use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class RefereeController extends Controller {

    public function __construct(BuildInsertUpdateModel $BuildInsertUpdateModel){
        $this->BuildInsertUpdateModel  = $BuildInsertUpdateModel;
    }

    public function list(Request $request){
        $params             = [];
        /* Search theo tên */
        if(!empty($request->get('search_name'))) $params['search_name'] = $request->get('search_name');
        /* paginate */
        $viewPerPage        = Cookie::get('viewRefereeInfo') ?? 50;
        $params['paginate'] = $viewPerPage;
        if(auth()->user()->hasRole('admin')){
            $list               = Referee::getList($params);
            return view('admin.referee.list', compact('list', 'params', 'viewPerPage'));
        }else if(auth()->user()->hasRole('referee')){
            $username   = auth()->user()->name;
            $list       = Referee::select('*')
                                    ->whereHas('seo', function($query) use($username){
                                        $query->where('slug', $username);
                                    })
                                    ->paginate(1);
            return view('admin.referee.list', compact('list', 'params', 'viewPerPage'));
        }
        
    }

    public function view(Request $request){
        $message            = $request->get('message') ?? null;
        $id                 = $request->get('id') ?? 0;
        $language           = $request->get('language') ?? 'vi';
        /* kiểm tra xem ngôn ngữ có nằm trong danh sách không */
        $flagView           = false;
        foreach(config('language') as $ld){
            if($ld['key']==$language) {
                $flagView   = true;
                break;
            }
        }
        /* tìm theo ngôn ngữ */
        $item               = Referee::select('*')
                                ->where('id', $id)
                                ->with('seo.contents', 'seos.infoSeo.contents', 'achievements', 'skills', 'experiences.contents', 'degrees.contents', 'activityImages')
                                ->first();
        if(empty($item)) $flagView = false;
        $slug               = $item->seo->slug ?? '';
        $hasAdminRole = auth()->user()->hasRole('admin');
        if($hasAdminRole || ($flagView == true && $slug == auth()->user()->name)){
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
                        ->where('reference_table', 'referee_info')
                        ->get();
            /* type */
            $type = !empty($itemSeo) ? 'edit' : 'create';
            $type = $request->get('type') ?? $type;
            /* trang cha */
            $parents = Page::all();
            return view('admin.referee.view', compact('item', 'itemSeo', 'prompts', 'type', 'language', 'parents', 'message'));
        } else {
            return redirect()->route('admin.referee.list');
        }
    }

    public function createAndUpdate(RefereeRequest $request){
        try {
            DB::beginTransaction();
            /* ngôn ngữ */
            $idSeo              = $request->get('seo_id');
            $idSeoVI            = $request->get('seo_id_vi') ?? 0;
            $idReferee          = $request->get('referee_info_id');
            $typePage           = 'referee_info';
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
            $seo                = $this->BuildInsertUpdateModel->buildArrayTableSeo($request->all(), $typePage, $dataPath);
            
            // Tạo giá trị mặc định cho seo_description khi tạo mới (nếu chưa có)
            if($action=='create' && empty($seo['seo_description'])) {
                $refereeName = $request->get('name') ?? '';
                $seo['seo_description'] = "Trọng tài {$refereeName} là chuyên gia uy tín, được đào tạo bài bản trong lĩnh vực cử tạ - thể hình. Với tinh thần công tâm và chuyên nghiệp, {$refereeName} đảm bảo mọi giải đấu diễn ra công bằng, minh bạch và đúng chuẩn quốc gia - quốc tế.";
            }
            
            if($action=='edit'){
                Seo::updateItem($idSeo, $seo);
            }else {
                $idSeo = Seo::insertItem($seo, $idSeoVI);
            }
            /* kiểm tra insert thành công không */
            if(!empty($idSeo)){
                /* insert hoặc update referee_info */
                if(empty($idReferee)){ /* check xem create hay update */
                    $idReferee  = Referee::insertItem([
                        'seo_id'                => $idSeo,
                        'phone'                 => $request->get('phone'),
                        'email'                 => $request->get('email'),
                        'name'                  => $request->get('name'),
                        'position'              => $request->get('position'),
                        'total_learner'         => (int) $request->get('total_learner', 0),
                        'total_teaching_hour'   => (int) $request->get('total_teaching_hour', 0),
                        'total_prize'           => (int) $request->get('total_prize', 0),
                        'area'                  => $request->get('area') ?: null,
                        'years_experience'      => $request->filled('years_experience') ? (int) $request->get('years_experience') : null,
                        'languages'             => \App\Helpers\SpokenLanguage::fromRequest($request->input('languages')),
                    ]);
                }else {
                    $dataReferee    = [];
                    if(!empty($request->get('phone'))) $dataReferee['phone'] = $request->get('phone');
                    if(!empty($request->get('email'))) $dataReferee['email'] = $request->get('email');
                    if($request->has('name')) $dataReferee['name'] = $request->get('name');
                    if($request->has('position')) $dataReferee['position'] = $request->get('position');
                    if($request->has('total_learner')) $dataReferee['total_learner'] = (int) $request->get('total_learner', 0);
                    if($request->has('total_teaching_hour')) $dataReferee['total_teaching_hour'] = (int) $request->get('total_teaching_hour', 0);
                    if($request->has('total_prize')) $dataReferee['total_prize'] = (int) $request->get('total_prize', 0);
                    if($request->has('area')) $dataReferee['area'] = $request->get('area') ?: null;
                    if($request->has('years_experience')) $dataReferee['years_experience'] = $request->filled('years_experience') ? (int) $request->get('years_experience') : null;
                    $dataReferee['languages'] = \App\Helpers\SpokenLanguage::fromRequest($request->input('languages'));
                    Referee::updateItem($idReferee, $dataReferee);
                }
                /* relation_seo_referee_info */
                $relationSeoRefereeInfo = RelationSeoRefereeInfo::select('*')
                                        ->where('seo_id', $idSeo)
                                        ->where('referee_info_id', $idReferee)
                                        ->first();
                if(empty($relationSeoRefereeInfo)) RelationSeoRefereeInfo::insertItem([
                    'seo_id'            => $idSeo,
                    'referee_info_id'   => $idReferee,
                ]);
                /* insert thành tích (referee_achievenment) */
                RefereeAchievement::select('*')
                    ->where('referee_info_id', $idReferee)
                    ->delete();
                if(!empty($request->get('repeater_referee_achievement'))){
                    foreach($request->get('repeater_referee_achievement') as $index => $achi){
                        if(!empty($achi['content'])){
                            RefereeAchievement::insertItem([
                                'referee_info_id'   => $idReferee,
                                'content'           => $achi['content'],
                                'ordering'          => $achi['ordering'] ?? $index,
                            ]);
                        }
                    }
                }
                /* insert kỹ năng (referee_skill) */
                RefereeSkill::select('*')
                    ->where('referee_info_id', $idReferee)
                    ->delete();
                if(!empty($request->get('repeater_referee_skill'))){
                    foreach($request->get('repeater_referee_skill') as $index => $skill){
                        if(!empty($skill['skill'])&&!empty($skill['percent'])){
                            RefereeSkill::insertItem([
                                'referee_info_id'   => $idReferee,
                                'skill'             => $skill['skill'],
                                'percent'           => $skill['percent'],
                                'ordering'          => $skill['ordering'] ?? $index,
                            ]);
                        }
                    }
                }
                /* insert kinh nghiệm (referee_experience) */
                RefereeExperience::select('*')
                    ->where('referee_info_id', $idReferee)
                    ->delete();
                if(!empty($request->get('repeater_referee_experience'))){
                    foreach($request->get('repeater_referee_experience') as $index => $exper){
                        if(!empty($exper['title'])&&!empty($exper['company'])&&!empty($exper['content'])){
                            $idRefereeExperience    = RefereeExperience::insertItem([
                                'referee_info_id'   => $idReferee,
                                'title'             => $exper['title'],
                                'company'           => $exper['company'],
                                'ordering'          => $exper['ordering'] ?? $index,
                            ]);
                            /* insert thêm content => ở đây chỉ insert và không xóa content cũ (chấp nhận phình dữ liệu) */
                            $tmp                    = explode("\r\n", $exper['content']);
                            foreach($tmp as $t){
                                RefereeExperienceContent::insertItem([
                                    'referee_experience_id' => $idRefereeExperience,
                                    'content'               => trim($t),
                                ]);
                            }
                        }
                    }
                }
                /* insert bằng cấp (referee_degree) */
                RefereeDegree::select('*')
                    ->where('referee_info_id', $idReferee)
                    ->delete();
                if(!empty($request->get('repeater_referee_degree'))){
                    foreach($request->get('repeater_referee_degree') as $index => $degree){
                        if(!empty($degree['title'])&&!empty($degree['school'])&&!empty($degree['content'])){
                            $idRefereeDegree    = RefereeDegree::insertItem([
                                'referee_info_id'   => $idReferee,
                                'title'             => $degree['title'],
                                'school'            => $degree['school'],
                                'ordering'          => $degree['ordering'] ?? $index,
                            ]);
                            /* insert thêm content => ở đây chỉ insert và không xóa content cũ (chấp nhận phình dữ liệu) */
                            $tmp                    = explode("\r\n", $degree['content']);
                            foreach($tmp as $t){
                                RefereeDegreeContent::insertItem([
                                    'referee_degree_id'     => $idRefereeDegree,
                                    'content'               => trim($t),
                                ]);
                            }
                        }
                    }
                }
                /* Hình ảnh hoạt động: quản lý qua AJAX (ProfileActivityImageController), không xử lý khi submit form */
                DB::commit();
                /* Message */
                $message        = [
                    'type'      => 'success',
                    'message'   => '<strong>Thành công!</strong> Đã cập nhật Trọng tài!'
                ];
                /* nếu có tùy chọn index => gửi google index */
                if(!empty($request->get('index_google'))&&$request->get('index_google')=='on') {
                    $flagIndex = IndexController::indexUrl($idSeo);
                    if($flagIndex==200){
                        $message['message'] = '<strong>Thành công!</strong> Đã cập nhật Trọng tài và Báo Google Index!';
                    }else {
                        $message['message'] = '<strong>Thành công!</strong> Đã cập nhật Trọng tài <span style="color:red;">nhưng báo Google Index lỗi</span>';
                    }
                }
            }
        } catch (\Exception $exception){
            DB::rollBack();
        }
        /* có lỗi mặc định Message */
        if(empty($message)){
            $message        = [
                'type'      => 'danger',
                'message'   => '<strong>Thất bại!</strong> Có lỗi xảy ra, vui lòng thử lại'
            ];
        }
        $request->session()->put('message', $message);
        return redirect()->route('admin.referee.view', ['id' => $idReferee]);
    }
    
    public function createUser(){
        $teachers = Referee::select('*')->get();
        $count = 0;

        foreach ($teachers as $teacher) {
            $slug = $teacher->seo->slug ?? '';
            $email = str_replace('-', '', $slug);

            $infoUser = User::where('email', $email)->first();

            if (!empty($slug) && empty($infoUser)) {
                $idUser = User::create([
                    'name' => $slug,
                    'email' => $email,
                    'password' => Hash::make($email),
                    'role' => 'referee'
                ]);

                $refereeRoleId = \App\Models\Role::where('slug', 'referee')->value('id');
                if ($refereeRoleId) {
                    UserRole::insertItem([
                        'user_id' => $idUser->id,
                        'role_id' => $refereeRoleId,
                    ]);
                }

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
        if(!auth()->user() || !auth()->user()->hasRole('admin')){
            abort(403, 'Bạn không có quyền xóa hồ sơ trọng tài.');
        }

        $result = app(\App\Services\ProfileDeletionService::class)
            ->deleteReferee((int) $request->get('id'));

        if ($request->wantsJson()) {
            return response()->json([
                'status' => !empty($result['ok']),
                'message' => $result['message'] ?? '',
            ], !empty($result['ok']) ? 200 : 422);
        }

        return !empty($result['ok']);
    }
}
