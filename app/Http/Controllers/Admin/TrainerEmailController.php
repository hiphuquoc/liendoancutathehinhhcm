<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\Trainer;
use App\Models\User;
use App\Mail\TrainerAccountMail;

class TrainerEmailController extends Controller
{
    /**
     * Hiển thị danh sách HLV để gửi email
     */
    public function index(Request $request)
    {
        $query = Trainer::with(['seo', 'user'])
            ->whereHas('seo', function ($q) {
                $q->where('type', 'trainer_info')
                  ->where('language', 'vi');
            })
            ->whereNotNull('user_id'); // Chỉ lấy trainer đã có tài khoản

        // Mặc định: không hiển thị kết quả nếu không có filter hoặc search
        $courseFilter = $request->get('course');
        $search = $request->get('search');
        
        // Chỉ query nếu có filter hoặc search
        if (!empty($courseFilter) || !empty($search)) {
            // Bộ lọc theo khóa học (mã tháng năm trong trainer_code)
            if (!empty($courseFilter)) {
                // Format: T12.25 (tháng.năm)
                $query->where('trainer_code', 'like', '%' . $courseFilter . '%');
            }

            // Tìm kiếm theo tên
            if (!empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%')
                      ->orWhere('trainer_code', 'like', '%' . $search . '%')
                      ->orWhere('email', 'like', '%' . $search . '%')
                      ->orWhereHas('seo', function ($subQ) use ($search) {
                          $subQ->where('title', 'like', '%' . $search . '%');
                      });
                });
            }

            $trainers = $query->orderBy('id', 'DESC')->get();
            
            // Thêm thông tin URL và user info
            $parentSlug = config('main_' . env('APP_NAME') . '.slug_trainer_parent', 'huan-luyen-vien');
            foreach ($trainers as $trainer) {
                if (!empty($trainer->seo->slug_full)) {
                    $trainer->profile_url = url('/' . $trainer->seo->slug_full);
                } elseif (!empty($trainer->seo->slug)) {
                    $trainer->profile_url = url('/' . $parentSlug . '/' . $trainer->seo->slug);
                } else {
                    $trainer->profile_url = url('/');
                }
                
                // Lấy thông tin user nếu có
                if ($trainer->user) {
                    $trainer->username = $trainer->user->username;
                    $trainer->login_url = url('/he-thong');
                }
            }
        } else {
            // Không có filter, trả về collection rỗng
            $trainers = collect();
        }

        // Lấy danh sách các khóa học (từ trainer_code)
        $courses = Trainer::whereNotNull('trainer_code')
            ->where('trainer_code', '!=', '')
            ->pluck('trainer_code')
            ->map(function ($code) {
                // Extract phần TMM.YY từ trainer_code
                if (preg_match('/\.(T\d{2}\.\d{2})\//', $code, $matches)) {
                    return $matches[1];
                }
                return null;
            })
            ->filter()
            ->unique()
            ->map(function ($code) {
                // Parse để sắp xếp: T12.25 -> month=12, year=25
                if (preg_match('/T(\d{2})\.(\d{2})/', $code, $matches)) {
                    return [
                        'code' => $code,
                        'month' => (int)$matches[1],
                        'year' => (int)$matches[2],
                        'sort_key' => (int)$matches[2] * 100 + (int)$matches[1]
                    ];
                }
                return ['code' => $code, 'month' => 0, 'year' => 0, 'sort_key' => 0];
            })
            ->sortByDesc('sort_key')
            ->pluck('code')
            ->values();

        return view('admin.trainerEmail.index', compact('trainers', 'courses', 'courseFilter', 'search'));
    }

    /**
     * Gửi email thông tin tài khoản cho các HLV đã chọn
     */
    public function sendEmails(Request $request)
    {
        $request->validate([
            'trainer_ids' => 'required|array|min:1',
            'trainer_ids.*' => 'required|integer|exists:trainer_info,id',
        ], [
            'trainer_ids.required' => 'Vui lòng chọn ít nhất một HLV',
            'trainer_ids.min' => 'Vui lòng chọn ít nhất một HLV',
        ]);

        try {
            $trainerIds = $request->input('trainer_ids', []);
            
            // Lấy danh sách trainers với user info
            $trainers = Trainer::with(['seo', 'user'])
                ->whereIn('id', $trainerIds)
                ->whereNotNull('user_id')
                ->whereNotNull('email')
                ->get();

            if ($trainers->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy HLV nào có đầy đủ thông tin để gửi email',
                ], 400);
            }

            $parentSlug = config('main_' . env('APP_NAME') . '.slug_trainer_parent', 'huan-luyen-vien');
            $successCount = 0;
            $failCount = 0;
            $errors = [];

            foreach ($trainers as $trainer) {
                try {
                    // Kiểm tra email và user
                    if (empty($trainer->email) || empty($trainer->user)) {
                        $failCount++;
                        $errors[] = "HLV {$trainer->name}: Không có email hoặc tài khoản";
                        continue;
                    }

                    // Tạo URL profile
                    if (!empty($trainer->seo->slug_full)) {
                        $profileUrl = url('/' . $trainer->seo->slug_full);
                    } elseif (!empty($trainer->seo->slug)) {
                        $profileUrl = url('/' . $parentSlug . '/' . $trainer->seo->slug);
                    } else {
                        $profileUrl = url('/');
                    }

                    // Gửi email
                    Mail::to($trainer->email)->send(new TrainerAccountMail(
                        $trainer->name,
                        $trainer->email,
                        $trainer->user->username,
                        $trainer->trainer_code,
                        $profileUrl,
                        url('/he-thong'),
                        route('admin.account.trainerProfile')
                    ));

                    $successCount++;
                    
                } catch (\Exception $e) {
                    $failCount++;
                    $errors[] = "HLV {$trainer->name}: " . $e->getMessage();
                    Log::error("TrainerEmail sendEmails error for trainer {$trainer->id}: " . $e->getMessage());
                }
            }

            $message = "Đã gửi email thành công cho {$successCount} HLV";
            if ($failCount > 0) {
                $message .= ", {$failCount} HLV gửi thất bại";
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'success_count' => $successCount,
                'fail_count' => $failCount,
                'errors' => $errors,
            ]);

        } catch (\Exception $e) {
            Log::error("TrainerEmail sendEmails error: " . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi gửi email: ' . $e->getMessage(),
            ], 500);
        }
    }
}

