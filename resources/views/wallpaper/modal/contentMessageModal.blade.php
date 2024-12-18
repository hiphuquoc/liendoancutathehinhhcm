<div class="modalBox_box_head">
    {{ $title ?? null }}
</div>
<div class="modalBox_box_body">
    {!! $content ?? null !!}
</div>
<div class="modalBox_box_close" onclick="openCloseModal('messageModal');">
    <i class="fa-solid fa-xmark"></i>
</div>