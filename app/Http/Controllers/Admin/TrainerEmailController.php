<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Trainer;
use App\Jobs\SendTrainerAccountEmailJob;

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
     * Đưa email thông tin tài khoản cho các HLV đã chọn vào hàng đợi (queue).
     * API luôn trả về JSON để tránh lỗi parse HTML trên frontend.
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
            $trainerIds = array_unique($request->input('trainer_ids', []));

            $trainers = Trainer::with(['seo', 'user'])
                ->whereIn('id', $trainerIds)
                ->whereNotNull('user_id')
                ->whereNotNull('email')
                ->get();

            if ($trainers->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy HLV nào có đầy đủ thông tin để gửi email.',
                ], 400);
            }

            $queuedCount = 0;
            foreach ($trainers as $trainer) {
                if (empty($trainer->email) || empty($trainer->user)) {
                    continue;
                }
                SendTrainerAccountEmailJob::dispatch($trainer->id);
                $queuedCount++;
            }

            if ($queuedCount === 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không có HLV nào đủ điều kiện (email + tài khoản) để gửi.',
                ], 400);
            }

            $message = "Đã đưa {$queuedCount} email vào hàng đợi. Email sẽ được gửi trong giây lát.";

            return response()->json([
                'success' => true,
                'message' => $message,
                'queued_count' => $queuedCount,
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('TrainerEmail sendEmails: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Lỗi hệ thống: ' . $e->getMessage(),
            ], 500);
        }
    }
}

