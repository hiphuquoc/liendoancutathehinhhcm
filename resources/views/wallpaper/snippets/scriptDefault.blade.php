<!-- BEGIN: Google Analytics -->
@if(env('APP_ENV')=='production')
    <script defer>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
    
        function loadGoogleAnalytics() {
            var script = document.createElement('script');
            script.src = 'https://www.googletagmanager.com/gtag/js?id={{ env('GOOGLE_ANALYTICS_ID') }}';
            document.head.appendChild(script);
    
            gtag('js', new Date());
            gtag('config', '{{ env('GOOGLE_ANALYTICS_ID') }}');
        }
    
        window.addEventListener('scroll', loadGoogleAnalytics, { once: true });
    </script>
@endif
<!-- END: Google Analytics -->
<!-- BEGIN: Jquery -->
{{-- <script defer src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script> --}}
<!-- END: Jquery -->
<!-- BEGIN: include JS -->
<script defer src="{{ asset('js/main/jquery-3.6.0.min.js') }}"></script>
<script defer src="{{ asset('js/main/bootstrap.min.js') }}"></script>
<script defer src="{{ asset('js/main/jquery.smartmenus.js') }}"></script>
<script defer src="{{ asset('js/main/owl.carousel.js') }}"></script>
<script defer src="{{ asset('js/main/custom.js') }}"></script>
<!-- END: include JS -->
<script defer type="text/javascript">

    document.addEventListener('DOMContentLoaded', function() {
        /* check để xem có cookie csrf chưa (do lần đầu truy cập trang không có lỗi google login) */
        // checkToSetCsrfFirstTime();
        
        /* lazyload ảnh lần đầu */
        lazyload();
        /* lazyload ảnh khi scroll */
        $(window).on('scroll', function() {
            lazyload();
        });

        // $('img').each(function() {
        //     if (!$(this).attr('alt') || !$(this).attr('title')) {
        //         console.log(this);
        //     }
        // });

        // /* check login để hiện thị button */
        // checkLoginAndSetShow();

        // /* thông tin khách hàng */
        // settingGPSVisitor();
        // settingTimezoneVisitor();

        /* ===== Hiệu ứng ===== */
        $('.effectFadeIn').each(function(){
            $(this).css('opacity', 0);
        });
        effectFadeIn();
        effectDropdown();
        effectLeftToRight();
        effectBottomToTop();
    });

    /* Hiệu ứng fade in */
    window.addEventListener('scroll', function() {
        effectFadeIn();
        effectDropdown();
        effectLeftToRight();
        effectBottomToTop();
    });

    function settingGPSVisitor() {
        // Kiểm tra xem đã thiết lập GPS thành công (dựa trên localStorage)
        if (localStorage.getItem('gps_set') == 'true') {
            return; // Nếu đã thiết lập thành công, không làm gì thêm
        }

        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
                const latitude = position.coords.latitude;
                const longitude = position.coords.longitude;

                // Tạo URL với tham số truy vấn
                const url = new URL('{{ route("main.settingGPSVisitor") }}');
                url.searchParams.append('latitude', latitude);
                url.searchParams.append('longitude', longitude);

                fetch(url, {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                    }
                })
                .then(response => response.json())
                .then(data => {
                    // Lưu trạng thái đã thiết lập GPS vào localStorage
                    localStorage.setItem('gps_set', data.flag);
                    // refresh
                    if(data.flag) location.reload();
                })
                .catch(error => {
                    console.error('Error fetching GPS data:', error);
                });
            }, function(error) {
                // console.error('Error retrieving location:', error);
            });
        } else {
            // console.error('Geolocation is not supported by this browser.');
        }
    }

    function settingTimezoneVisitor(){
        // Lấy múi giờ từ thiết bị người dùng
        const timezone = Intl.DateTimeFormat().resolvedOptions().timeZone;
        const url = new URL('{{ route("main.settingTimezoneVisitor") }}');
        url.searchParams.append('timezone', timezone);
        // Gửi múi giờ đến server qua GET request
        fetch(url, {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}' // CSRF token nếu cần cho GET
            }
        })
        .then(response => response.json())
        .then(data => {
            // Lưu trạng thái đã thiết lập GPS vào localStorage
            localStorage.setItem('timezone_set', data.flag);
        })
        .catch(error => {
            console.error('Error setting timezone:', error);
        });
    }
    
    function lazyload(){
        /* đối với ảnh */
        $('img.lazyload').each(function() {
            if (!$(this).hasClass('loaded')) {
                var distance = $(window).scrollTop() - $(this).offset().top + 900;
                if (distance > 0) {
                    $(this).attr('src', $(this).attr('data-src'));
                    $(this).addClass('loaded').removeClass('loading_1').attr('style', '');
                }
            }
        });
        /* đối với div dùng background */
        $('div.lazyload').each(function() {
            if (!$(this).hasClass('loaded')) {
                var distance = $(window).scrollTop() - $(this).offset().top + 900;
                if (distance > 0) {
                    $(this).css({
                        background  : 'url("'+$(this).attr('data-src')+'") no-repeat center center / cover',
                        filter      : 'unset'
                    });
                    $(this).addClass('loaded');
                }
            }
        });
    }

    // function openCloseElemt(idElemt){
    //     let displayE    = $('#' + idElemt).css('display');
    //     if(displayE=='none'){
    //         $('#' + idElemt).css('display', 'block');
    //         $('body').css('overflow', 'hidden');
    //     }else {
    //         $('#' + idElemt).css('display', 'none');
    //         $('body').css('overflow', 'unset');
    //     }
    // }

    function effectFadeIn(percentHeightScreenEffect = 1.3){
        $('.effectFadeIn').each(function() {
            const $this = $(this);

            // Kiểm tra nếu phần tử đã hiển thị (tránh chạy lại hiệu ứng)
            if ($this.css('opacity') == 1) {
                return;
            }

            const positionElement = $this.offset().top;
            const heightWindow = $(window).height();
            const positionScroll = $(window).scrollTop();

            // Kiểm tra nếu phần tử nằm trong khung nhìn
            if ((positionScroll + heightWindow / percentHeightScreenEffect) >= positionElement) {
                $this.animate({
                    opacity: 1,
                }, 500);
            }
        });
    }
    /* hiệu ứng rơi xuống => dùng cho phần tử có scrollTop thấp hơn ít nhất 1 màn hình */
    function effectDropdown(percentHeightScreenEffect = 1.3){
        $('.effectDropdown').each(function(){
            /* ẩn trước */
            if(!$(this).hasClass('alreadyEffectDropdown')) $(this).css('opacity', 0);
            /* thao tác */
            const positionElement   = $(this).offset().top;
            const heightWindow      = $(window).height();
            const positionScroll    = $(window).scrollTop();
            if(!$(this).hasClass('alreadyEffectDropdown')&&parseInt(heightWindow/percentHeightScreenEffect + positionScroll)>=positionElement){
                    const marginTopReal = parseInt($(this).css('margin-top'));
                    $(this).css({
                        'margin-top'    : (marginTopReal - 200)+'px'
                    });
                    $(this).animate({
                        'margin-top'    : marginTopReal+'px',
                        'opacity'       : 1
                    }, 800);
                    /* thực hiện rồi thì không thực hiện nữa */
                    $(this).addClass('alreadyEffectDropdown');
            }
        })
    }
    /* hiệu ứng xuất hiện từ trái qua phải => dùng cho phần tử có scrollTop thấp hơn ít nhất 1 màn hình */
    function effectLeftToRight(percentHeightScreenEffect = 1.3){
        $('.effectLeftToRight').each(function(){
            /* ẩn trước */
            if(!$(this).hasClass('alreadyEffectLeftToRight')) $(this).css('opacity', 0);
            /* thao tác */
            const positionElement           = $(this).offset().top;
            const heightWindow              = $(window).height();
            const positionScroll            = $(window).scrollTop();
            if(!$(this).hasClass('alreadyEffectLeftToRight')&&parseInt(heightWindow/percentHeightScreenEffect + positionScroll)>=positionElement){
                    const marginLeftReal    = parseInt($(this).css('margin-left'));
                    $(this).css({
                        'margin-left'   : (marginLeftReal - 200)+'px'
                    });
                    $(this).animate({
                        'margin-left'    : marginLeftReal+'px',
                        'opacity'       : 1
                    }, 800);
                    /* thực hiện rồi thì không thực hiện nữa */
                    $(this).addClass('alreadyEffectLeftToRight');
            }
        })
    }
    /* hiệu ứng xuất hiện từ dưới lên => dùng cho phần tử có scrollTop thấp hơn ít nhất 1 màn hình */
    function effectBottomToTop(percentHeightScreenEffect = 1.3){
        $('.effectBottomToTop').each(function(){
            /* ẩn trước */
            if(!$(this).hasClass('alreadyEffectBottomToTop')) $(this).css('opacity', 0);
            /* thao tác */
            const positionElement           = $(this).offset().top;
            const heightWindow              = $(window).height();
            const positionScroll            = $(window).scrollTop();
            if(!$(this).hasClass('alreadyEffectBottomToTop')&&parseInt(heightWindow/percentHeightScreenEffect + positionScroll)>=positionElement){
                    const marginTopReal     = parseInt($(this).css('margin-top'));
                    $(this).css({
                        'margin-top'    : (marginTopReal + 200)+'px'
                    });
                    $(this).animate({
                        'margin-top'    : marginTopReal+'px',
                        'opacity'       : 1
                    }, 800);
                    /* thực hiện rồi thì không thực hiện nữa */
                    $(this).addClass('alreadyEffectBottomToTop');
            }
        })
    }
    /* Go to top */
    mybutton 					    = document.getElementById("smoothScrollToTop");
    mybutton.style.display 	        = "none";
    window.onscroll                 = function() {scrollFunction()};
    function scrollFunction() {
        if (document.body.scrollTop > 500 || document.documentElement.scrollTop > 500) {
            mybutton.style.display 	= "block";
        } else {
            mybutton.style.display 	= "none";
        }
    }
    function smoothScrollToTop() {
        // const currentScroll = document.documentElement.scrollTop;
        // if (currentScroll > 0) {
        //     window.requestAnimationFrame(smoothScrollToTop);
        //     window.scrollTo(0, currentScroll - currentScroll / 8);
        // }
        document.documentElement.scrollTop          = 0;
    }
    /* link to a href #id smooth */
    document.querySelectorAll('a[href^="#"]').forEach(function(a) {
        a.addEventListener('click', function(event) {
            event.preventDefault();
            let id = this.getAttribute('href');
            let element = document.querySelector(id);
            if (!element) {
                console.error(`Element with ID ${id} not found`);
                return;
            }
            let offsetTop = element.offsetTop;
            window.scrollTo({
                top: offsetTop + 200,
                behavior: 'smooth'
            });
        });
    });
    
    // /* hiện thông báo cho sản phẩm vào giỏ hàng thành công */
    // function openCloseModal(idModal, action = null){
    //     const elementModal  = $('#'+idModal);
    //     const flag          = elementModal.css('display');
    //     /* tooggle */
    //     if(action==null){
    //         if(flag=='none'){
    //             elementModal.css('display', 'flex');
    //             $('#js_openCloseModal_blur').addClass('blurBackground');
    //             $('body').css('overflow', 'hidden');
    //         }else {
    //             elementModal.css('display', 'none');
    //             $('#js_openCloseModal_blur').removeClass('blurBackground');
    //             $('body').css('overflow', 'unset');
    //         }
    //     }
    //     /* đóng */
    //     if(action=='close'){
    //         elementModal.css('display', 'none');
    //         $('#js_openCloseModal_blur').removeClass('blurBackground');
    //         $('body').css('overflow', 'unset');
    //     }
    //     /* mở */
    //     if(action=='open'){
    //         elementModal.css('display', 'flex');
    //         $('#js_openCloseModal_blur').addClass('blurBackground');
    //         $('body').css('overflow', 'hidden');
    //     }
    // }
    
    // /* add loading icon */
    // function loadLoading(action = 'show') {
    //     if(action == 'show'){
    //         $('.loadingBox').addClass('show');
    //     }else if(action == 'hide'){
    //         $('.loadingBox').removeClass('show');
    //     }else {
    //         $('.loadingBox').toggleClass('show');
    //     }
    // }

    /* tính năng registry email ở footer */
    function submitFormRegistryEmail(idForm) {
        event.preventDefault();
        const inputEmail = $('#' + idForm).find('[name*=registry_email]');
        const valueEmail = inputEmail.val();
        if (isValidEmail(valueEmail)) {
            fetch("/registryEmail?registry_email=" + encodeURIComponent(valueEmail), {
                method: 'GET',
                mode: 'cors'
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(response => {
                /* bật thông báo */
                setMessageModal(response.title, response.content);
            })
            .catch(error => {
                console.error("Fetch request failed:", error);
            });
        } else {
            inputEmail.val('');
            inputEmail.attr('placeholder', 'Email không hợp lệ!');
        }
    }
    function isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }
    
    // /* validate form khi nhập */
    // function validateWhenType(elementInput, type = 'empty'){
    //     const idElement         = $(elementInput).attr('id');
    //     const parent            = $(document).find('[for*="'+idElement+'"]').parent();
    //     /* validate empty */
    //     if(type=='empty'){
    //         const valueElement  = $.trim($(elementInput).val());
    //         if(valueElement!=''&&valueElement!='0'){
    //             parent.removeClass('validateErrorEmpty');
    //             parent.addClass('validateSuccess');
    //         }else {
    //             parent.removeClass('validateSuccess');
    //             parent.addClass('validateErrorEmpty');
    //         }
    //     }
    //     /* validate phone */ 
    //     if(type=='phone'){
    //         const valueElement = $.trim($(elementInput).val());
    //         if(valueElement.length>=10&&/^\d+$/.test(valueElement)){
    //             parent.removeClass('validateErrorPhone');
    //             parent.addClass('validateSuccess');
    //         }else {
    //             parent.removeClass('validateSuccess');
    //             parent.addClass('validateErrorPhone');
    //         }
    //     }
    //     /* validate email */ 
    //     if(type=='email'){
    //         const valueElement = $.trim($(elementInput).val());
    //         /* check empty (nếu required) */
    //         if($(elementInput).prop('required')){
    //             if(valueElement==''){
    //                 parent.removeClass('validateSuccess');
    //                 parent.removeClass('validateErrorEmail');
    //                 parent.addClass('validateErrorEmpty');
    //                 return false;
    //             }
    //             /* check email hợp lệ */
    //             if(/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(valueElement)){
    //                 parent.removeClass('validateErrorEmail');
    //                 parent.removeClass('validateErrorEmpty');
    //                 parent.addClass('validateSuccess');
    //             }else {
    //                 parent.removeClass('validateSuccess');
    //                 parent.removeClass('validateErrorEmpty');
    //                 parent.addClass('validateErrorEmail');
    //             }
    //         }else {
    //             /* check email hợp lệ */
    //             if(/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(valueElement)){
    //                 parent.removeClass('validateErrorEmail');
    //                 parent.removeClass('validateErrorEmpty');
    //                 parent.addClass('validateSuccess');
    //             }
    //         }
    //     }
    // }
    
    
    // /* validate form */
    // function validateForm(idForm){
    //     let error       = [];
    //     /* input required không được bỏ trống */
    //     $('#'+idForm).find('input[required]').each(function(){
    //         /* đưa vào mảng */
    //         if($(this).val()==''){
    //             error.push($(this).attr('name'));
    //         }
    //     })
    //     /* select */
    //     $('#'+idForm).find('select[required]').each(function(){
    //         if($(this).val()==0) error.push($(this).attr('name'));
    //     })
    //     return error;
    // }
    
    /* check đăng nhập */
    function checkLoginAndSetShow(){
        let dataForm = {};
        dataForm.language = $('#language').val();            
        const queryString = new URLSearchParams(dataForm).toString();
        fetch('/checkLoginAndSetShow?' + queryString, {
            method  : 'GET',
            mode    : 'cors',
        })
        .then(response => {
            if (!response.ok){
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(response => {
            /* button desktop */
            $('#js_checkLoginAndSetShow_button').html(response.button);
            $('#js_checkLoginAndSetShow_button').css('display', 'flex');
            /* button mobile */
            $('#js_checkLoginAndSetShow_buttonMobile').html(response.button_mobile);
            /* modal chung */
            $('#js_checkLoginAndSetShow_modal').html(response.modal);
        })
        .catch(error => {
            console.error("Fetch request failed:", error);
        });
    }
    
</script>