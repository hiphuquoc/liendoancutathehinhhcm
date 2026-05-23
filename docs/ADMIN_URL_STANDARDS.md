# Quy Chuẩn URL Admin Panel

## Tổng Quan
Tài liệu này mô tả quy chuẩn URL cho hệ thống Admin Panel của liendoan.dev để đảm bảo tính nhất quán và dễ hiểu.

## Cấu Trúc URL Cơ Bản

### Prefix Chung
Tất cả các routes admin (trừ account và personnel) đều nằm trong prefix `/he-thong`

### Quy Chuẩn Routes Chính

#### 1. **List (Danh sách)**
- **Pattern**: `GET /he-thong/{resource}` hoặc `GET /he-thong/{resource}/list`
- **Route Name**: `admin.{resource}.list`
- **Ví dụ**: 
  - `/he-thong/document` → `admin.document.list`
  - `/he-thong/page/list` → `admin.page.list`

#### 2. **View/Form (Xem/Chỉnh sửa)**
- **Pattern**: `GET /he-thong/{resource}/view`
- **Route Name**: `admin.{resource}.view`
- **Query Params**: `?id={id}&language={lang}&type={create|edit|copy}`
- **Ví dụ**: 
  - `/he-thong/document/view?id=1&language=vi` → `admin.document.view`
  - `/he-thong/page/view?id=2&type=edit` → `admin.page.view`

#### 3. **Create/Update (Tạo/Cập nhật)**
- **Pattern**: `POST /he-thong/{resource}/createAndUpdate`
- **Route Name**: `admin.{resource}.createAndUpdate`
- **Ví dụ**: 
  - `POST /he-thong/document/createAndUpdate` → `admin.document.createAndUpdate`

#### 4. **Delete (Xóa)**
- **Pattern**: `GET /he-thong/{resource}/delete`
- **Route Name**: `admin.{resource}.delete`
- **Query Params**: `?id={id}`
- **Ví dụ**: 
  - `/he-thong/document/delete?id=1` → `admin.document.delete`

## Routes Đặc Biệt

### Account Routes (Ngoài /he-thong)
- **Profile**: `GET /account/profile` → `admin.account.profile`
- **Update Profile**: `POST /account/updateProfile` → `admin.account.updateProfile`
- **Change Password**: `GET /account/changePassword` → `admin.account.changePassword`
- **Update Password**: `POST /account/updatePassword` → `admin.account.updatePassword`

### Personnel Routes (Trong /he-thong)
- **Trainer List**: `GET /he-thong/trainer` → `admin.trainer.list`
- **Trainer View**: `GET /he-thong/trainer/view` → `admin.trainer.view`
- **Trainer Create/Update**: `POST /he-thong/trainer/createAndUpdate` → `admin.trainer.createAndUpdate`
- **Trainer Delete**: `GET /he-thong/trainer/delete?id={id}` → `admin.trainer.delete`
- **Referee List**: `GET /he-thong/referee` → `admin.referee.list`
- **Referee View**: `GET /he-thong/referee/view` → `admin.referee.view`
- **Referee Create/Update**: `POST /he-thong/referee/createAndUpdate` → `admin.referee.createAndUpdate`
- **Referee Delete**: `GET /he-thong/referee/delete?id={id}` → `admin.referee.delete`

**Note**: Các URL cũ (`/trainer`, `/referee`) sẽ tự động redirect sang URL mới (`/he-thong/trainer`, `/he-thong/referee`) để đảm bảo backward compatibility.

### Image Management
- **List**: `GET /he-thong/image` → `admin.image.list`
- **Upload**: `POST /he-thong/image/uploadImages` → `admin.image.uploadImages`
- **Load Image**: `GET /he-thong/image/loadImage` → `admin.image.loadImage`
- **Load Modal**: `GET /he-thong/image/loadModal` → `admin.image.loadModal`
- **Change Image**: `POST /he-thong/image/changeImage` → `admin.image.changeImage`
- **Remove Image**: `POST /he-thong/image/removeImage` → `admin.image.removeImage`

## Quy Tắc Đặt Tên

### Resource Names
- Sử dụng **camelCase** cho resource names: `categoryBlog`, `freeWallpaper`
- Sử dụng **singular** cho resource: `page`, `blog`, `document`
- Tránh viết tắt không rõ ràng

### Action Names
- **list**: Danh sách
- **view**: Form xem/chỉnh sửa
- **createAndUpdate**: Tạo mới hoặc cập nhật
- **delete**: Xóa
- **upload**: Upload file
- **load**: Load dữ liệu qua AJAX

### Route Names
- Format: `admin.{resource}.{action}`
- Ví dụ: `admin.document.list`, `admin.page.view`

## Best Practices

1. **Nhất quán**: Tất cả routes cùng loại nên theo cùng pattern
2. **Rõ ràng**: URL phải dễ hiểu, không cần giải thích
3. **RESTful**: Ưu tiên RESTful conventions khi có thể
4. **Tương thích**: Giữ backward compatibility khi refactor

## Migration Plan

### Phase 1: Chuẩn hóa List Routes ✅
- [x] Document: `/he-thong/document` ✅
- [x] Blog: `/he-thong/blog` ✅
- [x] CategoryBlog: `/he-thong/categoryBlog` ✅
- [x] Image: `/he-thong/image` ✅
- [x] Page: `/he-thong/page` (alias `/list` vẫn hoạt động) ✅
- [x] Tag: `/he-thong/tag` (alias `/list` vẫn hoạt động) ✅
- [x] Category: `/he-thong/category` (alias `/list` vẫn hoạt động) ✅
- [x] Trainer: `/he-thong/trainer` (redirect từ `/trainer`) ✅
- [x] Referee: `/he-thong/referee` (redirect từ `/referee`) ✅

### Phase 2: Chuẩn hóa View Routes
- Tất cả đã dùng `/view` ✅

### Phase 3: Chuẩn hóa Action Routes
- Tất cả đã dùng `/createAndUpdate` và `/delete` ✅

## Notes

- Các routes AJAX và helper có thể giữ nguyên pattern hiện tại
- Routes đặc biệt (như image upload, translate) có thể có pattern riêng
- Ưu tiên tính nhất quán cho các routes chính (CRUD)

