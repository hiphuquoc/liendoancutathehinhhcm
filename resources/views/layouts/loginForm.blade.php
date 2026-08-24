<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>Đăng nhập Quản trị - {{ config('app.name') }}</title>
    
    {{-- Favicon --}}
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    
    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary: #0174be;
            --primary-dark: #015d9b;
            --primary-light: #e8f4fc;
            --success: #07a35d;
            --danger: #dc2626;
            --warning: #f59e0b;
            --gray-50: #f9fafb;
            --gray-100: #f3f4f6;
            --gray-200: #e5e7eb;
            --gray-300: #d1d5db;
            --gray-400: #9ca3af;
            --gray-500: #6b7280;
            --gray-600: #4b5563;
            --gray-700: #374151;
            --gray-800: #1f2937;
            --gray-900: #111827;
            --radius: 12px;
            --radius-lg: 16px;
            --shadow-sm: 0 1px 2px rgba(0,0,0,0.05);
            --shadow: 0 4px 6px -1px rgba(0,0,0,0.1), 0 2px 4px -2px rgba(0,0,0,0.1);
            --shadow-lg: 0 10px 15px -3px rgba(0,0,0,0.1), 0 4px 6px -4px rgba(0,0,0,0.1);
            --shadow-xl: 0 20px 25px -5px rgba(0,0,0,0.1), 0 8px 10px -6px rgba(0,0,0,0.1);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #0f172a 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
            position: relative;
            overflow: hidden;
        }
        
        /* Animated Background */
        .bg-animation {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
            overflow: hidden;
        }
        
        .bg-animation::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle at 30% 20%, rgba(1, 116, 190, 0.15) 0%, transparent 50%),
                        radial-gradient(circle at 70% 80%, rgba(7, 163, 93, 0.1) 0%, transparent 40%);
            animation: pulse 15s ease-in-out infinite;
        }
        
        .bg-animation .grid {
            position: absolute;
            width: 100%;
            height: 100%;
            background-image: 
                linear-gradient(rgba(255,255,255,0.02) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,0.02) 1px, transparent 1px);
            background-size: 50px 50px;
        }
        
        .bg-animation .floating-shape {
            position: absolute;
            border-radius: 50%;
            opacity: 0.1;
            animation: float 20s ease-in-out infinite;
        }
        
        .bg-animation .floating-shape:nth-child(2) {
            width: 300px;
            height: 300px;
            background: var(--primary);
            top: 10%;
            left: 10%;
            animation-delay: -5s;
        }
        
        .bg-animation .floating-shape:nth-child(3) {
            width: 200px;
            height: 200px;
            background: var(--success);
            bottom: 20%;
            right: 15%;
            animation-delay: -10s;
        }
        
        .bg-animation .floating-shape:nth-child(4) {
            width: 150px;
            height: 150px;
            background: var(--warning);
            top: 60%;
            left: 5%;
            animation-delay: -15s;
        }
        
        @keyframes pulse {
            0%, 100% { transform: scale(1) rotate(0deg); }
            50% { transform: scale(1.1) rotate(5deg); }
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0) rotate(0deg); }
            50% { transform: translateY(-30px) rotate(10deg); }
        }
        
        /* Login Container */
        .loginContainer {
            width: 100%;
            max-width: 440px;
            position: relative;
            z-index: 1;
        }
        
        /* Login Card */
        .loginCard {
            background: rgba(255, 255, 255, 0.98);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-xl), 0 0 0 1px rgba(255,255,255,0.1);
            overflow: hidden;
            animation: slideUp 0.6s ease-out;
        }
        
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        /* Header */
        .loginCard_header {
            padding: 2rem 2rem 1.5rem;
            text-align: center;
            position: relative;
        }
        
        .loginCard_header::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 2rem;
            right: 2rem;
            height: 1px;
            background: linear-gradient(90deg, transparent, var(--gray-200), transparent);
        }
        
        .loginCard_header_logo {
            width: 56px;
            height: 56px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            border-radius: var(--radius);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            box-shadow: 0 4px 14px rgba(1, 116, 190, 0.3);
        }
        
        .loginCard_header_logo svg {
            width: 32px;
            height: 32px;
            color: white;
        }
        
        .loginCard_header_title {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--gray-900);
            margin-bottom: 0.375rem;
        }
        
        .loginCard_header_subtitle {
            font-size: 0.9375rem;
            color: var(--gray-500);
            line-height: 1.5;
        }
        
        /* Body */
        .loginCard_body {
            padding: 1.5rem 2rem 2rem;
        }
        
        /* Form Group */
        .formGroup {
            margin-bottom: 1.25rem;
        }
        
        .formGroup_label {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 0.5rem;
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--gray-700);
        }
        
        .formGroup_label a {
            font-weight: 500;
            color: var(--primary);
            text-decoration: none;
            font-size: 0.8125rem;
        }
        
        .formGroup_label a:hover {
            text-decoration: underline;
        }
        
        .formGroup_input {
            position: relative;
        }
        
        .formGroup_input input {
            width: 100%;
            padding: 0.875rem 1rem;
            padding-left: 3rem;
            font-size: 0.9375rem;
            color: var(--gray-900);
            background: var(--gray-50);
            border: 2px solid var(--gray-200);
            border-radius: var(--radius);
            outline: none;
            transition: all 0.2s ease;
            font-family: inherit;
        }
        
        .formGroup_input input:hover {
            border-color: var(--gray-300);
        }
        
        .formGroup_input input:focus {
            background: white;
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(1, 116, 190, 0.1);
        }
        
        .formGroup_input input.error {
            border-color: var(--danger);
            background: #fef2f2;
        }
        
        .formGroup_input input.error:focus {
            box-shadow: 0 0 0 4px rgba(220, 38, 38, 0.1);
        }
        
        .formGroup_input input::placeholder {
            color: var(--gray-400);
        }
        
        .formGroup_input_icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--gray-400);
            pointer-events: none;
            transition: color 0.2s;
        }
        
        .formGroup_input_icon svg {
            width: 20px;
            height: 20px;
        }
        
        .formGroup_input input:focus + .formGroup_input_icon {
            color: var(--primary);
        }
        
        .formGroup_input_toggle {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--gray-400);
            cursor: pointer;
            padding: 0.25rem;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: color 0.2s;
        }
        
        .formGroup_input_toggle:hover {
            color: var(--gray-600);
        }
        
        .formGroup_input_toggle svg {
            width: 20px;
            height: 20px;
        }
        
        .formGroup_forgot {
            font-size: 0.8125rem;
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
            cursor: pointer;
            transition: color 0.15s;
        }
        
        .formGroup_forgot:hover {
            color: var(--primary-dark);
            text-decoration: underline;
        }

        /* Modal Quên mật khẩu */
        .modalOverlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(15, 23, 42, 0.75);
            backdrop-filter: blur(4px);
            z-index: 999;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 1rem;
            opacity: 0;
            transition: opacity 0.2s ease;
        }
        
        .modalOverlay.active {
            display: flex;
            opacity: 1;
        }
        
        .modalBox {
            background: white;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-xl);
            width: 100%;
            max-width: 420px;
            overflow: hidden;
            transform: scale(0.95);
            transition: transform 0.2s ease;
        }
        
        .modalOverlay.active .modalBox {
            transform: scale(1);
        }
        
        .modalHeader {
            padding: 1.25rem 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid var(--gray-100);
            background: var(--gray-50);
        }
        
        .modalHeader_title {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--gray-900);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .modalHeader_title svg {
            width: 20px;
            height: 20px;
            color: var(--primary);
        }
        
        .modalClose {
            background: none;
            border: none;
            color: var(--gray-400);
            cursor: pointer;
            padding: 4px;
            border-radius: 6px;
            display: flex;
            align-items: center;
        }
        
        .modalClose:hover {
            color: var(--gray-700);
            background: var(--gray-200);
        }
        
        .modalBody {
            padding: 1.5rem;
        }
        
        .modalBody_desc {
            font-size: 0.875rem;
            color: var(--gray-600);
            line-height: 1.45;
            margin-bottom: 1.25rem;
        }
        
        .modalAlert {
            padding: 0.75rem 1rem;
            border-radius: 8px;
            font-size: 0.85rem;
            margin-bottom: 1rem;
            display: none;
            line-height: 1.4;
        }
        
        .modalAlert.show { display: block; }
        .modalAlert--error { background: #fef2f2; border: 1px solid #fee2e2; color: var(--danger); }
        .modalAlert--success { background: #f0fdf4; border: 1px solid #dcfce7; color: var(--success); }

        /* Remember Me */
        .formGroup_remember {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-top: 1rem;
        }
        
        .formGroup_remember input[type="checkbox"] {
            width: 18px;
            height: 18px;
            accent-color: var(--primary);
            cursor: pointer;
        }
        
        .formGroup_remember label {
            font-size: 0.875rem;
            color: var(--gray-600);
            cursor: pointer;
            user-select: none;
        }
        
        /* Alert */
        .alert {
            display: none;
            align-items: flex-start;
            gap: 0.75rem;
            padding: 1rem;
            border-radius: var(--radius);
            margin-bottom: 1.25rem;
            font-size: 0.875rem;
            animation: shake 0.4s ease;
        }
        
        .alert.show {
            display: flex;
        }
        
        .alert--error {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #991b1b;
        }
        
        .alert--success {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            color: #166534;
        }
        
        .alert--warning {
            background: #fffbeb;
            border: 1px solid #fde68a;
            color: #92400e;
        }
        
        .alert_icon {
            flex-shrink: 0;
            width: 20px;
            height: 20px;
        }
        
        .alert_content {
            flex: 1;
            line-height: 1.5;
        }
        
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }
        
        /* Submit Button */
        .submitBtn {
            width: 100%;
            padding: 1rem;
            font-size: 1rem;
            font-weight: 600;
            color: white;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            border: none;
            border-radius: var(--radius);
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            position: relative;
            overflow: hidden;
            margin-top: 1.5rem;
        }
        
        .submitBtn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s ease;
        }
        
        .submitBtn:hover:not(:disabled)::before {
            left: 100%;
        }
        
        .submitBtn:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(1, 116, 190, 0.35);
        }
        
        .submitBtn:active:not(:disabled) {
            transform: translateY(0);
        }
        
        .submitBtn:disabled {
            background: var(--gray-300);
            cursor: not-allowed;
        }
        
        .submitBtn_text {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .submitBtn_loading {
            display: none;
            align-items: center;
            gap: 0.5rem;
        }
        
        .submitBtn.loading .submitBtn_text {
            display: none;
        }
        
        .submitBtn.loading .submitBtn_loading {
            display: flex;
        }
        
        .spinner {
            width: 20px;
            height: 20px;
            border: 2px solid rgba(255,255,255,0.3);
            border-top-color: white;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        
        /* Footer */
        .loginCard_footer {
            padding: 1rem 2rem;
            background: var(--gray-50);
            border-top: 1px solid var(--gray-100);
            text-align: center;
        }
        
        .loginCard_footer_text {
            font-size: 0.8125rem;
            color: var(--gray-500);
        }
        
        .loginCard_footer_text a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
        }
        
        .loginCard_footer_text a:hover {
            text-decoration: underline;
        }
        
        /* Brand Footer */
        .brandFooter {
            text-align: center;
            margin-top: 1.5rem;
            color: rgba(255,255,255,0.5);
            font-size: 0.8125rem;
        }
        
        .brandFooter a {
            color: rgba(255,255,255,0.7);
            text-decoration: none;
            font-weight: 500;
        }
        
        .brandFooter a:hover {
            color: white;
        }
        
        /* Responsive */
        @media (max-width: 480px) {
            body {
                padding: 0;
                align-items: flex-start;
            }
            
            .loginContainer {
                max-width: none;
            }
            
            .loginCard {
                border-radius: 0;
                min-height: 100vh;
                display: flex;
                flex-direction: column;
            }
            
            .loginCard_header {
                padding: 1.5rem 1.25rem 1.25rem;
            }
            
            .loginCard_body {
                padding: 1.25rem;
                flex: 1;
            }
            
            .loginCard_footer {
                padding: 1rem 1.25rem;
            }
            
            .brandFooter {
                position: fixed;
                bottom: 0;
                left: 0;
                right: 0;
                padding: 1rem;
                background: rgba(0,0,0,0.5);
                margin: 0;
            }
        }
    </style>
</head>
<body>
    {{-- Animated Background --}}
    <div class="bg-animation">
        <div class="grid"></div>
        <div class="floating-shape"></div>
        <div class="floating-shape"></div>
        <div class="floating-shape"></div>
    </div>
    
    <div class="loginContainer">
        <div class="loginCard">
            {{-- Header --}}
            <div class="loginCard_header">
                <div class="loginCard_header_logo">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>
                <h1 class="loginCard_header_title">Đăng nhập Quản trị</h1>
                <p class="loginCard_header_subtitle">Hệ thống quản lý HLV - VĐV - Trọng tài Liên Đoàn</p>
            </div>
            
            {{-- Body --}}
            <div class="loginCard_body">
                {{-- Alert --}}
                <div id="alertBox" class="alert alert--error">
                    <svg class="alert_icon" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
                    </svg>
                    <div id="alertMessage" class="alert_content"></div>
                </div>
                
                <form id="loginForm" method="POST">
                    @csrf
                    
                    {{-- Email --}}
                    <div class="formGroup">
                        <label class="formGroup_label">
                            <span>Email</span>
                        </label>
                        <div class="formGroup_input">
                            <input type="text" 
                                   name="email" 
                                   id="email"
                                   placeholder="admin@example.com" 
                                   autocomplete="email"
                                   required>
                            <span class="formGroup_input_icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                            </span>
                        </div>
                    </div>
                    
                    {{-- Password --}}
                    <div class="formGroup">
                        <label class="formGroup_label">
                            <span>Mật khẩu</span>
                            <a href="javascript:void(0)" class="formGroup_forgot" onclick="openForgotModal()">Quên mật khẩu?</a>
                        </label>
                        <div class="formGroup_input">
                            <input type="password" 
                                   name="password" 
                                   id="password"
                                   placeholder="••••••••" 
                                   autocomplete="current-password"
                                   required>
                            <span class="formGroup_input_icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                            </span>
                            <button type="button" class="formGroup_input_toggle" onclick="togglePassword()">
                                <svg id="eyeIcon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                <svg id="eyeOffIcon" style="display:none;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                    
                    {{-- Remember Me --}}
                    <div class="formGroup_remember">
                        <input type="checkbox" name="remember" id="remember" value="1">
                        <label for="remember">Ghi nhớ đăng nhập</label>
                    </div>
                    
                    {{-- Submit Button --}}
                    <button type="submit" class="submitBtn" id="submitBtn">
                        <span class="submitBtn_text">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="20" height="20">
                                <path d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                            </svg>
                            Đăng nhập
                        </span>
                        <span class="submitBtn_loading">
                            <span class="spinner"></span>
                            Đang xử lý...
                        </span>
                    </button>
                </form>
            </div>
            
            {{-- Footer --}}
            <div class="loginCard_footer">
                <p class="loginCard_footer_text">
                    <a href="/">← Quay về trang chủ</a>
                </p>
            </div>
        </div>
        
        {{-- Brand Footer --}}
        <div class="brandFooter">
            <span>© {{ date('Y') }} <a href="/">{{ config('app.name') }}</a>. All rights reserved.</span>
        </div>
    </div>
    
    <script>
        // Toggle Password Visibility
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');
            const eyeOffIcon = document.getElementById('eyeOffIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.style.display = 'none';
                eyeOffIcon.style.display = 'block';
            } else {
                passwordInput.type = 'password';
                eyeIcon.style.display = 'block';
                eyeOffIcon.style.display = 'none';
            }
        }
        
        // Show Alert
        function showAlert(message, type = 'error') {
            const alertBox = document.getElementById('alertBox');
            const alertMessage = document.getElementById('alertMessage');
            
            alertBox.className = 'alert alert--' + type + ' show';
            alertMessage.textContent = message;
            
            // Scroll to alert
            alertBox.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }
        
        // Hide Alert
        function hideAlert() {
            const alertBox = document.getElementById('alertBox');
            alertBox.classList.remove('show');
        }
        
        // Set Loading State
        function setLoading(isLoading) {
            const submitBtn = document.getElementById('submitBtn');
            const emailInput = document.getElementById('email');
            const passwordInput = document.getElementById('password');
            
            if (isLoading) {
                submitBtn.classList.add('loading');
                submitBtn.disabled = true;
                emailInput.disabled = true;
                passwordInput.disabled = true;
            } else {
                submitBtn.classList.remove('loading');
                submitBtn.disabled = false;
                emailInput.disabled = false;
                passwordInput.disabled = false;
            }
        }
        
        // Clear Error States
        function clearErrors() {
            document.querySelectorAll('.formGroup_input input').forEach(input => {
                input.classList.remove('error');
            });
        }
        
        // Form Submission
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            hideAlert();
            clearErrors();
            
            const email = document.getElementById('email').value.trim();
            const password = document.getElementById('password').value;
            const remember = document.getElementById('remember').checked;
            
            // Client-side validation
            if (!email) {
                showAlert('Vui lòng nhập email');
                document.getElementById('email').classList.add('error');
                document.getElementById('email').focus();
                return;
            }
            
            if (!password) {
                showAlert('Vui lòng nhập mật khẩu');
                document.getElementById('password').classList.add('error');
                document.getElementById('password').focus();
                return;
            }
            
            setLoading(true);
            
            // Send login request
            fetch('{{ route("admin.loginAdmin") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    email: email,
                    password: password,
                    remember: remember
                })
            })
            .then(response => response.json().then(data => ({ status: response.status, data })))
            .then(({ status, data }) => {
                setLoading(false);
                
                if (data.success) {
                    showAlert(data.message, 'success');
                    
                    // Redirect after short delay
                    setTimeout(() => {
                        window.location.href = data.redirect_url || '{{ route("admin.category.list") }}';
                    }, 800);
                } else {
                    showAlert(data.message);
                    
                    // Mark fields with errors
                    if (data.errors) {
                        if (data.errors.email) {
                            document.getElementById('email').classList.add('error');
                        }
                        if (data.errors.password) {
                            document.getElementById('password').classList.add('error');
                        }
                    }
                    
                    // Focus on password for credential errors
                    if (data.type === 'credentials') {
                        document.getElementById('password').value = '';
                        document.getElementById('password').focus();
                    }
                }
            })
            .catch(error => {
                setLoading(false);
                console.error('Login error:', error);
                showAlert('Có lỗi xảy ra. Vui lòng thử lại sau.');
            });
        });
        
                // Modal Forgot Password Functions
        function openForgotModal() {
            const modal = document.getElementById('forgotModal');
            const currentEmail = document.getElementById('email').value.trim();
            const forgotEmailInput = document.getElementById('forgotEmail');
            
            if (currentEmail && currentEmail.includes('@')) {
                forgotEmailInput.value = currentEmail;
            }
            
            modal.classList.add('active');
            hideModalAlert();
            setTimeout(() => forgotEmailInput.focus(), 100);
        }

        function closeForgotModal() {
            document.getElementById('forgotModal').classList.remove('active');
        }

        function handleModalOverlayClick(e) {
            if (e.target.id === 'forgotModal') {
                closeForgotModal();
            }
        }

        function showModalAlert(message, type = 'error') {
            const box = document.getElementById('modalAlertBox');
            box.className = 'modalAlert modalAlert--' + type + ' show';
            box.textContent = message;
        }

        function hideModalAlert() {
            document.getElementById('modalAlertBox').classList.remove('show');
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeForgotModal();
            }
        });

        document.getElementById('forgotPasswordForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            hideModalAlert();

            const email = document.getElementById('forgotEmail').value.trim();
            if (!email) {
                showModalAlert('Vui lòng nhập địa chỉ email.');
                return;
            }

            const btn = document.getElementById('forgotSubmitBtn');
            btn.classList.add('loading');
            btn.disabled = true;

            try {
                const response = await fetch('{{ route("admin.forgotPassword") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ email: email })
                });

                const data = await response.json();
                btn.classList.remove('loading');
                btn.disabled = false;

                if (data.success) {
                    showModalAlert(data.message, 'success');
                    setTimeout(() => {
                        closeForgotModal();
                    }, 4000);
                } else {
                    showModalAlert(data.message || 'Có lỗi xảy ra, vui lòng thử lại sau.', 'error');
                }
            } catch (err) {
                btn.classList.remove('loading');
                btn.disabled = false;
                showModalAlert('Lỗi kết nối máy chủ. Vui lòng kiểm tra lại mạng hoặc thử lại sau.', 'error');
            }
        });

        // Auto-focus email input
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('email').focus();
        });
        
        // Enter key handler
        document.querySelectorAll('.formGroup_input input').forEach(input => {
            input.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    document.getElementById('loginForm').dispatchEvent(new Event('submit'));
                }
            });
            
            // Clear error on input
            input.addEventListener('input', function() {
                this.classList.remove('error');
            });
        });
    </script>
    {{-- Modal Quên Mật Khẩu --}}
    <div id="forgotModal" class="modalOverlay" onclick="handleModalOverlayClick(event)">
        <div class="modalBox">
            <div class="modalHeader">
                <div class="modalHeader_title">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                    </svg>
                    <span>Quên mật khẩu</span>
                </div>
                <button type="button" class="modalClose" onclick="closeForgotModal()" aria-label="Đóng">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="20" height="20">
                        <path d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <div class="modalBody">
                <p class="modalBody_desc">
                    Nhập địa chỉ <strong>Email</strong> liên kết với tài khoản của bạn. Hệ thống sẽ gửi liên kết bảo mật để bạn tạo mật khẩu mới.
                </p>

                <div id="modalAlertBox" class="modalAlert modalAlert--error"></div>

                <form id="forgotPasswordForm">
                    @csrf
                    <div class="formGroup">
                        <label class="formGroup_label">Email tài khoản</label>
                        <div class="formGroup_input">
                            <input type="email" id="forgotEmail" name="email" placeholder="vidu@gmail.com" required autocomplete="email">
                            <span class="formGroup_input_icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                            </span>
                        </div>
                    </div>

                    <button type="submit" class="submitBtn" id="forgotSubmitBtn">
                        <span class="submitBtn_text">Gửi link đặt lại mật khẩu</span>
                        <span class="submitBtn_loading">
                            <span class="spinner"></span>
                            Đang gửi email...
                        </span>
                    </button>
                </form>
            </div>
        </div>
    </div>

</body>
</html>
