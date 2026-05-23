{{-- 
    Component: Admin Form Sidebar
    Usage: @include('admin.components.formSidebar', ['sections' => [...]])
--}}
<div class="adminFormSidebar">
    <div class="adminFormSidebar_sticky">
        {{ $slot ?? '' }}
    </div>
</div>

