# Tối ưu hover cho thiết bị cảm ứng (touch)

## Mục đích

Trên mobile/tablet, khi người dùng chạm vào màn hình, trình duyệt có thể kích hoạt trạng thái `:hover` và **giữ nguyên** sau khi ngón tay rời đi, khiến giao diện trông như “dính” hover (đổi màu, transform, box-shadow). Tài liệu này mô tả cách dự án tắt/giảm hiệu ứng hover trên thiết bị không có chuột để tránh trải nghiệm khó chịu.

## Giải pháp đã áp dụng

### 1. Phát hiện thiết bị (script)

- **Vị trí**
  - **Admin:** `resources/views/admin/snippets/scriptDefault.blade.php` (đầu file, trước jQuery).
  - **Site public:** `resources/views/wallpaper/snippets/head.blade.php` (ngay sau thẻ meta charset).
- **Hành vi:** Dùng `window.matchMedia('(hover: none), (pointer: coarse)')`:
  - Nếu **có** (màn hình cảm ứng): thêm class `no-hover` vào `<html>`.
  - Nếu **không**: xóa class `no-hover`.
  - Lắng nghe sự kiện `change` để cập nhật khi người dùng đổi thiết bị (ví dụ cắm rút chuột).

### 2. Override CSS khi có class `no-hover`

- **Admin:** Cuối file `resources/sources/admin/admin-layout-redesign.scss`:
  ```scss
  html.no-hover *:hover {
    transform: none !important;
    box-shadow: none !important;
  }
  ```
- **Site public:** Inline trong `resources/views/wallpaper/snippets/head.blade.php`:
  ```html
  <style>html.no-hover *:hover{transform:none!important;box-shadow:none!important}</style>
  ```

Khi `html` có class `no-hover`, mọi phần tử ở trạng thái `:hover` sẽ bị tắt `transform` và `box-shadow`, giảm hiệu ứng “dính” hover khi chạm.

### 3. Mixin SCSS (admin, tùy chọn mở rộng)

Trong `admin-layout-redesign.scss` có mixin:

```scss
@mixin hover-only {
  @media (hover: hover) and (pointer: fine) {
    @content;
  }
}
```

Có thể bọc từng khối `&:hover { ... }` bằng `@include hover-only { &:hover { ... } }` để chỉ áp dụng hover khi thiết bị có chuột. Hiện tại đã bọc thủ công một số block (sidebar, nav item); phần còn lại dùng override toàn cục ở trên.

---

## Tác động với Google SEO

- **Không thay đổi nội dung:** Script chỉ thêm/xóa class `no-hover` trên `<html>`, không ẩn nội dung, không thay đổi text hay cấu trúc HTML.
- **Crawler thường không bị ảnh hưởng:** Googlebot dùng viewport kiểu desktop, `(hover: none)` và `(pointer: coarse)` thường là **false**, nên `<html>` không nhận class `no-hover`. Trang vẫn render như bình thường khi crawl.
- **Không cloaking:** Cùng một HTML, chỉ khác cách áp dụng CSS theo capability của thiết bị (có chuột hay không), không phân biệt user-agent crawler.
- **Core Web Vitals / indexing:** Không có thay đổi có ý nghĩa đến indexing hay điểm LCP/FID/CLS; script chạy nhanh, không chặn render.

Kết luận: Cách làm này **an toàn với SEO**, chỉ tối ưu trải nghiệm trên thiết bị cảm ứng.

---

## Tóm tắt file đã sửa (liendoan.dev)

| File | Thay đổi |
|------|----------|
| `resources/views/admin/snippets/scriptDefault.blade.php` | Thêm script phát hiện touch và thêm/xóa class `no-hover` trên `<html>`. |
| `resources/views/wallpaper/snippets/head.blade.php` | Thêm cùng script + thẻ `<style>` override `transform`/`box-shadow` khi `html.no-hover`. |
| `resources/sources/admin/admin-layout-redesign.scss` | Thêm mixin `hover-only`, thêm block `html.no-hover *:hover { ... }` ở cuối file. |

---

## Áp dụng cho dự án khác

Đã áp dụng cùng cơ chế cho:

- **hoptackinhdoanh.dev** — xem `hoptackinhdoanh.dev/docs/hover-optimization-touch-devices.md`
- **zenpot.dev** — xem `zenpot.dev/docs/hover-optimization-touch-devices.md`
- **chuyentauvanhoc.dev** — xem `chuyentauvanhoc.dev/docs/hover-optimization-touch-devices.md`
- **wallsora.dev** — xem `wallsora.dev/docs/hover-optimization-touch-devices.md`

Với dự án mới: thêm **cùng một script** và **cùng một đoạn style** vào head của site public (main), auth (nếu có) và admin, rồi ghi lại trong `docs/hover-optimization-touch-devices.md` của dự án đó.
