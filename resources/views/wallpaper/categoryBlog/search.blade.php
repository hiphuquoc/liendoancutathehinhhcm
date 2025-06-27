<div class="search_box">
  <div class="blog_title">
    <h3>Tìm kiếm</h3>
  </div>
  <div id="searchbox" class="show">
    <form id="searchForm" action="{{ route('routing', ['slug' => 'tin-tuc']) }}" method="GET">
      <input name="search_name" type="text" placeholder="Bạn đang cần tìm gì?" value="{{ request()->get('search_name') ?? '' }}" />
      <svg id="submitFormSearch" class="magnify" viewBox="0 0 48 48" aria-hidden="true" style="cursor: pointer;">
        <path d="M31 28h-1.59l-.55-.55C30.82 25.18 32 22.23 32 19c0-7.18-5.82-13-13-13S6 11.82 6 19s5.82 13 13 13c3.23 0 6.18-1.18 8.45-3.13l.55.55V31l10 9.98L40.98 38 31 28zm-12 0c-4.97 0-9-4.03-9-9s4.03-9 9-9 9 4.03 9 9-4.03 9-9 9z"></path>
      </svg>
    </form>
  </div>
</div>
@pushonce('scriptCustom')
<script type="text/javascript">
  document.addEventListener('DOMContentLoaded', function () {
    const searchForm = document.getElementById('searchForm');
    const submitFormSearch = document.getElementById('submitFormSearch');
    const searchInput = searchForm.querySelector('input[name="search_name"]');

    // Click vào SVG để submit form
    submitFormSearch.addEventListener('click', function () {
      searchForm.submit();
    });

    // Nhấn Enter trong input sẽ submit form
    searchInput.addEventListener('keydown', function (event) {
      if (event.key === 'Enter') {
        event.preventDefault(); // Ngăn hành vi mặc định
        searchForm.submit(); // Thực hiện submit form
      }
    });
  });
</script>
@endpushonce