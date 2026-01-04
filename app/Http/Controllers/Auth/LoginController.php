<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\PasswordResetMail;
use Illuminate\Support\Carbon;

class LoginController extends Controller
{
    /**
     * Maximum login attempts before lockout
     */
    private const MAX_ATTEMPTS = 5;
    
    /**
     * Lockout duration in seconds (5 minutes)
     */
    private const DECAY_SECONDS = 300;

    /**
     * Create admin user (one-time setup)
     */
    public static function create()
    {
        User::create([
            'name'      => 'admin',
            'email'     => 'websitekiengiang@gmail.com',
            'password'  => Hash::make('hitourVN@mk123'),
            'role'      => 'admin'
        ]);
        return redirect()->route('admin.loginForm');
    }

    /**
     * Display admin login form
     */
    public function loginForm(): View|\Illuminate\Http\RedirectResponse
    {
        // Nếu đã đăng nhập admin thì redirect
        if (Auth::check() && Auth::user()->hasRole('admin')) {
            return redirect()->route('admin.trainer.list');
        }
        
        return view('layouts.loginForm');
    }
    
    /**
     * Handle admin login request
     */
    public function loginAdmin(Request $request): JsonResponse
    {
        // Rate limiting key based on IP
        $throttleKey = 'admin-login:' . $request->ip();
        
        // Check if too many attempts
        if (RateLimiter::tooManyAttempts($throttleKey, self::MAX_ATTEMPTS)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            $minutes = ceil($seconds / 60);
            
            return response()->json([
                'success' => false,
                'message' => "Bạn đã thử đăng nhập quá nhiều lần. Vui lòng thử lại sau {$minutes} phút.",
                'type' => 'rate_limit',
                'retry_after' => $seconds,
            ], 429);
        }

        // Validate input
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|max:255',
            'password' => 'required|string|min:6|max:100',
        ], [
            'email.required' => 'Vui lòng nhập email',
            'password.required' => 'Vui lòng nhập mật khẩu',
            'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'type' => 'validation',
                'errors' => $validator->errors()->toArray(),
            ], 422);
        }

        // Determine if login is by username or email
        $loginValue = trim($request->email);
        
        // Find user by username or email
        $user = User::where('username', $loginValue)
                    ->orWhere('email', $loginValue)
                    ->first();
        
        // Remember me option
        $remember = $request->boolean('remember', false);

        // Attempt login
        if ($user && Hash::check($request->password, $user->password)) {
            Auth::login($user, $remember);
            
            // Check admin role
            if ($user->hasRole('admin') || $user->hasRole('trainer') || $user->hasRole('referee')) {
                // Clear rate limiter on success
                RateLimiter::clear($throttleKey);
                
                // Regenerate session
                $request->session()->regenerate();
                
                // Redirect trainer/referee to profile page, admin to trainer list
                $redirectUrl = $user->hasRole('admin') 
                    ? route('admin.trainer.list') 
                    : route('admin.account.profile');
                
                return response()->json([
                    'success' => true,
                    'message' => 'Đăng nhập thành công! Đang chuyển hướng...',
                    'redirect_url' => $redirectUrl,
                    'user' => [
                        'name' => $user->name,
                        'email' => $user->email,
                    ],
                ]);
            }
            
            // Not admin - logout and return error
            Auth::logout();
            RateLimiter::hit($throttleKey, self::DECAY_SECONDS);
            
            return response()->json([
                'success' => false,
                'message' => 'Tài khoản của bạn không có quyền truy cập khu vực quản trị.',
                'type' => 'unauthorized',
            ], 403);
        }

        // Failed login - increment rate limiter
        RateLimiter::hit($throttleKey, self::DECAY_SECONDS);
        $attemptsLeft = self::MAX_ATTEMPTS - RateLimiter::attempts($throttleKey);

        $message = 'Tên đăng nhập/Email hoặc mật khẩu không chính xác.';
        if ($attemptsLeft > 0 && $attemptsLeft <= 3) {
            $message .= " Bạn còn {$attemptsLeft} lần thử.";
        }

        return response()->json([
            'success' => false,
            'message' => $message,
            'type' => 'credentials',
            'attempts_left' => $attemptsLeft,
        ], 401);
    }

    /**
     * Handle customer login request
     */
    public function loginCustomer(Request $request): JsonResponse
    {
        $throttleKey = 'customer-login:' . $request->ip();
        
        if (RateLimiter::tooManyAttempts($throttleKey, self::MAX_ATTEMPTS)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return response()->json([
                'success' => false,
                'message' => 'Bạn đã thử đăng nhập quá nhiều lần. Vui lòng thử lại sau.',
                'retry_after' => $seconds,
            ], 429);
        }

        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:255',
            'password' => 'required|string|min:6|max:100',
        ], [
            'email.required' => 'Vui lòng nhập email',
            'email.email' => 'Email không hợp lệ',
            'password.required' => 'Vui lòng nhập mật khẩu',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors()->toArray(),
            ], 422);
        }

        $credentials = [
            'email' => trim($request->email),
            'password' => $request->password,
        ];

        $remember = $request->boolean('remember', false);

        if (Auth::attempt($credentials, $remember)) {
            RateLimiter::clear($throttleKey);
            $request->session()->regenerate();
            
            // Lấy intended URL từ session nếu có
            $redirectUrl = $request->session()->pull('url.intended', null);
            
            return response()->json([
                'success' => true,
                'message' => 'Đăng nhập thành công!',
                'redirect_url' => $redirectUrl, // Trả về redirect URL nếu có
                'user' => [
                    'name' => Auth::user()->name,
                    'email' => Auth::user()->email,
                ],
            ]);
        }

        RateLimiter::hit($throttleKey, self::DECAY_SECONDS);
        
        return response()->json([
            'success' => false,
            'message' => 'Email hoặc mật khẩu không chính xác.',
        ], 401);
    }

    /**
     * Handle customer registration request
     */
    public function registerCustomer(Request $request): JsonResponse
    {
        $throttleKey = 'customer-register:' . $request->ip();
        
        if (RateLimiter::tooManyAttempts($throttleKey, self::MAX_ATTEMPTS)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return response()->json([
                'success' => false,
                'message' => 'Bạn đã thử đăng ký quá nhiều lần. Vui lòng thử lại sau.',
                'retry_after' => $seconds,
            ], 429);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|min:2',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:6|max:100|confirmed',
            'password_confirmation' => 'required|string|min:6',
        ], [
            'name.required' => 'Vui lòng nhập họ tên',
            'name.min' => 'Họ tên phải có ít nhất 2 ký tự',
            'email.required' => 'Vui lòng nhập email',
            'email.email' => 'Email không hợp lệ',
            'email.unique' => 'Email này đã được sử dụng',
            'password.required' => 'Vui lòng nhập mật khẩu',
            'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự',
            'password.confirmed' => 'Mật khẩu xác nhận không khớp',
            'password_confirmation.required' => 'Vui lòng xác nhận mật khẩu',
        ]);

        if ($validator->fails()) {
            RateLimiter::hit($throttleKey, self::DECAY_SECONDS);
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors()->toArray(),
            ], 422);
        }

        try {
            $user = User::create([
                'name' => trim($request->name),
                'email' => trim($request->email),
                'password' => Hash::make($request->password),
                'role' => 'user',
                'wallet_balance' => 0,
            ]);

            // Auto login sau khi đăng ký
            Auth::login($user);
            RateLimiter::clear($throttleKey);
            $request->session()->regenerate();
            
            return response()->json([
                'success' => true,
                'message' => 'Đăng ký thành công! Đang đăng nhập...',
                'user' => [
                    'name' => $user->name,
                    'email' => $user->email,
                ],
            ]);
        } catch (\Exception $e) {
            RateLimiter::hit($throttleKey, self::DECAY_SECONDS);
            \Log::error('Registration error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi đăng ký. Vui lòng thử lại sau.',
            ], 500);
        }
    }

    /**
     * Handle forgot password request
     */
    public function forgotPassword(Request $request): JsonResponse
    {
        try {
            $throttleKey = 'forgot-password:' . $request->ip();
            
            // Rate limiting: 3 attempts per 15 minutes
            if (RateLimiter::tooManyAttempts($throttleKey, 3)) {
                $seconds = RateLimiter::availableIn($throttleKey);
                $minutes = ceil($seconds / 60);
                
                return response()->json([
                    'success' => false,
                    'message' => "Bạn đã yêu cầu quá nhiều lần. Vui lòng thử lại sau {$minutes} phút.",
                    'retry_after' => $seconds,
                ], 429);
            }

            $validator = Validator::make($request->all(), [
                'email' => 'required|email|max:255',
            ], [
                'email.required' => 'Vui lòng nhập email',
                'email.email' => 'Email không hợp lệ',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first(),
                    'errors' => $validator->errors()->toArray(),
                ], 422);
            }

            $email = trim($request->email);
            $user = User::where('email', $email)->first();

            // Always return success message for security (don't reveal if email exists)
            if (!$user) {
                RateLimiter::hit($throttleKey, 900); // 15 minutes
                return response()->json([
                    'success' => true,
                    'message' => 'Nếu email tồn tại, chúng tôi đã gửi link đặt lại mật khẩu đến email của bạn.',
                ]);
            }

            // Generate reset token
            $token = Str::random(64);
            $now = Carbon::now();

            // Delete old tokens for this user
            DB::table('password_resets')
                ->where('email', $email)
                ->delete();

            // Insert new token
            DB::table('password_resets')->insert([
                'email' => $email,
                'token' => Hash::make($token),
                'created_at' => $now,
            ]);

            // Send password reset email
            $language = app()->getLocale() ?? 'vi';
            
            try {
                Mail::to($user)->send(new PasswordResetMail($token, $email, $language));
            } catch (\Exception $mailException) {
                // Log mail error but don't fail the request
                Log::error('Mail sending error: ' . $mailException->getMessage());
                
                // If mail fails, still save the token so user can reset manually
                // or admin can help them
                
                // Check if it's an AWS/SES configuration error
                if (str_contains($mailException->getMessage(), 'AWS') || 
                    str_contains($mailException->getMessage(), 'SES') ||
                    str_contains($mailException->getMessage(), 'credentials')) {
                    Log::error('Mail configuration error - AWS SES credentials may be missing or incorrect');
                    
                    // Still return success but log the issue for admin
                    RateLimiter::hit($throttleKey, 900);
                    
                    return response()->json([
                        'success' => false,
                        'message' => 'Hệ thống email đang được cấu hình. Vui lòng liên hệ admin để được hỗ trợ đặt lại mật khẩu.',
                        'error' => config('app.debug') ? 'Mail configuration error: ' . $mailException->getMessage() : null,
                    ], 500);
                }
                
                // Re-throw if it's not a configuration error
                throw $mailException;
            }

            RateLimiter::hit($throttleKey, 900); // 15 minutes

            return response()->json([
                'success' => true,
                'message' => 'Chúng tôi đã gửi link đặt lại mật khẩu đến email của bạn. Vui lòng kiểm tra hộp thư.',
            ]);
        } catch (\Exception $e) {
            Log::error('Forgot password error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra. Vui lòng thử lại sau.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Show reset password form
     */
    public function showResetPasswordForm(Request $request)
    {
        $token = $request->query('token');
        $email = $request->query('email');
        $language = app()->getLocale() ?? 'vi';

        if (!$token || !$email) {
            return redirect('/')->with('error', 'Link đặt lại mật khẩu không hợp lệ.');
        }

        // Verify token
        $passwordReset = DB::table('password_resets')
            ->where('email', $email)
            ->first();

        if (!$passwordReset || !Hash::check($token, $passwordReset->token)) {
            return redirect('/')->with('error', 'Link đặt lại mật khẩu không hợp lệ hoặc đã hết hạn.');
        }

        // Check if token is expired (1 hour)
        $createdAt = Carbon::parse($passwordReset->created_at);
        if ($createdAt->addHour()->isPast()) {
            DB::table('password_resets')->where('email', $email)->delete();
            return redirect('/')->with('error', 'Link đặt lại mật khẩu đã hết hạn. Vui lòng yêu cầu lại.');
        }

        return view('main.auth.resetPassword', [
            'token' => $token,
            'email' => $email,
            'language' => $language,
        ]);
    }

    /**
     * Handle reset password request
     */
    public function resetPassword(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required|string',
            'email' => 'required|email|max:255',
            'password' => 'required|string|min:6|max:100|confirmed',
            'password_confirmation' => 'required|string|min:6',
        ], [
            'token.required' => 'Token không hợp lệ',
            'email.required' => 'Vui lòng nhập email',
            'email.email' => 'Email không hợp lệ',
            'password.required' => 'Vui lòng nhập mật khẩu mới',
            'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự',
            'password.confirmed' => 'Mật khẩu xác nhận không khớp',
            'password_confirmation.required' => 'Vui lòng xác nhận mật khẩu',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors()->toArray(),
            ], 422);
        }

        $email = trim($request->email);
        $token = $request->token;

        // Verify token
        $passwordReset = DB::table('password_resets')
            ->where('email', $email)
            ->first();

        if (!$passwordReset || !Hash::check($token, $passwordReset->token)) {
            return response()->json([
                'success' => false,
                'message' => 'Token không hợp lệ hoặc đã hết hạn.',
            ], 400);
        }

        // Check if token is expired (1 hour)
        $createdAt = Carbon::parse($passwordReset->created_at);
        if ($createdAt->addHour()->isPast()) {
            DB::table('password_resets')->where('email', $email)->delete();
            return response()->json([
                'success' => false,
                'message' => 'Token đã hết hạn. Vui lòng yêu cầu lại link đặt lại mật khẩu.',
            ], 400);
        }

        // Update user password
        $user = User::where('email', $email)->first();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy tài khoản.',
            ], 404);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        // Delete used token
        DB::table('password_resets')->where('email', $email)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Mật khẩu đã được đặt lại thành công! Bạn có thể đăng nhập ngay bây giờ.',
        ]);
    }

    /**
     * Handle logout request
     */
    public static function logout(Request $request = null)
    {
        Auth::logout();
        
        if ($request) {
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }
        
        // Redirect to previous page or home
        $referer = $_SERVER['HTTP_REFERER'] ?? '/';
        return redirect($referer);
    }
}
