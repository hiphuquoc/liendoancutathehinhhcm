<div class="sidebar-block sidebar-search">
    <h3 class="sidebar-block__title">Tìm kiếm</h3>
    <form id="searchForm" class="sidebar-search__form" action="{{ route('routing', ['slug' => 'tin-tuc']) }}" method="GET" role="search" aria-label="Tìm kiếm bài viết">
        <label for="search_name" class="visually-hidden">Tìm kiếm</label>
        <input id="search_name" name="search_name" type="search" class="sidebar-search__input" placeholder="Bạn đang cần tìm gì?" value="{{ request()->get('search_name') ?? '' }}" autocomplete="off" />
        <button type="submit" class="sidebar-search__btn" aria-label="Gửi tìm kiếm">
            <svg class="sidebar-search__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
            </svg>
        </button>
    </form>
</div>
@pushonce('scriptCustom')
<script type="text/javascript">
document.addEventListener('DOMContentLoaded', function () {
    var searchForm = document.getElementById('searchForm');
    var searchInput = document.getElementById('search_name');
    if (searchForm && searchInput) {
        searchInput.addEventListener('keydown', function (e) {
            if (e.key === 'Enter') searchForm.submit();
        });
    }
});
</script>
@endpushonce
