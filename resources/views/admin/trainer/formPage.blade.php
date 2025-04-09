<div class="formBox">
    <div class="formBox_full">
        <!-- One Row -->
        <div class="formBox_column2_item_row">
            <div class="inputWithNumberChacractor">
                <span data-toggle="tooltip" data-placement="top" title="
                    Ngăn cách bởi dấu |
                ">
                    <i class="explainInput" data-feather='alert-circle'></i>
                    <label class="form-label inputRequired" for="title">Họ và tên | Chức vụ</label>
                </span>
            </div>
            <input type="text" class="form-control" id="title" name="title" value="{{ old('title') ?? $itemSeo->title ?? null }}" required>
            <div class="invalid-feedback">{{ config('admin.massage_validate.not_empty') }}</div>
        </div>
        @if($language=='vi')
            
            <!-- One Row -->
            <div class="formBox_column2_item_row">
                <div class="inputWithNumberChacractor">
                    <span data-toggle="tooltip" data-placement="top" title="
                        Đây là Số điện thoại của Huấn luyện viên hiển thị trên website
                    ">
                        <i class="explainInput" data-feather='alert-circle'></i>
                        <label class="form-label inputRequired" for="phone">Số điện thoại</label>
                    </span>
                </div>
                <input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone') ?? $item->phone ?? null }}" required>
                <div class="invalid-feedback">{{ config('admin.massage_validate.not_empty') }}</div>
            </div>
            <!-- One Row -->
            <div class="formBox_column2_item_row">
                <div class="inputWithNumberChacractor">
                    <span data-toggle="tooltip" data-placement="top" title="
                        Đây là Email của Huấn luyện viên hiển thị trên website
                    ">
                        <i class="explainInput" data-feather='alert-circle'></i>
                        <label class="form-label inputRequired" for="email">Email</label>
                    </span>
                </div>
                <input type="text" class="form-control" id="email" name="email" value="{{ old('email') ?? $item->email ?? null }}" required>
                <div class="invalid-feedback">{{ config('admin.massage_validate.not_empty') }}</div>
            </div>

        @endif
        
    </div>
</div>