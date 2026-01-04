<!DOCTYPE html>
<html lang="vi">

<!-- === START:: Head === -->
<head>
    @include('admin.snippets.head')
    @stack('headCustom')
</head>
<!-- === END:: Head === -->

<!-- === START:: Body === -->
<body>

    <div id="js_fullLoading_blur">
        <div class="pageContent container">
            <div class="adminDashboard">
                <!-- === START:: Sidebar === -->
        @include('admin.snippets.menu')
                <!-- === END:: Sidebar === -->

                <!-- === START:: Main Content === -->
                <main class="adminDashboard_main">
                    @if(session('success'))
                        <div class="alert alert-success">
                            <svg viewBox="0 0 24 24" fill="currentColor">
                                <path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12zm13.36-1.814a.75.75 0 10-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 00-1.06 1.06l2.25 2.25a.75.75 0 001.14-.094l3.75-5.25z" clip-rule="evenodd"/>
                            </svg>
                            {{ session('success') }}
                        </div>
                    @endif
                    
                    @if(session('error'))
                        <div class="alert alert-error">
                            <svg viewBox="0 0 24 24" fill="currentColor">
                                <path fill-rule="evenodd" d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25zm-1.72 6.97a.75.75 0 10-1.06 1.06L10.94 12l-1.72 1.72a.75.75 0 101.06 1.06L12 13.06l1.72 1.72a.75.75 0 101.06-1.06L13.06 12l1.72-1.72a.75.75 0 10-1.06-1.06L12 10.94l-1.72-1.72z" clip-rule="evenodd"/>
                            </svg>
                            {{ session('error') }}
                        </div>
                    @endif
                    
            @yield('content')
                </main>
                <!-- === END:: Main Content === -->
            </div>
        </div>
        
        {{-- Mobile Menu Backdrop --}}
        <div class="adminDashboard_mobileMenuBackdrop" id="adminMobileMenuBackdrop"></div>
    </div>

    <!-- === START:: Footer === -->
    {{-- @include('snippets.footer') --}}
    <!-- === END:: Footer === -->

    <!-- === START:: Toast Notifications === -->
    <div id="toast-container" class="toast-container position-fixed top-0 start-0">
        <!-- Toasts sẽ được thêm động ở đây -->
    </div>
    <!-- === END:: Toast Notifications === -->

    <!-- === START:: Modal === -->
    @include('admin.modal.fullLoading')
    @include('admin.modal.clearCache')
    @stack('modal')
    <!-- === END:: Modal === -->
    
    <!-- === START:: Scripts Default === -->
    @include('admin.snippets.scriptDefault')
    <!-- === END:: Scripts Default === -->

    <!-- === START:: Scripts Custom === -->
    @stack('scriptCustom')
    <!-- === END:: Scripts Custom === -->
</body>
<!-- === END:: Body === -->

</html>