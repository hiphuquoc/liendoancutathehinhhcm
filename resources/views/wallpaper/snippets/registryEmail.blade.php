<div class="row">
    <div class="col-lg-12 col-sm-12 col-xs-12">
        <div class="newsletter-box effectDropdown">
            <div class="newsletter-box_content">
                <h2 class="section-title">ĐĂNG KÝ <span class="highlight">NHẬN BẢN TIN</span></h2>
                <p>Hãy tham gia để nhận được những thông tin mới nhất từ chúng tôi!</p>
            </div>
            <div class="newsletter-box_form">
                <form id="registryEmail" method="GET" onsubmit="submitFormRegistryEmail('registryEmail'); return false;">
                    <div class="input-group-custom">
                        <input 
                            type="email" 
                            name="registry_email" 
                            placeholder="Email của bạn...." 
                            oninput="validateWhenType(this, 'email');" 
                            required 
                        />
                        <button type="submit">
                            <span>ĐĂNG KÝ</span> <i class="fa fa-paper-plane"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('modal')
    @include('wallpaper.modal.messageModal')
@endpush
