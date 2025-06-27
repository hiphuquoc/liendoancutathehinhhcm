<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use PhpOffice\PhpSpreadsheet\IOFactory;

use App\Http\Requests\TrainerRequest;
use Illuminate\Support\Facades\Storage;
use App\Helpers\Charactor;
use App\Models\Seo;
use App\Models\Trainer;
use App\Http\Controllers\Admin\TrainerController;

class CreateTrainer extends Command {
    protected $signature = 'trainer:create';

    protected $description = 'Command description';

    public function handle(){
        // Đường dẫn file trong thư mục storage
        $filePath = Storage::path('public/danh-sach-hlv-new.xlsx'); // Thay "your-folder" bằng tên thư mục trong storage

        // Load file Excel bằng PhpSpreadsheet
        $spreadsheet = IOFactory::load($filePath);
        $worksheet = $spreadsheet->getActiveSheet();

        $trainers = [];

        // Bỏ qua dòng tiêu đề (nếu có) và chuyển thành mảng huấn luyện viên
        $trainers = [];
        foreach ($worksheet->getRowIterator() as $rowIndex => $row) {
            if ($rowIndex < 5) continue; // Bỏ qua dòng tiêu đề (Excel đếm từ 1)
        
            $cells = [];
            foreach ($row->getCellIterator() as $cell) {
                $cells[] = $cell->getCalculatedValue(); // ✅ Lấy giá trị đã tính toán
            }
        
            $brithDay   = $cells[2] ?? '';
            $tmp        = explode('/', $brithDay);
            $brithDay   = end($tmp);
            $slug       = Charactor::convertStrToUrl(strtolower($cells[1]));
        
            $trainers[] = [
                'name'          => ucwords($cells[1]) ?? '',
                // 'birth_day'     => $brithDay, 
                'phone'         => $cells[4] ?? '0912345678',   
                // 'email'         => $cells[4] ?? '',
                // 'address'       => $cells[5] ?? '',
                // 'sex'           => $cells[7] ?? '', // ✅ Lấy được giá trị thay vì công thức
                // 'class_name'    => $cells[9] ?? '', // ✅ Lấy được giá trị thay vì công thức
                // 'link'          => env('APP_URL').'/huan-luyen-vien/'.$slug,
            ];
        }

        // dd($trainers);

        $count          = 0;
        /* lấy id trang parent (huan-luyen-vien) */
        $parent         = Seo::select('*')
                            ->where('slug', config('main_'.env('APP_NAME').'.slug_trainer_parent'))
                            ->first();
        /* lấy thông tin 1 HLV để làm mẫu */
        $trainerExample = Trainer::select('*')
                            ->whereHas('seo', function($query){
                                $query->where('slug', 'cao-quoc-viet');
                            })
                            ->orderBy('id', 'ASC')
                            ->with('achievements', 'skills', 'experiences', 'degrees')
                            ->first();
        $dataAchievements = [];
        $i                = 0;
        foreach($trainerExample->achievements as $achi){
            if(!empty($achi->content)){
                $dataAchievements[$i]['content'] = $achi->content;
                ++$i;
            }
        }
        $dataSkills         = [];
        $i                  = 0;
        foreach($trainerExample->skills as $skill){
            if(!empty($skill->skill)){
                $dataSkills[$i]['percent'] = $skill->percent;
                $dataSkills[$i]['skill'] = $skill->skill;
                ++$i;
            }
        }
        $dataExperiences    = [];
        $i                  = 0;
        foreach($trainerExample->experiences as $exper){
            if(!empty($exper->title)&&!empty($exper->company)){
                $dataExperiences[$i]['title'] = $exper->title;
                $dataExperiences[$i]['company'] = $exper->company;
                $tmp = [];
                foreach($exper->contents as $t){
                    $tmp[] = $t->content;
                }
                $dataExperiences[$i]['content'] = implode("\r\n", $tmp);
                ++$i;
            }
        }
        $dataDegrees        = [];
        $i                  = 0;
        foreach($trainerExample->degrees as $degree){
            if(!empty($degree->title)&&!empty($degree->school)){
                $dataDegrees[$i]['title'] = $degree->title;
                $dataDegrees[$i]['school'] = $degree->school;
                $tmp = [];
                foreach($degree->contents as $t){
                    $tmp[] = $t->content;
                }
                $dataDegrees[$i]['content'] = implode("\r\n", $tmp);
                ++$i;
            }
        }
        foreach($trainers as $trainer){
            if(!empty($trainer['name'])){
                $nameCover  = ucwords(mb_strtolower($trainer['name'], 'UTF-8'));
                // insert dữ liệu
                $seoTitle   = "Huẩn luyện viên ".$nameCover." của Liên Đoàn Cử Tạ - Thể Hình HCM | liendoancutathehinhhcm";
                $slug       = \App\Helpers\Charactor::convertStrToUrl($nameCover);
                /* data tổng */
                $data = [
                    "seo_id" => 0,
                    "seo_id_vi" => 0,
                    "trainer_info_id" => 0,
                    "language" => "vi",
                    "type" => "copy",
                    "parent" => $parent->id,
                    "rating_aggregate_count" => "8452",
                    "rating_aggregate_star" => "4.7",
                    /* biến số */
                    "title" => $nameCover,
                    "phone" => $trainer['phone'],
                    "email" => $slug.'@liendoancutathehinhhcm.com.vn',
                    "seo_title" => $seoTitle,
                    "seo_description" => 'Viết 1 đoạn giới thiệu về bạn!',
                    "slug" => $slug,
                    "repeater_trainer_achievement" => $dataAchievements,
                    "repeater_trainer_skill" => $dataSkills,
                    "repeater_trainer_experience" => $dataExperiences,
                    "repeater_trainer_degree" => $dataDegrees,
                ];

                dispatch(function () use ($data) {
                    $request = TrainerRequest::create(route('admin.trainer.view'), 'POST', $data);
                    $request->setLaravelSession(session());
                    
                    $controller = app(TrainerController::class);
                    $controller->createAndUpdate($request);
                });
            }
        }
        
        $this->info("🎉 Đã tạo {$count} vận động viên thành công!");
    }
}
