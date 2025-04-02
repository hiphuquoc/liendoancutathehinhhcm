<form id="formSearch" method="get" action="#">
    <div class="searchBox">
        <div class="searchBox_item">
            <div class="input-group">
                <input type="text" class="form-control" id="search_name" name="search_name" placeholder="Tìm theo tên" value="{{ $params['search_name'] ?? null }}" onkeyup="loadProductWithDelay();">
                {{-- <button class="btn btn-primary waves-effect" id="button-addon2" type="submit" aria-label="Tìm">Tìm</button> --}}
            </div>
        </div>
        {{-- <div class="searchBox_item" style="margin-left:auto;text-align:right;">
            @php
                $xhtmlSettingView   = \App\Helpers\Setting::settingView('viewProductInfo', config('setting.admin_array_number_view'), $viewPerPage, $list->total());
                echo $xhtmlSettingView;
            @endphp
        </div> --}}
    </div>
</form>