<div id="messageModal" class="modalBox">
    <div class="modalBox_bg js_toggleMessageModal" onclick="openCloseModal('messageModal');"></div>
    <div class="modalBox_box">
        @include('wallpaper.modal.contentMessageModal', [
            'title' => $title ?? null,
            'content'   => $content ?? null
        ])
    </div>
</div>