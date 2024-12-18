<div class="row">
    <div class="col-lg-12 col-sm-12 col-xs-12">
        <div class="card-box effectDropdown">
            <div class="card-box-title">
                <h2>ĐĂNG KÝ <span>NHẬN BẢN TIN</span></h2>
            </div>
            <div class="card-box-input">
                <form id="registryEmail" method="GET">
                    <input 
                        type="email" 
                        name="registry_email" 
                        placeholder="Email của bạn...." 
                        class="card-input" 
                        oninput="validateWhenType(this, 'email');" 
                        required 
                    />
                    <!-- Button ẩn để hỗ trợ Enter -->
                    <button type="submit" style="display: none;"></button>
                </form>
                <div class="input-img" id="submitRegistryEmail" style="cursor: pointer;">
                    <img 
                        src="https://liendoancutathehinhhcm.storage.googleapis.com/storage/images/paper-plane.webp" 
                        alt="đăng ký nhận bản tin" 
                        class="img-fluid" 
                    />
                </div>
            </div>
            <div class="card-box-content">
                <p>Hãy tham gia để nhận được những thông tin mới nhất từ chúng tôi!</p>
            </div>
        </div>
    </div>
</div>
@push('modal')
    @include('wallpaper.modal.messageModal')
@endpush
