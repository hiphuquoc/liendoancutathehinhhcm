<!-- === START:: Scripts Default === -->
<script>
(function(){var mq=window.matchMedia('(hover: none), (pointer: coarse)');if(mq.matches)document.documentElement.classList.add('no-hover');else document.documentElement.classList.remove('no-hover');mq.addEventListener('change',function(){if(mq.matches)document.documentElement.classList.add('no-hover');else document.documentElement.classList.remove('no-hover');});})();
</script>
<!-- jQuery Core -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<!-- jQuery UI -->
<script src="https://code.jquery.com/ui/1.13.1/jquery-ui.min.js"></script>
<!-- jQuery Repeater Plugin -->
<script src="{{ asset('sources/admin/app-assets/vendors/js/forms/repeater/jquery.repeater.min.js') }}"></script>
<!-- START:: TIPTAP (thay TinyMCE) -->
<script type="module" src="{{ asset('sources/admin/app-assets/js/tiptap-editor.js') }}"></script>
<!-- END:: TIPTAP -->
<!-- === END:: Scripts Default === -->
<script defer>
    $(window).on('load', function () {
        // Feather icons removed - using SVG icons directly instead
        // if (feather) {
        //     feather.replace({
        //         width: 14,
        //         height: 14
        //     });
        // }
        // Bootstrap tooltip removed - using custom tooltip implementation
        // $('[data-bs-toggle="tooltip"]').tooltip();
        loadImageFromGoogleCloud();
    })

    $(function () {
        // Bootstrap tooltip removed - using custom tooltip implementation
        // $('[data-toggle="tooltip"]').tooltip({ html : true })
    })
    /* COUNT CHARACTOR */
    $('input, textarea').on('input', function(){
        const idElemt           = $(this).attr('id');
        if(idElemt){
            const lengthInput   = $(this).val().length;
            const elemtShow     = $(document).find("[data-charactor='" + idElemt + "']");
            elemtShow.html(lengthInput);
        }
    })
    /* Setting view */
    function settingView(name, valDefault){
        $.ajax({
            url         : '{{ route("admin.setting.view") }}',
            type        : 'get',
            dataType    : 'html',
            data        : {
                name,
                default : valDefault
            },
            success     : function(result){
                location.reload();
            }
        });
    }

    function submitForm(idForm, addParams = {}){
        const form = document.getElementById(idForm);
        if(!form) {
            console.error('Form not found:', idForm);
            return;
        }
        
        // Check HTML5 validation
        if (form.checkValidity && !form.checkValidity()) {
            console.log('Form validation failed');
            form.reportValidity();
            return;
        }
        
        // Thêm các tham số bổ sung (nếu có) vào form
        if (addParams && typeof addParams === 'object') {
            Object.keys(addParams).forEach(function(key) {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = key;
                input.value = addParams[key];
                form.appendChild(input);
            });
        }
        
        // Submit form
        console.log('Submitting form:', idForm);
        form.submit();
    }

    /* copy to clipboard */
    function copyToClipboard(idContent, callbackFunction=null) {
        // Get the text field
        var copyText = document.getElementById(idContent);

        // Select the text field
        copyText.select();
        // copyText.setSelectionRange(0, 99999); // For mobile devices - input không phải hidden

        // Copy the text inside the text field
        navigator.clipboard.writeText(copyText.value);
    
        // Alert the copied text
        callbackFunction;
    }
    /* loading trong khi chờ ajax */
    function addLoading(idWrite){
        const html = '{{ view("admin.template.loading") }}';
        $('.'+idWrite).append(html);
    }
    function removeLoading(){
        $('.js_loading_element').remove();
    }
    /* load image from goole cloud */
    function loadImageFromGoogleCloud(){
        $(document).find('img[data-google-cloud]').each(function(){
            var elementImg          = $(this);
            const urlGoogleCloud    = elementImg.attr('data-google-cloud');
            const size              = elementImg.attr('data-size');
            $.ajax({
                url         : '{{ route("ajax.loadImageFromGoogleCloud") }}',
                type        : 'get',
                dataType    : 'html',
                data        : {
                    url_google_cloud    : urlGoogleCloud,
                    size
                },
                success     : function(response){
                    elementImg.attr('src', response);
                }
            });
        });
    }
    /* function viết content */
    function callAI(action){
        $('[data-type="'+action+'"]').each(function() {
            const id                = $(this).data('id');
            const language          = $(this).data('language');
            const id_prompt         = $(this).data('id_prompt');
            const id_content        = $(this).data('id_content');
            chatGpt($(this), id, language, id_prompt, id_content);
        });
    }
    /* ai chatgpt */
    function chatGpt(input, id, language, id_prompt, id_content){ /* id_content hiện chỉ dùng cho content do có nhiều phần tử content dùng chung 1 prompt */
        addAndRemoveClass($(input), 'inputLoading', 'inputSuccess inputError');
        const idBox = $(input).attr('id');
        $.ajax({
            url         : '{{ route("main.chatGpt") }}',
            type        : 'get',
            dataType    : 'json',
            data        : {
                id, language, id_prompt, id_content
            }
        }).done(function(data){
            if(data.error=='') {
                addAndRemoveClass($(input), 'inputSuccess', 'inputLoading inputError');
                $(input).val(data.content);
                if (typeof window.setTiptapContent === 'function' && idBox) {
                    window.setTiptapContent(idBox, data.content);
                }
            }else {
                addAndRemoveClass($(input), 'inputError', 'inputLoading inputSuccess');
            }
            const idInput = $(input).attr('id');
            if(idInput){
                const lengthInput = $(input).val().length;
                const elemtShow = $(document).find("[data-charactor='" + idInput + "']");
                elemtShow.html(lengthInput);
            }
        })
    }
    function addAndRemoveClass(input, add, remove){
        $(input).addClass(add).removeClass(remove);
        var inputTag = $(input).prev();
        if(inputTag.is('tags')){
            inputTag.addClass(add).removeClass(remove);
        }
    }
    function clearCacheHtml(){
        const modal = document.getElementById('adminClearCacheModal');
        if (modal) {
            modal.classList.add('adminClearCacheModal--open');
            document.body.style.overflow = 'hidden';
        }
    }
    
    function closeClearCacheModal(){
        const modal = document.getElementById('adminClearCacheModal');
        if (modal) {
            modal.classList.remove('adminClearCacheModal--open');
            document.body.style.overflow = '';
            
            // Reset button state
            const confirmBtn = document.getElementById('adminClearCacheModal_confirmBtn');
            if (confirmBtn) {
                confirmBtn.disabled = false;
                const buttonText = confirmBtn.querySelector('.adminClearCacheModal_button_text');
                const buttonLoader = confirmBtn.querySelector('.adminClearCacheModal_button_loader');
                if (buttonText) buttonText.style.display = '';
                if (buttonLoader) buttonLoader.style.display = 'none';
            }
        }
    }
    
    function confirmClearCache(){
        const confirmBtn = document.getElementById('adminClearCacheModal_confirmBtn');
        const buttonText = confirmBtn.querySelector('.adminClearCacheModal_button_text');
        const buttonLoader = confirmBtn.querySelector('.adminClearCacheModal_button_loader');
        
        // Show loading state
        confirmBtn.disabled = true;
        if (buttonText) buttonText.style.display = 'none';
        if (buttonLoader) buttonLoader.style.display = 'inline-flex';
        
        // Call API
        $.ajax({
            url: '{{ route("admin.cache.clearCache") }}',
            type: 'get',
            dataType: 'html',
            success: function(response){
                // Close modal
                closeClearCacheModal();
                
                // Show success message (you can use createToast function if available)
                if (typeof createToast === 'function') {
                    createToast('success', 'Thành công', 'Đã xóa cache HTML thành công!');
                } else {
                    alert('Đã xóa cache HTML thành công!');
                }
            },
            error: function(xhr, status, error){
                // Reset button state
                confirmBtn.disabled = false;
                if (buttonText) buttonText.style.display = '';
                if (buttonLoader) buttonLoader.style.display = 'none';
                
                // Show error message
                if (typeof createToast === 'function') {
                    createToast('error', 'Lỗi', 'Có lỗi xảy ra khi xóa cache. Vui lòng thử lại.');
                } else {
                    alert('Có lỗi xảy ra khi xóa cache. Vui lòng thử lại.');
                }
            }
        });
    }
    
    // Close modal on ESC key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const modal = document.getElementById('adminClearCacheModal');
            if (modal && modal.classList.contains('adminClearCacheModal--open')) {
                closeClearCacheModal();
            }
        }
    });
    /* tạo job dịch tự động */
    function createJobTranslateContent(idSeoVI, language){
        $.ajax({
            url         : '{{ route("admin.translate.createJobTranslateContentAjax") }}',
            type        : 'post',
            dataType    : 'html',
            data        : {
                "_token": "{{ csrf_token() }}",
                id_seo_vi : idSeoVI,
                language
            }
        }).done(function(data){
            if(data) location.reload();
        })
    }
    function openCloseFullLoading(){
        const htmlLoading = $('#js_fullLoading_bg');
        if(htmlLoading.is(":visible")){
            htmlLoading.css('display', 'none');
            $('#js_fullLoading_blur').css({
                'filter' : 'unset',
                'overflow'  : 'unset',
            });
        } else {
            htmlLoading.css('display', 'flex');
            $('#js_fullLoading_blur').css({
                'filter'    : 'blur(8px)',
                'overflow'  : 'hidden',
            });
        }
    }
    function createToast(type, title, message) {
        const toastContainer = document.getElementById('toast-container') || document.body;
        
        // Tạo ID duy nhất cho mỗi Toast
        const toastId = 'toast-' + Date.now();

        // Tạo cấu trúc HTML của Toast
        const toastHTML = `
            <div id="${toastId}" class="toast toast-${type}" aria-live="polite" style="display: block; opacity: 1;">
                <div class="toast-progress" style="width: 0%;"></div>
                <button type="button" class="toast-close-button" role="button">×</button>
                <div class="toast-title">${title}</div>
                <div class="toast-message">${message}</div>
            </div>
        `;

        // Thêm Toast vào container
        toastContainer.insertAdjacentHTML('beforeend', toastHTML);

        const toastElement = document.getElementById(toastId);

        // Tự động ẩn Toast sau 3 giây và xóa khỏi DOM
        setTimeout(() => {
            toastElement.style.opacity = 0;
            setTimeout(() => toastElement.remove(), 300); // Xóa sau khi hiệu ứng mờ hoàn tất
        }, 10000);

        // Xử lý sự kiện đóng thủ công
        const closeButton = toastElement.querySelector('.toast-close-button');
        if (closeButton) {
            closeButton.addEventListener('click', () => toastElement.remove());
        }
    }

    /* Admin Sidebar Menu - New Design */
    // Admin Mobile Menu Toggle - Global function
    function toggleAdminMobileMenu() {
        const sidebar = document.getElementById('adminDashboardSidebar');
        const backdrop = document.getElementById('adminMobileMenuBackdrop');
        const isOpen = sidebar && sidebar.classList.contains('adminDashboard_sidebar--mobileOpen');
        
        if (!isOpen) {
            // Open menu
            if (sidebar) {
                sidebar.classList.add('adminDashboard_sidebar--mobileOpen');
            }
            if (backdrop) {
                backdrop.classList.add('active');
            }
            document.body.style.overflow = 'hidden';
        } else {
            // Close menu
            if (sidebar) {
                sidebar.classList.remove('adminDashboard_sidebar--mobileOpen');
            }
            if (backdrop) {
                backdrop.classList.remove('active');
            }
            document.body.style.overflow = '';
        }
        }
    
    (function() {
        const menuItems = document.querySelectorAll('.admin-menu-item.has-submenu');

        // Toggle submenu - handled by onclick in HTML for better control
        // This is just a fallback for any edge cases
        menuItems.forEach(function(item) {
            const link = item.querySelector('.admin-menu-link.has-submenu-toggle');
            if (link && !link.onclick) {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    item.classList.toggle('open');
                });
            }
        });

        // Auto open submenu if active
        menuItems.forEach(function(item) {
            const activeChild = item.querySelector('.admin-submenu-item.active');
            if (activeChild) {
                item.classList.add('open');
            }
        });

        // Close menu when clicking backdrop
        document.addEventListener('DOMContentLoaded', function() {
            const backdrop = document.getElementById('adminMobileMenuBackdrop');
            if (backdrop) {
                backdrop.addEventListener('click', function() {
                    toggleAdminMobileMenu();
                });
            }
            
            // Close menu on ESC key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    const sidebar = document.getElementById('adminDashboardSidebar');
                    if (sidebar && sidebar.classList.contains('adminDashboard_sidebar--mobileOpen')) {
                        toggleAdminMobileMenu();
                    }
                }
            });
        });

        // Handle window resize
        let resizeTimer;
        window.addEventListener('resize', function() {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(function() {
                if (window.innerWidth > 1023) {
                    const sidebar = document.getElementById('adminDashboardSidebar');
                    const backdrop = document.getElementById('adminMobileMenuBackdrop');
                    if (sidebar) {
                        sidebar.classList.remove('adminDashboard_sidebar--mobileOpen');
                    }
                    if (backdrop) {
                        backdrop.classList.remove('active');
                    }
                    document.body.style.overflow = '';
                }
            }, 250);
        });
    })();
    
    // Copy trainer code function - Global function for all pages
    function copyTrainerCode(code, element) {
        // element can be button or the entire code box
        var codeBox = element.classList && element.classList.contains('adminPersonnelPage_card_code') ? element : 
                      element.classList && element.classList.contains('adminSidebar_header_trainerCode') ? element :
                      element.closest('.adminPersonnelPage_card_code') || element.closest('.adminSidebar_header_trainerCode');
        if (!codeBox) codeBox = element;
        
        // Copy to clipboard
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(code).then(function() {
                // Show success state
                codeBox.classList.add('adminPersonnelPage_card_code--copied', 'adminSidebar_header_trainerCode--copied');
                codeBox.setAttribute('data-tooltip', 'Đã sao chép!');
                codeBox.setAttribute('title', 'Đã sao chép!');
                
                // Reset after 2 seconds
                setTimeout(function() {
                    codeBox.classList.remove('adminPersonnelPage_card_code--copied', 'adminSidebar_header_trainerCode--copied');
                    codeBox.setAttribute('data-tooltip', 'Nhấp để sao chép mã HLV');
                    codeBox.setAttribute('title', 'Nhấp để sao chép mã HLV');
                }, 2000);
            }).catch(function(err) {
                console.error('Failed to copy:', err);
                alert('Không thể sao chép mã số. Vui lòng thử lại hoặc sao chép thủ công.');
            });
        } else {
            // Fallback for older browsers
            var textArea = document.createElement("textarea");
            textArea.value = code;
            textArea.style.position = "fixed";
            textArea.style.left = "-999999px";
            textArea.style.top = "-999999px";
            document.body.appendChild(textArea);
            textArea.focus();
            textArea.select();
            try {
                document.execCommand('copy');
                codeBox.classList.add('adminPersonnelPage_card_code--copied', 'adminSidebar_header_trainerCode--copied');
                codeBox.setAttribute('data-tooltip', 'Đã sao chép!');
                codeBox.setAttribute('title', 'Đã sao chép!');
                setTimeout(function() {
                    codeBox.classList.remove('adminPersonnelPage_card_code--copied', 'adminSidebar_header_trainerCode--copied');
                    codeBox.setAttribute('data-tooltip', 'Nhấp để sao chép mã HLV');
                    codeBox.setAttribute('title', 'Nhấp để sao chép mã HLV');
                }, 2000);
            } catch (err) {
                console.error('Failed to copy:', err);
                alert('Không thể sao chép mã số. Vui lòng thử lại hoặc sao chép thủ công.');
            }
            document.body.removeChild(textArea);
        }
    }
</script>