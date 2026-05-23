<section class="hero-contact-form">
  <div class="container">
    <div class="row">
      <div class="col-lg-12 col-sm-12 col-xs-12">
        <div class="contact-form">
          <form id="formContact" method="GET" onsubmit="submitFormRequestContact(event); return false;">
            <input type="text" name="full_name" class="contact-form-input" placeholder="Họ và tên" style="margin-top:0 !important;" required />
            <input type="phone" name="phone" class="contact-form-input" placeholder="Điện thoại" required />
            {{-- <input type="email" name="email" class="contact-form-input" placeholder="Địa chỉ email" style="margin-top:20px;" /> --}}
            <textarea class="contact-form-message" name="message" placeholder="Nội dung tin nhắn..."></textarea>
            <button type="submit" class="contact-form-btn">GỬI ĐI</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</section>

@pushonce('scriptCustom')
  <script type="text/javascript">
    /* Tính năng yêu cầu liên hệ ở contact */
    function submitFormRequestContact(event) {
      event.preventDefault();

      // Lấy dữ liệu từ form
      const form = document.getElementById('formContact');
      const formData = new FormData(form);

      // Chuyển dữ liệu form thành query string (GET request)
      const queryString = new URLSearchParams(formData).toString();

      // Gửi dữ liệu đi bằng fetch
      fetch(`/requestContact?${queryString}`, {
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
        // Bật thông báo sau khi gửi thành công
        setMessageModal(response.title, response.content);
      })
      .catch(error => {
        console.error("Fetch request failed:", error);
      });
    }
  </script>
@endpushonce