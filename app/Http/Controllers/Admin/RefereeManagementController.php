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
use App\Models\Referee;
use App\Models\Seo;
use App\Models\User;
use App\Models\UserRole;
use App\Services\BuildInsertUpdateModel;
use App\Http\Requests\RefereeRequest;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class RefereeManagementController extends Controller
{
    public function __construct(BuildInsertUpdateModel $BuildInsertUpdateModel)
    {
        $this->BuildInsertUpdateModel = $BuildInsertUpdateModel;
    }

    /**
     * Hiển thị trang upload Excel để cập nhật Trọng tài
     */
    public function index()
    {
        return view('admin.refereeManagement.index');
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
     * Xử lý upload Excel và tạo referee + user đồng bộ
     */
    public function uploadExcel(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|mimes:xlsx,xls|max:10240', // Max 10MB
        ]);

        try {
            DB::beginTransaction();

            $file = $request->file('excel_file');
            $filePath = $file->store('temp', 'local');
            $fullPath = Storage::path($filePath);

            // Load file Excel
            $spreadsheet = IOFactory::load($fullPath);
            $worksheet = $spreadsheet->getActiveSheet();

            // Lấy parent seo (trong-tai)
            $parent = Seo::where('slug', 'trong-tai')
                ->first();

            if (empty($parent)) {
                throw new \Exception('Không tìm thấy trang parent "trong-tai"');
            }

            // Lấy thông tin mẫu từ một referee để copy achievements, skills, etc.
            $refereeExample = Referee::with('achievements', 'skills', 'experiences.contents', 'degrees.contents')
                ->first();

            $dataAchievements = [];
            $dataSkills = [];
            $dataExperiences = [];
            $dataDegrees = [];

            if ($refereeExample) {
                foreach ($refereeExample->achievements as $achi) {
                    if (!empty($achi->content)) {
                        $dataAchievements[] = ['content' => $achi->content];
                    }
                }

                foreach ($refereeExample->skills as $skill) {
                    if (!empty($skill->skill)) {
                        $dataSkills[] = [
                            'percent' => $skill->percent,
                            'skill' => $skill->skill,
                        ];
                    }
                }

                foreach ($refereeExample->experiences as $exper) {
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

                foreach ($refereeExample->degrees as $degree) {
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
            $refereesData = [];
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
                $refereesData[] = [
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

            foreach ($refereesData as $index => $referee) {
                $checkEmail = trim($referee['email'] ?? '');
                $checkPhone = trim($referee['phone'] ?? '');
                
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
                    $duplicateEmails[$email] = array_map(function($idx) use ($refereesData) {
                        return !empty($refereesData[$idx]['name']) ? $refereesData[$idx]['name'] : '(Không có tên)';
                    }, $indices);
                }
            }

            foreach ($phoneMap as $phone => $indices) {
                if (count($indices) > 1) {
                    $duplicatePhones[$phone] = array_map(function($idx) use ($refereesData) {
                        return !empty($refereesData[$idx]['name']) ? $refereesData[$idx]['name'] : '(Không có tên)';
                    }, $indices);
                }
            }

            // Kiểm tra trùng với database (chỉ kiểm tra với email/phone hợp lệ)
            $existingEmails = [];
            $existingPhones = [];
            foreach ($refereesData as $referee) {
                $checkEmail = trim($referee['email'] ?? '');
                $checkPhone = trim($referee['phone'] ?? '');
                
                // Chỉ kiểm tra email nếu email hợp lệ
                if (!empty($checkEmail) && filter_var($checkEmail, FILTER_VALIDATE_EMAIL)) {
                    $existing = Referee::where('email', $checkEmail)->first();
                    if ($existing) {
                        if (!isset($existingEmails[$checkEmail])) {
                            $existingEmails[$checkEmail] = [];
                        }
                        $nameForDisplay = !empty($referee['name']) ? $referee['name'] : '(Không có tên)';
                        $existingEmails[$checkEmail][] = $nameForDisplay;
                    }
                }

                // Chỉ kiểm tra phone nếu phone có giá trị
                if (!empty($checkPhone)) {
                    $existing = Referee::where('phone', $checkPhone)->first();
                    if ($existing) {
                        if (!isset($existingPhones[$checkPhone])) {
                            $existingPhones[$checkPhone] = [];
                        }
                        $nameForDisplay = !empty($referee['name']) ? $referee['name'] : '(Không có tên)';
                        $existingPhones[$checkPhone][] = $nameForDisplay;
                    }
                }
            }

            // Kết quả chi tiết
            $results = [];
            $successCount = 0;
            $duplicateCount = 0;
            $errorCount = 0;
            
            // Lấy danh sách slug hiện có để kiểm tra trùng
            $existingSlugs = DB::table('seo')
                ->join('referee_info', 'referee_info.seo_id', '=', 'seo.id')
                ->pluck('seo.slug')
                ->toArray();

            // Xử lý từng referee
            foreach ($refereesData as $refereeData) {
                $name = trim($refereeData['name'] ?? '');
                $email = trim($refereeData['email'] ?? '');
                $phone = trim($refereeData['phone'] ?? '');
                $cccd = trim($refereeData['cccd'] ?? '');

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
                        'slug' => $slug,
                        'reasons' => $duplicateReasons,
                        'qr_code' => null,
                    ];
                    continue;
                }

                // Tạo referee
                try {
                    // Tạo SEO data
                    $seoTitle = "Trọng tài {$nameCover} của Liên Đoàn Cử Tạ - Thể Hình HCM | liendoancutathehinhhcm";
                    $seoData = [
                        'seo_id' => 0,
                        'seo_id_vi' => 0,
                        'referee_info_id' => 0,
                        'language' => 'vi',
                        'type' => 'copy',
                        'parent' => $parent->id,
                        'rating_aggregate_count' => '8452',
                        'rating_aggregate_star' => '4.7',
                        'title' => $nameCover,
                        'name' => $nameCover,
                        'position' => 'Trọng tài',
                        'phone' => $phone,
                        'email' => $email,
                        'seo_title' => $seoTitle,
                        'seo_description' => 'Viết 1 đoạn giới thiệu về bạn!',
                        'description' => 'Viết 1 đoạn giới thiệu về bạn!',
                        'slug' => $slug,
                        'repeater_referee_achievement' => $dataAchievements,
                        'repeater_referee_skill' => $dataSkills,
                        'repeater_referee_experience' => $dataExperiences,
                        'repeater_referee_degree' => $dataDegrees,
                    ];

                    // Tạo request object để sử dụng RefereeRequest validation
                    $refereeRequest = RefereeRequest::create(
                        route('admin.referee.view'),
                        'POST',
                        $seoData
                    );
                    $refereeRequest->setLaravelSession(session());

                    // Gọi RefereeController để tạo referee
                    $refereeController = app(\App\Http\Controllers\Admin\RefereeController::class);
                    $result = $refereeController->createAndUpdate($refereeRequest);

                    // Lấy referee vừa tạo
                    $referee = Referee::whereHas('seo', function ($query) use ($slug) {
                        $query->where('slug', $slug);
                    })->first();

                    if ($referee) {
                        // Tạo user cho referee (liên kết qua slug/email, không có user_id field)
                        $username = str_replace('-', '', $slug);
                        
                        // Kiểm tra email/username đã tồn tại chưa
                        $existingUser = User::where('email', $email)
                            ->orWhere('username', $username)
                            ->first();

                        if (!$existingUser) {
                            $user = User::create([
                                'name' => $nameCover,
                                'email' => $email,
                                'username' => $username,
                                'password' => Hash::make($username),
                                'position' => 'Trọng tài',
                                'phone' => $phone,
                                'role' => 'sub-admin',
                            ]);

                            // Gán role sub-admin
                            UserRole::insertItem([
                                'user_id' => $user->id,
                                'role_id' => 2,
                            ]);

                            // Đồng bộ name, position, phone, email từ referee sang user
                            $user->name = $referee->name;
                            $user->position = $referee->position ?? 'Trọng tài';
                            $user->phone = $referee->phone;
                            $user->email = $referee->email;
                            $user->save();
                        }

                        // Tạo QR code
                        $parentSlug = 'trong-tai';
                        $seo = $referee->seo;
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
                            'slug' => $slug,
                            'qr_code' => $qrCodeSvg,
                            'url' => $url ?? '',
                        ];
                    } else {
                        throw new \Exception('Không thể tạo referee');
                    }
                } catch (\Exception $e) {
                    $errorCount++;
                    Log::error("RefereeManagement uploadExcel error for {$nameCover}: " . $e->getMessage());
                    $results[] = [
                        'status' => 'error',
                        'name' => $nameCover,
                        'phone' => $phone,
                        'email' => $email,
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

            Log::error("RefereeManagement uploadExcel error: " . $e->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'Lỗi khi xử lý file Excel: ' . $e->getMessage(),
            ], 500);
        }
    }
}

