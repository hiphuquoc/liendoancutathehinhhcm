<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\Upload;
use App\Models\Athlete;
use App\Models\Seo;
use App\Models\RelationSeoAthleteInfo;
use App\Models\Prompt;
use App\Models\Page;
use App\Models\AthleteAchievement;
use App\Models\AthleteExperience;
use App\Models\AthleteExperienceContent;
use App\Models\AthleteSkill;
use App\Models\AthleteDegree;
use App\Models\AthleteDegreeContent;
use App\Services\BuildInsertUpdateModel;
use App\Http\Requests\AthleteRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AthleteController extends Controller
{
    public function __construct(BuildInsertUpdateModel $BuildInsertUpdateModel)
    {
        $this->BuildInsertUpdateModel = $BuildInsertUpdateModel;
    }

    public function list(Request $request)
    {
        $params = [];
        if (!empty($request->get('search_name'))) {
            $params['search_name'] = $request->get('search_name');
        }
        $viewPerPage = Cookie::get('viewAthleteInfo') ?? 50;
        $params['paginate'] = $viewPerPage;
        if (auth()->user()->hasRole('admin')) {
            $list = Athlete::getList($params);

            return view('admin.athlete.list', compact('list', 'params', 'viewPerPage'));
        } elseif (auth()->user()->hasRole('sub-admin')) {
            $userId = auth()->user()->id;
            $list = Athlete::select('*')
                ->where('user_id', $userId)
                ->with('seo')
                ->paginate(1);

            return view('admin.athlete.list', compact('list', 'params', 'viewPerPage'));
        }
    }

    public function view(Request $request)
    {
        Log::info('=== AthleteController::view() DEBUG START ===');

        $sessionMessage = $request->session()->get('message');
        if (!empty($sessionMessage) && !empty($sessionMessage['message'])) {
            $messageText = $sessionMessage['message'] ?? '';
            if (stripos($messageText, 'vận động viên') === false &&
                stripos($messageText, 'athlete') === false &&
                stripos($messageText, 'thông tin cá nhân') !== false) {
                $request->session()->forget('message');
            }
        }

        $message = $request->get('message') ?? null;
        $id = $request->get('id') ?? 0;
        $language = $request->get('language') ?? 'vi';

        $user = auth()->user();

        $flagView = false;
        foreach (config('language') as $ld) {
            if ($ld['key'] == $language) {
                $flagView = true;
                break;
            }
        }

        $item = Athlete::select('*')
            ->where('id', $id)
            ->with('seo.contents', 'seos.infoSeo.contents', 'achievements', 'skills', 'experiences.contents', 'degrees.contents', 'activityImages')
            ->first();

        if (empty($item)) {
            $flagView = false;
        }

        if (!$user->hasRole('admin')) {
            abort(403, 'Bạn không có quyền truy cập trang này.');
        }

        if ($flagView && !empty($item)) {
            $itemSeo = [];
            if (!empty($item->seos)) {
                foreach ($item->seos as $s) {
                    if ($s->infoSeo->language == $language) {
                        $itemSeo = $s->infoSeo;
                        break;
                    }
                }
            }
            $prompts = Prompt::select('*')
                ->where('reference_table', 'athlete_info')
                ->get();
            $type = !empty($itemSeo) ? 'edit' : 'create';
            $type = $request->get('type') ?? $type;
            $parents = Page::all();

            $sessionMessage = $request->session()->get('message');
            if (!empty($sessionMessage)) {
                $messageText = $sessionMessage['message'] ?? '';
                if (stripos($messageText, 'vận động viên') === false &&
                    stripos($messageText, 'athlete') === false) {
                    $request->session()->forget('message');
                }
            }

            Log::info('=== AthleteController::view() DEBUG END - SUCCESS ===');

            return view('admin.athlete.view', compact('item', 'itemSeo', 'prompts', 'type', 'language', 'parents', 'message'));
        }

        Log::warning('=== AthleteController::view() DEBUG END - REDIRECT ===');

        return redirect()->route('admin.athlete.list');
    }

    public function createAndUpdate(AthleteRequest $request)
    {
        if (!auth()->user()->hasRole('admin')) {
            abort(403, 'Bạn không có quyền thực hiện thao tác này.');
        }

        $idAthlete = (int) $request->get('athlete_info_id', 0);
        $message = null;

        try {
            DB::beginTransaction();
            $idSeo = $request->get('seo_id');
            $idSeoVI = $request->get('seo_id_vi') ?? 0;
            $idAthlete = (int) $request->get('athlete_info_id', 0);
            $typePage = 'athlete_info';
            $type = $request->get('type');
            $action = !empty($idSeo) && $type == 'edit' ? 'edit' : 'create';
            $dataPath = [];
            if ($request->hasFile('image')) {
                $name = !empty($request->get('slug')) ? $request->get('slug') : time();
                $fileName = $name.'.'.config('image.extension');
                $folderUpload = config('main_'.env('APP_NAME').'.google_cloud_storage.wallpapers');
                $dataPath = Upload::uploadWallpaper($request->file('image'), $fileName, $folderUpload);
            }
            $requestData = $request->all();
            if ($typePage === 'athlete_info' && !empty($requestData['description'])) {
                $requestData['athlete_description'] = $requestData['description'];
            }
            $seo = $this->BuildInsertUpdateModel->buildArrayTableSeo($requestData, $typePage, $dataPath);

            if ($action == 'create' && empty($seo['seo_description'])) {
                $athleteName = $request->get('name') ?? '';
                $seo['seo_description'] = "Vận động viên {$athleteName} là thành viên tích cực trong lĩnh vực cử tạ - thể hình. Với tinh thần thi đấu và rèn luyện bền bỉ, {$athleteName} góp phần phát triển phong trào thể thao của Liên đoàn.";
            }

            if ($action == 'edit') {
                Seo::updateItem($idSeo, $seo);
            } else {
                $idSeo = Seo::insertItem($seo, $idSeoVI);
            }

            if (!empty($idSeo)) {
                if (empty($idAthlete)) {
                    $athleteData = [
                        'seo_id' => $idSeo,
                        'phone' => $request->get('phone'),
                        'email' => $request->get('email'),
                        'name' => $request->get('name'),
                        'position' => $request->get('position'),
                        'description' => $request->get('description'),
                        'total_learner' => (int) $request->get('total_learner', 0),
                        'total_teaching_hour' => (int) $request->get('total_teaching_hour', 0),
                        'total_prize' => (int) $request->get('total_prize', 0),
                    ];
                    if (auth()->user()->hasRole('sub-admin') && !auth()->user()->hasRole('admin')) {
                        $athleteData['user_id'] = auth()->user()->id;
                    }
                    $idAthlete = Athlete::insertItem($athleteData);
                    if (!empty($idAthlete)) {
                        $athleteCode = Athlete::generateAthleteCode($idAthlete, $idSeo);
                        if (!empty($athleteCode)) {
                            Athlete::updateItem($idAthlete, ['athlete_code' => $athleteCode]);
                        }
                        if (!empty($athleteData['description'])) {
                            $seoData = ['description' => $athleteData['description']];
                            Seo::updateItem($idSeo, $seoData);
                        }
                        $athlete = Athlete::find($idAthlete);
                        if (!empty($athlete) && !empty($athlete->user_id)) {
                            $user = User::find($athlete->user_id);
                            if (!empty($user)) {
                                $hasChanges = false;
                                if (!empty($athleteData['name']) && $user->name !== $athleteData['name']) {
                                    $user->name = $athleteData['name'];
                                    $hasChanges = true;
                                }
                                if (isset($athleteData['position']) && $user->position !== $athleteData['position']) {
                                    $user->position = $athleteData['position'];
                                    $hasChanges = true;
                                }
                                if (isset($athleteData['phone']) && $user->phone !== $athleteData['phone']) {
                                    $user->phone = $athleteData['phone'];
                                    $hasChanges = true;
                                }
                                if (!empty($athleteData['email']) && $user->email !== $athleteData['email']) {
                                    $user->email = $athleteData['email'];
                                    $hasChanges = true;
                                }
                                if ($hasChanges) {
                                    $user->save();
                                }
                            }
                        }
                    }
                } else {
                    $dataAthlete = [];
                    if (!empty($request->get('phone'))) {
                        $dataAthlete['phone'] = $request->get('phone');
                    }
                    if (!empty($request->get('email'))) {
                        $dataAthlete['email'] = $request->get('email');
                    }
                    if ($request->has('name')) {
                        $dataAthlete['name'] = $request->get('name');
                    }
                    if ($request->has('position')) {
                        $dataAthlete['position'] = $request->get('position');
                    }
                    if ($request->has('description')) {
                        $dataAthlete['description'] = $request->get('description');
                    }
                    if ($request->has('total_learner')) {
                        $dataAthlete['total_learner'] = (int) $request->get('total_learner', 0);
                    }
                    if ($request->has('total_teaching_hour')) {
                        $dataAthlete['total_teaching_hour'] = (int) $request->get('total_teaching_hour', 0);
                    }
                    if ($request->has('total_prize')) {
                        $dataAthlete['total_prize'] = (int) $request->get('total_prize', 0);
                    }
                    $athlete = Athlete::find($idAthlete);
                    if (!empty($athlete) && empty($athlete->athlete_code)) {
                        $athleteCode = Athlete::generateAthleteCode($idAthlete, $idSeo);
                        if (!empty($athleteCode)) {
                            $dataAthlete['athlete_code'] = $athleteCode;
                        }
                    }
                    Athlete::updateItem($idAthlete, $dataAthlete);

                    if (isset($dataAthlete['description'])) {
                        $existingSeo = Seo::find($idSeo);
                        $seoData = [
                            'description' => $dataAthlete['description'],
                        ];
                        if ($existingSeo && !empty($existingSeo->slug)) {
                            $seoData['slug'] = $existingSeo->slug;
                        }
                        Seo::updateItem($idSeo, $seoData);
                    }

                    $athlete = Athlete::find($idAthlete);
                    if (!empty($athlete) && !empty($athlete->user_id)) {
                        $user = User::find($athlete->user_id);
                        if (!empty($user)) {
                            $hasChanges = false;
                            if (!empty($athlete->name) && $user->name !== $athlete->name) {
                                $user->name = $athlete->name;
                                $hasChanges = true;
                            }
                            if ($user->position !== $athlete->position) {
                                $user->position = $athlete->position;
                                $hasChanges = true;
                            }
                            if ($user->phone !== $athlete->phone) {
                                $user->phone = $athlete->phone;
                                $hasChanges = true;
                            }
                            if (!empty($athlete->email) && $user->email !== $athlete->email) {
                                $user->email = $athlete->email;
                                $hasChanges = true;
                            }
                            if ($hasChanges) {
                                $user->save();
                            }
                        }
                    }
                }

                $relation = RelationSeoAthleteInfo::select('*')
                    ->where('seo_id', $idSeo)
                    ->where('athlete_info_id', $idAthlete)
                    ->first();
                if (empty($relation)) {
                    RelationSeoAthleteInfo::insertItem([
                        'seo_id' => $idSeo,
                        'athlete_info_id' => $idAthlete,
                    ]);
                }

                AthleteAchievement::select('*')
                    ->where('athlete_info_id', $idAthlete)
                    ->delete();
                if (!empty($request->get('repeater_athlete_achievement'))) {
                    foreach ($request->get('repeater_athlete_achievement') as $index => $achi) {
                        if (!empty($achi['content'])) {
                            AthleteAchievement::insertItem([
                                'athlete_info_id' => $idAthlete,
                                'content' => $achi['content'],
                                'ordering' => $achi['ordering'] ?? $index,
                            ]);
                        }
                    }
                }

                AthleteSkill::select('*')
                    ->where('athlete_info_id', $idAthlete)
                    ->delete();
                if (!empty($request->get('repeater_athlete_skill'))) {
                    foreach ($request->get('repeater_athlete_skill') as $index => $skill) {
                        if (!empty($skill['skill']) && !empty($skill['percent'])) {
                            AthleteSkill::insertItem([
                                'athlete_info_id' => $idAthlete,
                                'skill' => $skill['skill'],
                                'percent' => $skill['percent'],
                                'ordering' => $skill['ordering'] ?? $index,
                            ]);
                        }
                    }
                }

                AthleteExperience::select('*')
                    ->where('athlete_info_id', $idAthlete)
                    ->delete();
                if (!empty($request->get('repeater_athlete_experience'))) {
                    foreach ($request->get('repeater_athlete_experience') as $index => $exper) {
                        if (!empty($exper['title']) && !empty($exper['company']) && !empty($exper['content'])) {
                            $idExp = AthleteExperience::insertItem([
                                'athlete_info_id' => $idAthlete,
                                'title' => $exper['title'],
                                'company' => $exper['company'],
                                'ordering' => $exper['ordering'] ?? $index,
                            ]);
                            $tmp = explode("\r\n", $exper['content']);
                            foreach ($tmp as $t) {
                                AthleteExperienceContent::insertItem([
                                    'athlete_experience_id' => $idExp,
                                    'content' => trim($t),
                                ]);
                            }
                        }
                    }
                }

                AthleteDegree::select('*')
                    ->where('athlete_info_id', $idAthlete)
                    ->delete();
                if (!empty($request->get('repeater_athlete_degree'))) {
                    foreach ($request->get('repeater_athlete_degree') as $index => $degree) {
                        if (!empty($degree['title']) && !empty($degree['school']) && !empty($degree['content'])) {
                            $idDeg = AthleteDegree::insertItem([
                                'athlete_info_id' => $idAthlete,
                                'title' => $degree['title'],
                                'school' => $degree['school'],
                                'ordering' => $degree['ordering'] ?? $index,
                            ]);
                            $tmp = explode("\r\n", $degree['content']);
                            foreach ($tmp as $t) {
                                AthleteDegreeContent::insertItem([
                                    'athlete_degree_id' => $idDeg,
                                    'content' => trim($t),
                                ]);
                            }
                        }
                    }
                }

                DB::commit();
                $message = [
                    'type' => 'success',
                    'message' => '<strong>Thành công!</strong> Đã cập nhật Vận động viên!',
                ];
                if (!empty($request->get('index_google')) && $request->get('index_google') == 'on') {
                    $flagIndex = IndexController::indexUrl($idSeo);
                    if ($flagIndex == 200) {
                        $message['message'] = '<strong>Thành công!</strong> Đã cập nhật Vận động viên và Báo Google Index!';
                    } else {
                        $message['message'] = '<strong>Thành công!</strong> Đã cập nhật Vận động viên <span style="color:red;">nhưng báo Google Index lỗi</span>';
                    }
                }
            }
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::error('AthleteController::createAndUpdate() ERROR', [
                'message' => $exception->getMessage(),
                'trace' => $exception->getTraceAsString(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
            ]);
        }
        if (empty($message)) {
            $message = [
                'type' => 'danger',
                'message' => '<strong>Thất bại!</strong> Có lỗi xảy ra, vui lòng thử lại',
            ];
        }
        $request->session()->put('message', $message);

        return redirect()->route('admin.athlete.view', ['id' => $idAthlete]);
    }

    public function delete(Request $request)
    {
        if (!empty($request->get('id'))) {
            try {
                DB::beginTransaction();
                $id = $request->get('id');
                $info = Athlete::select('*')
                    ->where('id', $id)
                    ->with('seo', 'seos', 'activityImages')
                    ->first();
                if (!empty($info->seo->image)) {
                    Upload::deleteWallpaper($info->seo->image);
                }
                foreach ($info->activityImages as $actImg) {
                    Upload::deleteWallpaper($actImg->image);
                }
                $info->activityImages()->delete();
                $info->achievements()->delete();
                $info->skills()->delete();
                foreach ($info->experiences as $e) {
                    $e->contents()->delete();
                }
                $info->experiences()->delete();
                foreach ($info->degrees as $d) {
                    $d->contents()->delete();
                }
                $info->degrees()->delete();
                foreach ($info->seos as $s) {
                    if (!empty($s->infoSeo->image)) {
                        Upload::deleteWallpaper($s->infoSeo->image);
                    }
                    if (!empty($s->infoSeo->contents)) {
                        foreach ($s->infoSeo->contents as $c) {
                            $c->delete();
                        }
                    }
                    $s->infoSeo()->delete();
                    $s->delete();
                }
                $slug = $info->seo->slug ?? null;
                if (!empty($slug)) {
                    $email = str_replace('-', '', $slug);
                    $user = User::where('email', $email)->first();
                    if (!empty($user)) {
                        UserRole::where('user_id', $user->id)->delete();
                        $user->delete();
                    }
                }
                $info->delete();
                DB::commit();

                return true;
            } catch (\Exception $exception) {
                DB::rollBack();

                return false;
            }
        }
    }
}
