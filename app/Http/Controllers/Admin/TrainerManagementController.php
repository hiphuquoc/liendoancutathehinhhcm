<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Helpers\Charactor;
use App\Models\Trainer;
use App\Models\Seo;
use App\Models\User;
use App\Models\UserRole;
use App\Services\BuildInsertUpdateModel;
use App\Http\Requests\TrainerRequest;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class TrainerManagementController extends Controller
{
    public function __construct(BuildInsertUpdateModel $BuildInsertUpdateModel)
    {
        $this->BuildInsertUpdateModel = $BuildInsertUpdateModel;
    }

    /**
     * Hiển thị trang upload Excel để cập nhật HLV
     */
    public function index()
    {
        return view('admin.trainerManagement.index');
    }

    /**
     * Tạo slug unique bằng cách thêm số thứ tự nếu trùng
     */
    private function generateUniqueSlug($baseSlug, $existingSlugs = [])
    {
        $slug = $baseSlug;
        $counter = 2;
        
        while (in_array($slug, $existingSlugs)) {
            // Tách phần cuối nếu đã có số (ví dụ: nguyen-van-a-2)
            if (preg_match('/^(.+)-(\d+)$/', $slug, $matches)) {
                $baseSlug = $matches[1];
                $counter = (int)$matches[2] + 1;
            }
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }
        
        return $slug;
    }

    /**
     * Tạo username duy nhất trong bảng users (bắt buộc unique)
     */
    private function generateUniqueUsername($baseUsername, $excludeUserId = null)
    {
        $username = str_replace('-', '', $baseUsername);
        $counter = 2;
        $baseUsernameClean = $username;
        
        // Kiểm tra username đã tồn tại trong bảng users chưa (bắt buộc unique)
        while (true) {
            $query = \App\Models\User::where('username', $username);
            if ($excludeUserId) {
                $query->where('id', '!=', $excludeUserId);
            }
            $existingUser = $query->first();
            
            if (!$existingUser) {
                // Username chưa tồn tại, có thể sử dụng
                break;
            }
            
            // Username đã tồn tại, tạo username mới với số
            if (preg_match('/^(.+?)(\d+)$/', $username, $matches)) {
                $baseUsernameClean = $matches[1];
                $counter = (int)$matches[2] + 1;
            }
            $username = $baseUsernameClean . $counter;
            $counter++;
        }
        
        return $username;
    }

    /**
     * Tạo trainer_code từ tháng/năm
     */
    private function generateTrainerCode($month, $year, $orderNumber)
    {
        $monthFormatted = 'T' . $month; // T01, T02, ..., T12
        $yearFormatted = $year; // 25, 26, etc.
        $federationCode = 'HWBF'; // Liên Đoàn Cử Tạ - Thể Hình HCM
        $orderNumberFormatted = str_pad($orderNumber, 3, '0', STR_PAD_LEFT); // 001, 002, etc.
        
        return "N.O:{$orderNumberFormatted}.{$monthFormatted}.{$yearFormatted}/HLV-{$federationCode}";
    }

    /**
     * Lấy số thứ tự tiếp theo cho trainer_code dựa trên tháng/năm
     * Tìm trainer_code có số thứ tự cao nhất trong cùng tháng/năm và trả về số tiếp theo
     */
    private function getNextOrderNumber($month, $year)
    {
        // Pattern để tìm trainer_code có dạng: N.O:XXX.T{month}.{year}/HLV-HWBF
        // Ví dụ: N.O:001.T01.26/HLV-HWBF
        $pattern = "%.T{$month}.{$year}/HLV-HWBF";
        
        // Tìm tất cả trainer có trainer_code khớp pattern
        $existingCodes = Trainer::where('trainer_code', 'LIKE', $pattern)
            ->pluck('trainer_code')
            ->toArray();
        
        if (empty($existingCodes)) {
            return 1;
        }
        
        // Trích xuất số thứ tự từ mỗi trainer_code và tìm max
        $maxOrderNumber = 0;
        foreach ($existingCodes as $code) {
            // Pattern: N.O:XXX.T...
            if (preg_match('/^N\.O:(\d+)\./', $code, $matches)) {
                $orderNumber = (int)$matches[1];
                if ($orderNumber > $maxOrderNumber) {
                    $maxOrderNumber = $orderNumber;
                }
            }
        }
        
        return $maxOrderNumber + 1;
    }

    /**
     * Xử lý upload Excel và tạo trainer + user đồng bộ
     */
    public function uploadExcel(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|mimes:xlsx,xls|max:10240', // Max 10MB
            'month' => 'required|string|size:2',
            'year' => 'required|string|size:2',
        ]);

        try {
            DB::beginTransaction();

            $file = $request->file('excel_file');
            $filePath = $file->store('temp', 'local');
            $fullPath = Storage::path($filePath);

            $month = $request->get('month');
            $year = $request->get('year');

            // Load file Excel
            $spreadsheet = IOFactory::load($fullPath);
            $worksheet = $spreadsheet->getActiveSheet();

            // Lấy parent seo (huan-luyen-vien)
            $parent = Seo::where('slug', config('main_' . env('APP_NAME') . '.slug_trainer_parent'))
                ->first();

            if (empty($parent)) {
                throw new \Exception('Không tìm thấy trang parent "huan-luyen-vien"');
            }

            // Lấy thông tin mẫu từ một trainer để copy achievements, skills, etc.
            $trainerExample = Trainer::whereHas('seo', function ($query) {
                $query->where('slug', 'cao-quoc-viet');
            })
                ->with('achievements', 'skills', 'experiences.contents', 'degrees.contents')
                ->first();

            $dataAchievements = [];
            $dataSkills = [];
            $dataExperiences = [];
            $dataDegrees = [];

            if ($trainerExample) {
                foreach ($trainerExample->achievements as $achi) {
                    if (!empty($achi->content)) {
                        $dataAchievements[] = ['content' => $achi->content];
                    }
                }

                foreach ($trainerExample->skills as $skill) {
                    if (!empty($skill->skill)) {
                        $dataSkills[] = [
                            'percent' => $skill->percent,
                            'skill' => $skill->skill,
                        ];
                    }
                }

                foreach ($trainerExample->experiences as $exper) {
                    if (!empty($exper->title) && !empty($exper->company)) {
                        $content = [];
                        foreach ($exper->contents as $t) {
                            $content[] = $t->content;
                        }
                        $dataExperiences[] = [
                            'title' => $exper->title,
                            'company' => $exper->company,
                            'content' => implode("\r\n", $content),
                        ];
                    }
                }

                foreach ($trainerExample->degrees as $degree) {
                    if (!empty($degree->title) && !empty($degree->school)) {
                        $content = [];
                        foreach ($degree->contents as $t) {
                            $content[] = $t->content;
                        }
                        $dataDegrees[] = [
                            'title' => $degree->title,
                            'school' => $degree->school,
                            'content' => implode("\r\n", $content),
                        ];
                    }
                }
            }

            // Đọc tất cả dữ liệu từ Excel trước để kiểm tra trùng
            // Cấu trúc: Cột 1 (STT), Cột 2 (Họ và Tên - BẮT BUỘC), Cột 3 (Ngày tháng năm sinh - tùy chọn), 
            //           Cột 4 (Số CCCD - tùy chọn), Cột 5 (Phone - tùy chọn), Cột 6 (Email - BẮT BUỘC), Cột 7 (Địa chỉ - tùy chọn)
            $trainersData = [];
            foreach ($worksheet->getRowIterator() as $rowIndex => $row) {
                if ($rowIndex < 5) continue; // Bỏ qua dòng tiêu đề

                $cells = [];
                foreach ($row->getCellIterator() as $cell) {
                    $cells[] = $cell->getCalculatedValue();
                }

                // Cột 1: STT (index 0) - bỏ qua
                // Cột 2: Họ và Tên (BẮT BUỘC) - index 1
                $name = trim($cells[1] ?? '');
                // Cột 3: Ngày tháng năm sinh (tùy chọn) - index 2
                $dob = trim($cells[2] ?? '');
                // Cột 4: Số CCCD (tùy chọn) - index 3
                $cccd = trim($cells[3] ?? '');
                // Cột 5: Phone (tùy chọn) - index 4
                $phone = trim($cells[4] ?? '');
                // Cột 6: Email (BẮT BUỘC) - index 5
                $email = trim($cells[5] ?? '');
                // Cột 7: Địa chỉ (tùy chọn) - index 6
                $address = trim($cells[6] ?? '');

                // Lưu tất cả dữ liệu, kể cả trường hợp thiếu name/email để xử lý lỗi sau
                $trainersData[] = [
                    'row' => $rowIndex,
                    'name' => $name,
                    'name_formatted' => !empty($name) ? mb_convert_case($name, MB_CASE_TITLE, 'UTF-8') : '',
                    'dob' => $dob, // Có thể trống
                    'cccd' => $cccd, // Có thể trống
                    'phone' => $phone, // Có thể trống
                    'email' => $email, // BẮT BUỘC
                    'address' => $address, // Có thể trống
                ];
            }

            // Kiểm tra trùng email/phone trong file Excel (chỉ với dữ liệu hợp lệ)
            $duplicateEmails = [];
            $duplicatePhones = [];
            $emailMap = [];
            $phoneMap = [];

            foreach ($trainersData as $index => $trainer) {
                $checkEmail = trim($trainer['email'] ?? '');
                $checkPhone = trim($trainer['phone'] ?? '');
                
                // Chỉ thêm vào map nếu email hợp lệ
                if (!empty($checkEmail) && filter_var($checkEmail, FILTER_VALIDATE_EMAIL)) {
                    $email = strtolower($checkEmail);
                    if (!isset($emailMap[$email])) {
                        $emailMap[$email] = [];
                    }
                    $emailMap[$email][] = $index;
                }

                // Chỉ thêm vào map nếu phone có giá trị
                if (!empty($checkPhone)) {
                    if (!isset($phoneMap[$checkPhone])) {
                        $phoneMap[$checkPhone] = [];
                    }
                    $phoneMap[$checkPhone][] = $index;
                }
            }

            foreach ($emailMap as $email => $indices) {
                if (count($indices) > 1) {
                    $duplicateEmails[$email] = array_map(function($idx) use ($trainersData) {
                        return !empty($trainersData[$idx]['name']) ? $trainersData[$idx]['name'] : '(Không có tên)';
                    }, $indices);
                }
            }

            foreach ($phoneMap as $phone => $indices) {
                if (count($indices) > 1) {
                    $duplicatePhones[$phone] = array_map(function($idx) use ($trainersData) {
                        return !empty($trainersData[$idx]['name']) ? $trainersData[$idx]['name'] : '(Không có tên)';
                    }, $indices);
                }
            }

            // Kiểm tra trùng với database (chỉ kiểm tra với email/phone hợp lệ)
            $existingEmails = [];
            $existingPhones = [];
            foreach ($trainersData as $trainer) {
                $checkEmail = trim($trainer['email'] ?? '');
                $checkPhone = trim($trainer['phone'] ?? '');
                
                // Chỉ kiểm tra email nếu email hợp lệ
                if (!empty($checkEmail) && filter_var($checkEmail, FILTER_VALIDATE_EMAIL)) {
                    $existing = Trainer::where('email', $checkEmail)->first();
                    if ($existing) {
                        if (!isset($existingEmails[$checkEmail])) {
                            $existingEmails[$checkEmail] = [];
                        }
                        $nameForDisplay = !empty($trainer['name']) ? $trainer['name'] : '(Không có tên)';
                        $existingEmails[$checkEmail][] = $nameForDisplay;
                    }
                }

                // Chỉ kiểm tra phone nếu phone có giá trị
                if (!empty($checkPhone)) {
                    $existing = Trainer::where('phone', $checkPhone)->first();
                    if ($existing) {
                        if (!isset($existingPhones[$checkPhone])) {
                            $existingPhones[$checkPhone] = [];
                        }
                        $nameForDisplay = !empty($trainer['name']) ? $trainer['name'] : '(Không có tên)';
                        $existingPhones[$checkPhone][] = $nameForDisplay;
                    }
                }
            }

            // Kết quả chi tiết
            $results = [];
            $successCount = 0;
            $duplicateCount = 0;
            $errorCount = 0;
            
            // Lấy số thứ tự tiếp theo từ database (đếm tiếp từ số cuối cùng của tháng/năm)
            $orderNumber = $this->getNextOrderNumber($month, $year);

            // Lấy danh sách slug hiện có để kiểm tra trùng
            $existingSlugs = Seo::where('type', 'trainer_info')
                ->where('language', 'vi')
                ->pluck('slug')
                ->toArray();

            // Xử lý từng trainer
            foreach ($trainersData as $trainerData) {
                $name = trim($trainerData['name'] ?? '');
                $email = trim($trainerData['email'] ?? '');
                $phone = trim($trainerData['phone'] ?? '');
                $cccd = trim($trainerData['cccd'] ?? '');

                // VALIDATION: Kiểm tra các trường bắt buộc
                $validationErrors = [];
                if (empty($name)) {
                    $validationErrors[] = 'Họ và Tên không được để trống';
                }
                if (empty($email)) {
                    $validationErrors[] = 'Email không được để trống';
                } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $validationErrors[] = 'Email không hợp lệ';
                }

                // Nếu thiếu Name hoặc Email → chuyển sang error
                if (!empty($validationErrors)) {
                    $errorCount++;
                    $nameDisplay = !empty($name) ? $name : '(Không có tên)';
                    $results[] = [
                        'status' => 'error',
                        'name' => $nameDisplay,
                        'phone' => $phone ?: 'N/A',
                        'email' => $email ?: 'N/A',
                        'trainer_code' => null,
                        'slug' => null,
                        'error' => implode(', ', $validationErrors),
                        'qr_code' => null,
                    ];
                    continue;
                }

                // Format tên: viết hoa chữ cái đầu mỗi từ (hỗ trợ Unicode/tiếng Việt)
                $nameCover = mb_convert_case($name, MB_CASE_TITLE, 'UTF-8');
                
                $baseSlug = Charactor::convertStrToUrl($nameCover);
                $slug = $this->generateUniqueSlug($baseSlug, $existingSlugs);
                $existingSlugs[] = $slug; // Thêm vào danh sách để tránh trùng trong cùng batch

                // Kiểm tra trùng
                $isDuplicate = false;
                $duplicateReasons = [];

                // Kiểm tra trùng email trong file (chỉ khi email hợp lệ)
                if (!empty($email) && isset($duplicateEmails[strtolower($email)])) {
                    $isDuplicate = true;
                    $duplicateReasons[] = 'Email trùng trong file: ' . implode(', ', $duplicateEmails[strtolower($email)]);
                }

                // Kiểm tra trùng phone trong file (chỉ khi phone có giá trị)
                if (!empty($phone) && isset($duplicatePhones[$phone])) {
                    $isDuplicate = true;
                    $duplicateReasons[] = 'Số điện thoại trùng trong file: ' . implode(', ', $duplicatePhones[$phone]);
                }

                // Kiểm tra trùng với database
                if (!empty($email) && isset($existingEmails[$email])) {
                    $isDuplicate = true;
                    $duplicateReasons[] = 'Email đã tồn tại trong hệ thống';
                }

                if (!empty($phone) && isset($existingPhones[$phone])) {
                    $isDuplicate = true;
                    $duplicateReasons[] = 'Số điện thoại đã tồn tại trong hệ thống';
                }

                if ($isDuplicate) {
                    $duplicateCount++;
                    $results[] = [
                        'status' => 'duplicate',
                        'name' => $nameCover,
                        'phone' => $phone ?: 'N/A',
                        'email' => $email,
                        'trainer_code' => null,
                        'slug' => $slug,
                        'reasons' => $duplicateReasons,
                        'qr_code' => null,
                    ];
                    continue;
                }

                // Tạo trainer
                try {
                    $trainerCode = $this->generateTrainerCode($month, $year, $orderNumber);
                    $orderNumber++;

                    // Tạo SEO data
                    $seoTitle = "Huấn luyện viên {$nameCover} của Liên Đoàn Cử Tạ - Thể Hình HCM | liendoancutathehinhhcm";
                    $seoData = [
                        'seo_id' => 0,
                        'seo_id_vi' => 0,
                        'trainer_info_id' => 0,
                        'language' => 'vi',
                        'type' => 'copy',
                        'parent' => $parent->id,
                        'rating_aggregate_count' => '8452',
                        'rating_aggregate_star' => '4.7',
                        'title' => $nameCover,
                        'name' => $nameCover,
                        'position' => 'Huấn luyện viên cá nhân (PT)',
                        'phone' => $phone,
                        'email' => $email,
                        'seo_title' => $seoTitle,
                        'seo_description' => 'Viết 1 đoạn giới thiệu về bạn!',
                        'description' => 'Viết 1 đoạn giới thiệu về bạn!',
                        'slug' => $slug,
                        'repeater_trainer_achievement' => $dataAchievements,
                        'repeater_trainer_skill' => $dataSkills,
                        'repeater_trainer_experience' => $dataExperiences,
                        'repeater_trainer_degree' => $dataDegrees,
                    ];

                    // Tạo request object để sử dụng TrainerRequest validation
                    $trainerRequest = TrainerRequest::create(
                        route('admin.trainer.view'),
                        'POST',
                        $seoData
                    );
                    $trainerRequest->setLaravelSession(session());

                    // Gọi TrainerController để tạo trainer
                    $trainerController = app(\App\Http\Controllers\Admin\TrainerController::class);
                    $result = $trainerController->createAndUpdate($trainerRequest);

                    // Lấy trainer vừa tạo
                    $trainer = Trainer::whereHas('seo', function ($query) use ($slug) {
                        $query->where('slug', $slug);
                    })->first();

                    if ($trainer) {
                        // Cập nhật trainer_code
                        $trainer->trainer_code = $trainerCode;
                        $trainer->save();

                        // Tạo user cho trainer (trainer mới tạo sẽ không có user_id)
                        // Kiểm tra email và username (từ slug) để tìm user đã tồn tại
                        $baseUsername = str_replace('-', '', strtolower($slug));
                        $existingUserByEmail = User::where('email', $email)->first();
                        $existingUserByUsername = User::where('username', $baseUsername)->first();
                        
                        // Ưu tiên email, sau đó username
                        $existingUser = $existingUserByEmail ?: $existingUserByUsername;
                        
                        if ($existingUser) {
                            // User đã tồn tại - có thể là cùng 1 người với chức vụ khác (Referee) hoặc username trùng
                            // Cho phép dùng chung user (cùng 1 người có 2 chức vụ)
                            
                            // Kiểm tra xem user này đã được sử dụng bởi Trainer chưa
                            $trainerUsingUser = \App\Models\Trainer::where('user_id', $existingUser->id)
                                ->where('id', '!=', $trainer->id) // Loại trừ chính trainer hiện tại
                                ->first();
                            
                            if ($trainerUsingUser) {
                                // User đã được sử dụng bởi Trainer (cùng chức vụ) - không cho phép
                                // Xóa trainer đã tạo và báo lỗi
                                if ($trainer->seo_id) {
                                    $seo = \App\Models\Seo::find($trainer->seo_id);
                                    if ($seo) {
                                        $seo->delete();
                                    }
                                }
                                $trainer->delete();
                                
                                $duplicateCount++;
                                $results[] = [
                                    'status' => 'duplicate',
                                    'name' => $nameCover,
                                    'phone' => $phone ?: 'N/A',
                                    'email' => $email,
                                    'trainer_code' => null,
                                    'slug' => $slug,
                                    'reasons' => ['Email/Username đã tồn tại trong hệ thống (Huấn luyện viên)'],
                                    'qr_code' => null,
                                ];
                                continue; // Skip và tiếp tục với record tiếp theo
                            } else {
                                // User chưa được sử dụng bởi Trainer - có thể dùng chung (cùng 1 người, 2 chức vụ)
                                // Thêm role trainer cho user nếu chưa có
                                $trainerRoleId = \App\Models\Role::where('slug', 'trainer')->value('id');
                                if ($trainerRoleId) {
                                    $existingRole = \App\Models\UserRole::where('user_id', $existingUser->id)
                                        ->where('role_id', $trainerRoleId)
                                        ->first();
                                    if (!$existingRole) {
                                        \App\Models\UserRole::insertItem([
                                            'user_id' => $existingUser->id,
                                            'role_id' => $trainerRoleId,
                                        ]);
                                    }
                                }
                                
                                // Cập nhật role của user thành trainer (hoặc giữ nguyên nếu đã có referee role)
                                if (!$existingUser->hasRole('trainer')) {
                                    $existingUser->role = 'trainer';
                                    $existingUser->save();
                                }
                                
                                $trainer->user_id = $existingUser->id;
                                $trainer->save();
                                // Tiếp tục xử lý QR code và kết quả (không continue)
                            }
                        } else {
                            // Email và username chưa tồn tại - tạo user mới
                            // Username từ slug (bỏ dấu -)
                            $username = $baseUsername;
                            
                            // Tạo user mới
                            $user = User::create([
                                'name' => $nameCover,
                                'email' => $email,
                                'username' => $username,
                                'password' => Hash::make($username),
                                'position' => 'Huấn luyện viên cá nhân (PT)',
                                'phone' => $phone,
                                'role' => 'trainer',
                            ]);

                            // Gán role trainer
                            $trainerRoleId = \App\Models\Role::where('slug', 'trainer')->value('id');
                            if ($trainerRoleId) {
                                UserRole::insertItem([
                                    'user_id' => $user->id,
                                    'role_id' => $trainerRoleId,
                                ]);
                            }

                            // Cập nhật user_id vào trainer
                            $trainer->user_id = $user->id;
                            $trainer->save();

                            // Đồng bộ name, position, phone, email từ trainer sang user
                            $user->name = $trainer->name;
                            $user->position = $trainer->position;
                            $user->phone = $trainer->phone;
                                $user->email = $trainer->email;
                                $user->save();
                            }
                        }

                        // Tạo QR code
                        $parentSlug = config('main_' . env('APP_NAME') . '.slug_trainer_parent', 'huan-luyen-vien');
                        $seo = $trainer->seo;
                        if ($seo) {
                            if (!empty($seo->slug_full)) {
                                $url = url('/' . $seo->slug_full);
                            } elseif (!empty($seo->slug)) {
                                $url = url('/' . $parentSlug . '/' . $seo->slug);
                            } else {
                                $url = '';
                            }

                            if ($url) {
                                // Cast to string vì QrCode::generate() trả về HtmlString object
                                $qrCodeSvg = (string) QrCode::encoding('UTF-8')
                                    ->format('svg')
                                    ->size(120)
                                    ->margin(1)
                                    ->generate($url);
                            } else {
                                $qrCodeSvg = null;
                            }
                        } else {
                            $qrCodeSvg = null;
                        }

                        $successCount++;
                        $results[] = [
                            'status' => 'success',
                            'name' => $nameCover,
                            'phone' => $phone,
                            'email' => $email,
                            'trainer_code' => $trainerCode,
                            'slug' => $slug,
                            'qr_code' => $qrCodeSvg,
                            'url' => $url ?? '',
                        ];
                    } else {
                        throw new \Exception('Không thể tạo trainer');
                    }
                } catch (\Exception $e) {
                    $errorCount++;
                    Log::error("TrainerManagement uploadExcel error for {$nameCover}: " . $e->getMessage());
                    $results[] = [
                        'status' => 'error',
                        'name' => $nameCover,
                        'phone' => $phone,
                        'email' => $email,
                        'trainer_code' => null,
                        'slug' => $slug,
                        'error' => $e->getMessage(),
                        'qr_code' => null,
                    ];
                }
            }

            // Xóa file tạm
            Storage::delete($filePath);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => "Đã xử lý: {$successCount} thành công, {$duplicateCount} trùng, {$errorCount} lỗi",
                'success_count' => $successCount,
                'duplicate_count' => $duplicateCount,
                'error_count' => $errorCount,
                'duplicate_emails' => $duplicateEmails,
                'duplicate_phones' => $duplicatePhones,
                'existing_emails' => $existingEmails,
                'existing_phones' => $existingPhones,
                'results' => $results,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            if (isset($filePath)) {
                Storage::delete($filePath);
            }

            Log::error("TrainerManagement uploadExcel error: " . $e->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'Lỗi khi xử lý file Excel: ' . $e->getMessage(),
            ], 500);
        }
    }
}
