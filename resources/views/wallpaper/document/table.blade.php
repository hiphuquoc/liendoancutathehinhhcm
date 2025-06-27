@if(!empty($documents) && $documents->isNotEmpty())
  <div class="tableDocument">
    <div class="tableContainer">
      <table>
        <thead>
          <tr>
            <th width="50px">#</th>
            <th>Tên tài liệu</th>
            <th width="120px">Ngày đăng</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          @foreach($documents as $document)
            <tr>
              <td>{{ $loop->index + 1 }}</td>
              <td>{{ $document->seo->title }}</td>
              <td>{{ date('d/m/Y', strtotime($document->seo->created_at)) }}</td>
              @php
                $urlFile = \App\Helpers\Image::getUrlImageCloud($document->file_cloud);
              @endphp
              <td class="action">
                <a href="{{ $urlFile }}" target="_blank">
                  <i class="fa-solid fa-eye"></i>
                </a>
                <a href="{{ $urlFile }}" target="_blank">
                  <i class="fa-solid fa-download"></i>
                </a>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
  <div class="noteDocument">*Lưu ý: Các tài liệu này thuộc bản quyền của Liên đoàn Cử tạ - Thể hình TP.HCM và chỉ được sử dụng cho mục đích giảng dạy trong khuôn khổ các chương trình đào tạo của Liên đoàn. Mọi hình thức sao chép, phát hành hoặc sử dụng ngoài phạm vi này đều phải có sự cho phép bằng văn bản từ Liên đoàn.</div>
@endif

{{-- @push('scriptCustom')
  <script type="text/javascript">
    function downloadDocument(urlFile, fileName) {
      fetch(urlFile)
        .then(response => response.blob())
        .then(blob => {
          // Tạo URL tạm thời từ blob
          const url = window.URL.createObjectURL(blob);
          
          // Tạo thẻ <a> ẩn
          const link = document.createElement('a');
          link.href = url;
          
          // Đặt tên file tải về (nếu không có tên file thì lấy từ URL)
          link.download = fileName ? `${fileName}${urlFile.substring(urlFile.lastIndexOf('.'))}` : urlFile.substring(urlFile.lastIndexOf('/') + 1);
          
          // Thêm vào DOM và kích hoạt tải
          document.body.appendChild(link);
          link.click();
          
          // Dọn dẹp
          document.body.removeChild(link);
          window.URL.revokeObjectURL(url);
        })
        .catch(error => {
          console.error('Download failed:', error);
          // Fallback: mở link trong tab mới nếu download thất bại
          window.open(urlFile, '_blank');
        });
    }
  </script>
@endpush --}}