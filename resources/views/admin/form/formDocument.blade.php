<div id="viewPDF" class="pageAdminWithRightSidebar_main_content_item width100">
    <div class="card">
       <div class="card-header border-bottom">
          <h4 class="card-title">Thông tin tài liệu</h4>
       </div>
       <div class="card-body">
          <div class="formBox">
             <div class="formBox_full">
                <!-- One Row -->
                <div class="formBox_full_item">
                    <span data-toggle="tooltip" data-placement="top" title="" data-bs-original-title="
                        Tài liệu chỉ hỗ trợ upload định dạng .PDF
                    ">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-alert-circle explainInput">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="8" x2="12" y2="12"></line>
                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                    </svg>
                    <label class="form-label inputRequired" for="document">Tệp tài liệu định dạng .PDF</label>
                    </span>
                    <input class="form-control" type="file" id="document" name="document">
                    <div class="invalid-feedback">Không được để trống trường này</div>
                </div>
                <!-- One Row -->
                @if(!empty($item->file_cloud))
                    <div class="formBox_full_item">
                        
                        <label class="form-label">Xem trước nội dung file</label>
                        <div id="viewFilePDF">
    
                            <embed src="{{ \App\Helpers\Image::getUrlImageCloud($item->file_cloud) }}" type="application/pdf" width="100%" height="800px" />
    
                        </div>

                    </div>
                @endif

             </div>
          </div>
       </div>
    </div>
 </div>

 @push('scriptCustom')
    <Script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function () {
            // Lấy tham chiếu đến input file
            const fileInput = document.getElementById('document'); // Input file

            // Thêm sự kiện khi người dùng chọn file
            fileInput.addEventListener('change', function (event) {
                const file = event.target.files[0]; // Lấy file được chọn

                // Kiểm tra xem file có phải là PDF không
                if (file && file.type === 'application/pdf') {
                    // Tạo đường dẫn tạm thời cho file
                    const fileURL = URL.createObjectURL(file);

                    // Tìm phần tử xem trước hiện tại (nếu có)
                    let previewContainerWrapper = document.querySelector('.formBox_full_item #viewFilePDF');

                    // Nếu chưa có phần tử xem trước, tạo mới
                    if (!previewContainerWrapper) {
                        // Tạo thẻ bao bọc với class formBox_full_item
                        const wrapper = document.createElement('div');
                        wrapper.classList.add('formBox_full_item');

                        // Tạo label và nội dung xem trước
                        const label = document.createElement('label');
                        label.classList.add('form-label');
                        label.textContent = 'Xem trước nội dung file';

                        const previewContainer = document.createElement('div');
                        previewContainer.id = 'viewFilePDF';

                        const embed = document.createElement('embed');
                        embed.src = fileURL;
                        embed.type = 'application/pdf';
                        embed.width = '100%';
                        embed.height = '800px'; // Chiều cao mặc định

                        // Gắn các phần tử con vào container
                        previewContainer.appendChild(embed);
                        wrapper.appendChild(label);
                        wrapper.appendChild(previewContainer);

                        // Thêm cấu trúc HTML vào DOM
                        const formBoxFull = fileInput.closest('.formBox_full_item');
                        formBoxFull.insertAdjacentElement('afterend', wrapper);
                    } else {
                        // Nếu đã có phần tử xem trước, chỉ cập nhật src của thẻ <embed>
                        const embed = previewContainerWrapper.querySelector('embed');
                        if (embed) {
                            embed.src = fileURL; // Cập nhật đường dẫn mới
                        }
                    }

                    console.log('File PDF đã được chọn và hiển thị xem trước.');

                    // Cuộn màn hình đến thẻ có id="viewPDF"
                    const viewPDF = document.getElementById('viewPDF');
                    if (viewPDF) {
                        viewPDF.scrollIntoView({ behavior: 'smooth' }); // Cuộn mượt
                    }
                } else {
                    // Nếu không phải file PDF, thông báo lỗi
                    alert('Vui lòng chọn file có định dạng .PDF');
                    fileInput.value = ''; // Xóa giá trị input để người dùng chọn lại
                }
            });
        });
    </script>
@endpush