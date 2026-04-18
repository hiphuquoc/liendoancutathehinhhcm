<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Models\Athlete;
use App\Jobs\SendAthleteAccountEmailJob;
use App\Mail\AthleteAccountMail;

class AthleteEmailController extends Controller
{
    /**
     * Hiển thị danh sách VĐV để gửi email
     */
    public function index(Request $request)
    {
        $query = Athlete::with(['seo', 'user'])
            ->whereHas('seo', function ($q) {
                $q->where('type', 'athlete_info')
                  ->where('language', 'vi');
            })
            ->whereNotNull('user_id'); // Chỉ VĐV đã có tài khoản

        // Mặc định: không hiển thị kết quả nếu không có filter hoặc search
        $courseFilter = $request->get('course');
        $search = $request->get('search');
        
        // Chỉ query nếu có filter hoặc search
        if (!empty($courseFilter) || !empty($search)) {
            // Bộ lọc theo khóa học (mã tháng năm trong athlete_code)
            if (!empty($courseFilter)) {
                // Format: T12.25 (tháng.năm)
                $query->where('athlete_code', 'like', '%' . $courseFilter . '%');
            }

            // Tìm kiếm theo tên
            if (!empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%')
                      ->orWhere('athlete_code', 'like', '%' . $search . '%')
                      ->orWhere('email', 'like', '%' . $search . '%')
                      ->orWhereHas('seo', function ($subQ) use ($search) {
                          $subQ->where('title', 'like', '%' . $search . '%');
                      });
                });
            }

            $athletes = $query->orderBy('id', 'DESC')->get();
            
            // Thêm thông tin URL và user info
            $parentSlug = config('main_' . env('APP_NAME') . '.slug_athlete_parent', 'van-dong-vien');
            foreach ($athletes as $athleteRow) {
                if (!empty($athleteRow->seo->slug_full)) {
                    $athleteRow->profile_url = url('/' . $athleteRow->seo->slug_full);
                } elseif (!empty($athleteRow->seo->slug)) {
                    $athleteRow->profile_url = url('/' . $parentSlug . '/' . $athleteRow->seo->slug);
                } else {
                    $athleteRow->profile_url = url('/');
                }
                
                if ($athleteRow->user) {
                    $athleteRow->username = $athleteRow->user->username;
                    $athleteRow->login_url = url('/he-thong');
                }
            }
        } else {
            // Không có filter, trả về collection rỗng
            $athletes = collect();
        }

        // Lấy danh sách các khóa học (từ athlete_code)
        $courses = Athlete::whereNotNull('athlete_code')
            ->where('athlete_code', '!=', '')
            ->pluck('athlete_code')
            ->map(function ($code) {
                // Extract phần TMM.YY từ athlete_code
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

        return view('admin.athleteEmail.index', compact('athletes', 'courses', 'courseFilter', 'search'));
    }

    /**
     * Đưa email thông tin tài khoản cho các HLV đã chọn vào hàng đợi (queue).
     * API luôn trả về JSON để tránh lỗi parse HTML trên frontend.
     */
    public function sendEmails(Request $request)
    {
        $request->validate([
            'athlete_ids' => 'required|array|min:1',
            'athlete_ids.*' => 'required|integer|exists:athlete_info,id',
        ], [
            'athlete_ids.required' => 'Vui lòng chọn ít nhất một VĐV',
            'athlete_ids.min' => 'Vui lòng chọn ít nhất một VĐV',
        ]);

        try {
            $athleteIds = array_unique($request->input('athlete_ids', []));

            $athletes = Athlete::with(['seo', 'user'])
                ->whereIn('id', $athleteIds)
                ->whereNotNull('user_id')
                ->whereNotNull('email')
                ->get();

            if ($athletes->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy VĐV nào có đầy đủ thông tin để gửi email.',
                ], 400);
            }

            $queuedCount = 0;
            foreach ($athletes as $athlete) {
                if (empty($athlete->email) || empty($athlete->user)) {
                    continue;
                }
                SendAthleteAccountEmailJob::dispatch($athlete->id);
                $queuedCount++;
            }

            if ($queuedCount === 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không có VĐV nào đủ điều kiện (email + tài khoản) để gửi.',
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
            Log::error('AthleteEmail sendEmails: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Lỗi hệ thống: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Gửi email test thông tin tài khoản VĐV đến một địa chỉ tùy chọn (gửi trực tiếp, không queue).
     * Luôn trả về JSON.
     */
    public function sendTestEmail(Request $request)
    {
        $request->validate([
            'athlete_id' => 'required|integer|exists:athlete_info,id',
            'test_email' => 'required|email',
        ], [
            'athlete_id.required' => 'Thiếu thông tin VĐV.',
            'athlete_id.exists' => 'VĐV không tồn tại.',
            'test_email.required' => 'Vui lòng nhập email nhận test.',
            'test_email.email' => 'Địa chỉ email không hợp lệ.',
        ]);

        try {
            $athlete = Athlete::with(['seo', 'user'])
                ->where('id', $request->athlete_id)
                ->whereNotNull('user_id')
                ->first();

            if (!$athlete || !$athlete->user) {
                return response()->json([
                    'success' => false,
                    'message' => 'VĐV không có tài khoản hoặc không tồn tại.',
                ], 400);
            }

            $parentSlug = config('main_' . env('APP_NAME') . '.slug_athlete_parent', 'van-dong-vien');
            $seo = $athlete->seo;

            if ($seo && !empty($seo->slug_full)) {
                $profileUrl = url('/' . $seo->slug_full);
            } elseif ($seo && !empty($seo->slug)) {
                $profileUrl = url('/' . $parentSlug . '/' . $seo->slug);
            } else {
                $profileUrl = url('/');
            }

            Mail::to($request->test_email)->send(new AthleteAccountMail(
                $athlete->name,
                $athlete->email ?? $request->test_email,
                $athlete->user->username,
                $athlete->athlete_code ?? '',
                $profileUrl,
                url('/he-thong'),
                route('admin.account.athleteProfile')
            ));

            return response()->json([
                'success' => true,
                'message' => 'Đã gửi email test thành công đến ' . $request->test_email,
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('AthleteEmail sendTestEmail: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Lỗi gửi email: ' . $e->getMessage(),
            ], 500);
        }
    }
}

