<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Models\Trainer;
use App\Jobs\SendTrainerAccountEmailJob;
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
                // Tự động tạo mật khẩu mới ngẫu nhiên, lưu DB và gửi qua email cho người dùng
                $newPassword = \Illuminate\Support\Str::random(8);
                $trainer->user->password = \Illuminate\Support\Facades\Hash::make($newPassword);
                $trainer->user->save();

                SendTrainerAccountEmailJob::dispatch($trainer->id, $newPassword);
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

    /**
     * Gửi email test thông tin tài khoản HLV đến một địa chỉ tùy chọn (gửi trực tiếp, không queue).
     * Luôn trả về JSON.
     */
    public function sendTestEmail(Request $request)
    {
        $request->validate([
            'trainer_id' => 'required|integer|exists:trainer_info,id',
            'test_email' => 'required|email',
        ], [
            'trainer_id.required' => 'Thiếu thông tin HLV.',
            'trainer_id.exists' => 'HLV không tồn tại.',
            'test_email.required' => 'Vui lòng nhập email nhận test.',
            'test_email.email' => 'Địa chỉ email không hợp lệ.',
        ]);

        try {
            $trainer = Trainer::with(['seo', 'user'])
                ->where('id', $request->trainer_id)
                ->whereNotNull('user_id')
                ->first();

            if (!$trainer || !$trainer->user) {
                return response()->json([
                    'success' => false,
                    'message' => 'HLV không có tài khoản hoặc không tồn tại.',
                ], 400);
            }

            $parentSlug = config('main_' . env('APP_NAME') . '.slug_trainer_parent', 'huan-luyen-vien');
            $seo = $trainer->seo;

            if ($seo && !empty($seo->slug_full)) {
                $profileUrl = url('/' . $seo->slug_full);
            } elseif ($seo && !empty($seo->slug)) {
                $profileUrl = url('/' . $parentSlug . '/' . $seo->slug);
            } else {
                $profileUrl = url('/');
            }

            Mail::to($request->test_email)->send(new TrainerAccountMail(
                $trainer->name,
                $trainer->email ?? $request->test_email,
                $trainer->user->username,
                $trainer->trainer_code ?? '',
                $profileUrl,
                url('/he-thong'),
                route('admin.account.trainerProfile'),
                '••••••••'
            ));

            return response()->json([
                'success' => true,
                'message' => 'Đã gửi email test thành công đến ' . $request->test_email,
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('TrainerEmail sendTestEmail: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Lỗi gửi email: ' . $e->getMessage(),
            ], 500);
        }
    }
}

