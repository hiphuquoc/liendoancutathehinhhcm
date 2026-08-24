<!DOCTYPE html>
<html lang=vi>
<head>
    <meta charset=UTF-8>
    <meta name=viewport content=width=device-width, initial-scale=1.0>
    <meta name=robots content=noindex, nofollow>
    <title>Đặt lại mật khẩu - {{ config(''app.name'') }}</title>
    
    {{-- Favicon --}}
    <link rel=icon type=image/x-icon href=/favicon.ico>
    
    {{-- Google Fonts --}}
    <link rel=preconnect href=https://fonts.googleapis.com>
    <link rel=preconnect href=https://fonts.gstatic.com crossorigin>
    <link href=https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap rel=stylesheet>
    
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
            --shadow-lg: 0 10px 15px -3px rgba(0,0,0,0.1), 0 4px 6px -4px rgba(0,0,0,0.1);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: ''Inter'', -apple-system, BlinkMacSystemFont, ''Segoe UI'', Roboto, sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #0f172a 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
            position: relative;
        }
        
        .bg-animation {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
            overflow: hidden;
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
        
        .resetContainer {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 440px;
        }
        
        .resetCard {
            background: white;
            border-radius: var(--radius);
            box-shadow: var(--shadow-lg);
            overflow: hidden;
        }
        
        .resetCard_header {
            padding: 2rem 2rem 1.5rem;
            text-align: center;
            background: linear-gradient(180deg, var(--gray-50) 0%, white 100%);
            border-bottom: 1px solid var(--gray-100);
        }
        
        .resetCard_header_logo {
            width: 48px;
            height: 48px;
            background: var(--primary-light);
            color: var(--primary);
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1rem;
        }
        
        .resetCard_header_logo svg {
            width: 26px;
            height: 26px;
        }
        
        .resetCard_header_title {
            font-size: 1.35rem;
            font-weight: 700;
            color: var(--gray-900);
            margin-bottom: 0.35rem;
        }
        
        .resetCard_header_subtitle {
            font-size: 0.875rem;
            color: var(--gray-500);
            line-height: 1.4;
        }
        
        .resetCard_body {
            padding: 1.75rem 2rem;
        }
        
        .alert {
            padding: 0.875rem 1rem;
            border-radius: 8px;
            font-size: 0.875rem;
            margin-bottom: 1.25rem;
            display: none;
            align-items: center;
            gap: 0.625rem;
            line-height: 1.4;
        }
        
        .alert.show {
            display: flex;
        }
        
        .alert--error {
            background: #fef2f2;
            border: 1px solid #fee2e2;
            color: var(--danger);
        }
        
        .alert--success {
            background: #f0fdf4;
            border: 1px solid #dcfce7;
            color: var(--success);
        }
        
        .alert_icon {
            flex-shrink: 0;
            width: 20px;
            height: 20px;
        }
        
        .formGroup {
            margin-bottom: 1.25rem;
        }
        
        .formGroup_label {
            display: block;
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--gray-700);
            margin-bottom: 0.375rem;
        }
        
        .formGroup_input {
            position: relative;
        }
        
        .formGroup_input input {
            width: 100%;
            padding: 0.75rem 2.75rem 0.75rem 2.6rem;
            font-size: 0.9375rem;
            color: var(--gray-900);
            background: var(--gray-50);
            border: 1.5px solid var(--gray-200);
            border-radius: 8px;
            outline: none;
            transition: all 0.2s;
        }
        
        .formGroup_input input:focus {
            background: white;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(1, 116, 190, 0.15);
        }
        
        .formGroup_input_icon {
            position: absolute;
            left: 0.875rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--gray-400);
            display: flex;
            align-items: center;
            pointer-events: none;
        }
        
        .formGroup_input_icon svg {
            width: 18px;
            height: 18px;
        }
        
        .formGroup_input_toggle {
            position: absolute;
            right: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--gray-400);
            cursor: pointer;
            padding: 4px;
            display: flex;
            align-items: center;
        }
        
        .formGroup_input_toggle:hover {
            color: var(--gray-600);
        }
        
        .formGroup_input_toggle svg {
            width: 18px;
            height: 18px;
        }
        
        .formGroup_hint {
            font-size: 0.75rem;
            color: var(--gray-500);
            margin-top: 0.25rem;
        }
        
        .submitBtn {
            width: 100%;
            padding: 0.875rem;
            font-size: 0.95rem;
            font-weight: 600;
            color: white;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            margin-top: 1.5rem;
        }
        
        .submitBtn:hover:not(:disabled) {
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(1, 116, 190, 0.35);
        }
        
        .submitBtn:disabled {
            background: var(--gray-300);
            cursor: not-allowed;
        }
        
        .submitBtn_loading {
            display: none;
            align-items: center;
            gap: 0.5rem;
        }
        
        .submitBtn.loading .submitBtn_text { display: none; }
        .submitBtn.loading .submitBtn_loading { display: flex; }
        
        .spinner {
            width: 18px;
            height: 18px;
            border: 2px solid rgba(255,255,255,0.3);
            border-top-color: white;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }
        
        @keyframes spin { to { transform: rotate(360deg); } }
        
        .resetCard_footer {
            padding: 1rem 2rem;
            background: var(--gray-50);
            border-top: 1px solid var(--gray-100);
            text-align: center;
        }
        
        .resetCard_footer a {
            color: var(--primary);
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 500;
        }
        
        .resetCard_footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class=bg-animation><div class=grid></div></div>
    
    <div class=resetContainer>
        <div class=resetCard>
            <div class=resetCard_header>
                <div class=resetCard_header_logo>
                    <svg viewBox=0 0 24 24 fill=none stroke=currentColor stroke-width=2>
                        <path d=M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z/>
                    </svg>
                </div>
                <h1 class=resetCard_header_title>Tạo mật khẩu mới</h1>
                <p class=resetCard_header_subtitle>Tài khoản: <strong>{{  }}</strong></p>
            </div>
            
            <div class=resetCard_body>
                <div id=alertBox class=alert alert--error>
                    <div id=alertMessage></div>
                </div>
                
                <form id=resetPasswordForm>
                    @csrf
                    <input type=hidden id=token name=token value={{ }}>
                    <input type=hidden id=email name=email value={{ }}>
                    
                    <div class=formGroup>
                        <label class=formGroup_label>Mật khẩu mới</label>
                        <div class=formGroup_input>
                            <input type=password id=password name=password placeholder=Ít nhất 6 ký tự required autofocus autocomplete=new-password>
                            <span class=formGroup_input_icon>
                                <svg viewBox=0 0 24 24 fill=none stroke=currentColor stroke-width=2>
                                    <path d=M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z/>
                                </svg>
                            </span>
                            <button type=button class=formGroup_input_toggle onclick=togglePassword('password', this)>
                                <svg viewBox=0 0 24 24 fill=none stroke=currentColor stroke-width=2><path d=M15 12a3 3 0 11-6 0 3 3 0 016 0z/><path d=M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z/></svg>
                            </button>
                        </div>
                    </div>
                    
                    <div class=formGroup>
                        <label class=formGroup_label>Xác nhận mật khẩu mới</label>
                        <div class=formGroup_input>
                            <input type=password id=password_confirmation name=password_confirmation placeholder=Nhập lại mật khẩu mới required autocomplete=new-password>
                            <span class=formGroup_input_icon>
                                <svg viewBox=0 0 24 24 fill=none stroke=currentColor stroke-width=2>
                                    <path d=M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z/>
                                </svg>
                            </span>
                            <button type=button class=formGroup_input_toggle onclick=togglePassword('password_confirmation', this)>
                                <svg viewBox=0 0 24 24 fill=none stroke=currentColor stroke-width=2><path d=M15 12a3 3 0 11-6 0 3 3 0 016 0z/><path d=M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z/></svg>
                            </button>
                        </div>
                    </div>
                    
                    <button type=submit class=submitBtn id=submitBtn>
                        <span class=submitBtn_text>Xác nhận đổi mật khẩu</span>
                        <span class=submitBtn_loading>
                            <span class=spinner></span>
                            Đang xử lý...
                        </span>
                    </button>
                </form>
            </div>
            
            <div class=resetCard_footer>
                <a href={{ route(''admin.loginForm'') }}>← Quay lại trang đăng nhập</a>
            </div>
        </div>
    </div>
    
    <script>
        function togglePassword(inputId, btn) {
            const input = document.getElementById(inputId);
            if (input.type === ''password'') {
                input.type = ''text'';
                btn.style.color = ''var(--primary)'';
            } else {
                input.type = ''password'';
                btn.style.color = '''';
            }
        }
        
        function showAlert(msg, type = ''error'') {
            const box = document.getElementById(''alertBox'');
            const msgEl = document.getElementById(''alertMessage'');
            box.className = ''alert alert--'' + type + '' show'';
            msgEl.textContent = msg;
        }
        
        function hideAlert() {
            document.getElementById(''alertBox'').classList.remove(''show'');
        }
        
        document.getElementById(''resetPasswordForm'').addEventListener(''submit'', async function(e) {
            e.preventDefault();
            hideAlert();
            
            const p1 = document.getElementById(''password'').value;
            const p2 = document.getElementById(''password_confirmation'').value;
            
            if (p1.length < 6) {
                showAlert(''Mật khẩu phải có ít nhất 6 ký tự'');
                return;
            }
            
            if (p1 !== p2) {
                showAlert(''Mật khẩu xác nhận không khớp'');
                return;
            }
            
            const btn = document.getElementById(''submitBtn'');
            btn.classList.add(''loading'');
            btn.disabled = true;
            
            try {
                const res = await fetch(''{{ route(admin.resetPassword) }}'', {
                    method: ''POST'',
                    headers: {
                        ''Content-Type'': ''application/json'',
                        ''X-CSRF-TOKEN'': ''{{ csrf_token() }}'',
                        ''Accept'': ''application/json''
                    },
                    body: JSON.stringify({
                        token: document.getElementById(''token'').value,
                        email: document.getElementById(''email'').value,
                        password: p1,
                        password_confirmation: p2
                    })
                });
                
                const data = await res.json();
                btn.classList.remove(''loading'');
                
                if (data.success) {
                    showAlert(data.message, ''success'');
                    setTimeout(() => {
                        window.location.href = ''{{ route(admin.loginForm) }}'';
                    }, 1500);
                } else {
                    btn.disabled = false;
                    showAlert(data.message || ''Có lỗi xảy ra, vui lòng thử lại.'');
                }
            } catch (err) {
                btn.classList.remove(''loading'');
                btn.disabled = false;
                showAlert(''Lỗi kết nối máy chủ. Vui lòng thử lại.'');
            }
        });
    </script>
</body>
</html>