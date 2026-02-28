@php
    $searchActionUrl = $sidebarSearchActionUrl ?? request()->url();
@endphp
<div class="sidebar-block sidebar-search">
    <h3 class="sidebar-block__title">Tìm kiếm</h3>
    <form id="sidebarBlogSearchForm" class="sidebar-search__form" action="{{ $searchActionUrl }}" method="GET" role="search" aria-label="Tìm kiếm bài viết">
        <label for="sidebar_search_name" class="visually-hidden">Tìm kiếm</label>
        <input id="sidebar_search_name" name="search_name" type="search" class="sidebar-search__input" placeholder="Bạn đang cần tìm gì?" value="{{ request()->query('search_name', '') }}" autocomplete="off" />
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
    var form = document.getElementById('sidebarBlogSearchForm');
    var input = document.getElementById('sidebar_search_name');
    if (form && input) {
        input.addEventListener('keydown', function (e) {
            if (e.key === 'Enter') form.submit();
        });
    }
});
</script>
@endpushonce
