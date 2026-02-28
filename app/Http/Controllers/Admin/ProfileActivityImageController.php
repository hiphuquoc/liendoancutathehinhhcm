<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\Upload;
use App\Models\ProfileActivityImage;
use App\Models\Trainer;
use App\Models\Referee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * API cho Hình ảnh hoạt động (HLV / Trọng tài).
 * Upload, xóa, sắp xếp ngay lập tức qua AJAX – không cần nhấn Lưu form.
 */
class ProfileActivityImageController extends Controller
{
    /**
     * Kiểm tra user hiện tại có quyền sửa owner (trainer/referee) này không.
     */
    private function canModifyOwner(string $ownerType, int $ownerId): bool
    {
        $user = Auth::user();
        if ($user->hasRole('admin')) {
            return true;
        }
        if ($ownerType === ProfileActivityImage::OWNER_TYPE_TRAINER && $user->hasRole('trainer')) {
            $trainer = Trainer::where('user_id', $user->id)->where('id', $ownerId)->first();
            return $trainer !== null;
        }
        if ($ownerType === ProfileActivityImage::OWNER_TYPE_REFEREE && $user->hasRole('referee')) {
            $referee = Referee::where('user_id', $user->id)->where('id', $ownerId)->first();
            return $referee !== null;
        }
        return false;
    }

    /**
     * Upload một hoặc nhiều ảnh. POST: owner_type, owner_id, image[] (files).
     */
    public function upload(Request $request)
    {
        $request->validate([
            'owner_type' => 'required|in:trainer_info,referee_info',
            'owner_id'   => 'required|integer|min:1',
            'image'      => 'required',
            'image.*'    => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);
        $files = $request->file('image');
        if (!is_array($files)) {
            $files = $files ? [$files] : [];
        }

        if (!$this->canModifyOwner($request->owner_type, (int) $request->owner_id)) {
            return response()->json(['success' => false, 'message' => 'Bạn không có quyền thêm ảnh cho hồ sơ này.'], 403);
        }

        $ownerType = $request->owner_type;
        $ownerId   = (int) $request->owner_id;
        $folderUpload = config('main_'.env('APP_NAME').'.google_cloud_storage.wallpapers');
        $maxOrder = ProfileActivityImage::where('owner_type', $ownerType)->where('owner_id', $ownerId)->max('ordering') ?? 0;
        $created = [];

        foreach ($files as $idx => $file) {
            if (!$file->isValid()) {
                continue;
            }
            $fileName = $ownerType === ProfileActivityImage::OWNER_TYPE_TRAINER
                ? 'trainer-activity-'.$ownerId.'-'.time().'-'.$idx.'.'.config('image.extension')
                : 'referee-activity-'.$ownerId.'-'.time().'-'.$idx.'.'.config('image.extension');
            $dataPath = Upload::uploadWallpaper($file, $fileName, $folderUpload);
            if (!empty($dataPath)) {
                $maxOrder++;
                $row = ProfileActivityImage::create([
                    'owner_type' => $ownerType,
                    'owner_id'   => $ownerId,
                    'image'      => $dataPath,
                    'ordering'  => $maxOrder,
                ]);
                $thumbUrl = \App\Helpers\Image::getUrlImageSmallByUrlImage($row->image);
                $created[] = [
                    'id'        => $row->id,
                    'image'     => $row->image,
                    'thumb_url' => $thumbUrl,
                    'image_url' => $row->image_url,
                    'ordering'  => $row->ordering,
                ];
            }
        }

        return response()->json([
            'success' => true,
            'message' => count($created) ? 'Đã thêm '.count($created).' ảnh.' : 'Không có ảnh nào được thêm.',
            'items'   => $created,
        ]);
    }

    /**
     * Xóa một ảnh. POST: id (của profile_activity_images).
     */
    public function delete(Request $request)
    {
        $request->validate(['id' => 'required|integer|min:1']);
        $row = ProfileActivityImage::find($request->id);
        if (!$row) {
            return response()->json(['success' => false, 'message' => 'Ảnh không tồn tại.'], 404);
        }
        if (!$this->canModifyOwner($row->owner_type, $row->owner_id)) {
            return response()->json(['success' => false, 'message' => 'Bạn không có quyền xóa ảnh này.'], 403);
        }
        Upload::deleteWallpaper($row->image);
        $row->delete();
        return response()->json(['success' => true, 'message' => 'Đã xóa ảnh.']);
    }

    /**
     * Sắp xếp lại thứ tự. POST: owner_type, owner_id, order[] (mảng id theo thứ tự mới).
     */
    public function reorder(Request $request)
    {
        $request->validate([
            'owner_type' => 'required|in:trainer_info,referee_info',
            'owner_id'   => 'required|integer|min:1',
            'order'      => 'required|array',
            'order.*'    => 'integer|min:1',
        ]);

        if (!$this->canModifyOwner($request->owner_type, (int) $request->owner_id)) {
            return response()->json(['success' => false, 'message' => 'Bạn không có quyền sắp xếp.'], 403);
        }

        $ownerType = $request->owner_type;
        $ownerId   = (int) $request->owner_id;
        foreach ($request->order as $position => $id) {
            ProfileActivityImage::where('owner_type', $ownerType)
                ->where('owner_id', $ownerId)
                ->where('id', $id)
                ->update(['ordering' => $position]);
        }
        return response()->json(['success' => true, 'message' => 'Đã cập nhật thứ tự.']);
    }
}
