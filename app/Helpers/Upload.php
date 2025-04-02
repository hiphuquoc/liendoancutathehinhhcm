<?php

namespace App\Helpers;

use Intervention\Image\ImageManagerStatic;
use App\Models\SystemFile;
use Illuminate\Support\Facades\Storage;

class Upload {
    
    public static function uploadCustom($requestImage, $name = null){
        $result             = null;
        if(!empty($requestImage)){
            // ===== folder upload
            $folderUpload   = config('image.folder_upload');
            // ===== image upload
            $image          = $requestImage;
            $extension      = config('image.extension');
            // ===== set filename & checkexists
            $name           = $name ?? time();
            $filename       = $name.'-'.time().'.'.$extension;
            $fileUrl        = $folderUpload.$filename;
            // save image resize
            ImageManagerStatic::make($image->getRealPath())
                ->encode($extension, config('image.quality'))
                ->save(Storage::path($fileUrl));
            $result         = $fileUrl;
        }
        return $result;
    }

    public static function uploadWallpaper($requestImage, $filename, $folderUpload){
        $result = null;
        if (!empty($requestImage)) {
            // ===== folder upload
            $image              = $requestImage;
            // ===== set filename & checkexists
            $filenameNotExtension = pathinfo($filename)['filename'];
            $extension          = pathinfo($filename)['extension'];
            $fileUrl            = $folderUpload . $filename;
            $gcsDisk            = Storage::disk('gcs');
            // Resize and save the main image
            $imageTmp           = ImageManagerStatic::make($image->getRealPath());
            $percentPixel       = $imageTmp->width() / $imageTmp->height();
            $widthImage         = $imageTmp->width();
            $heightImage        = $imageTmp->height();
            $gcsDisk->put($fileUrl, $imageTmp->encode($extension, config('image.quality'))->resize($widthImage, $heightImage)->stream());
            $result             = $fileUrl;
            // Resize and save the large image
            $fileUrlLarge       = $folderUpload . $filenameNotExtension . '-large.' . $extension;
            $widthImageLarge    = config('image.resize_large_width');
            $heightImageLarge   = $widthImageLarge / $percentPixel;
            $gcsDisk->put($fileUrlLarge, $imageTmp->encode($extension, config('image.quality'))->resize($widthImageLarge, $heightImageLarge)->stream());
            // Resize and save the small image
            $fileUrlSmall       = $folderUpload . $filenameNotExtension . '-small.' . $extension;
            $widthImageSmall    = config('image.resize_small_width');
            $heightImageSmall   = $widthImageSmall / $percentPixel;
            $gcsDisk->put($fileUrlSmall, $imageTmp->encode($extension, config('image.quality'))->resize($widthImageSmall, $heightImageSmall)->stream());
            // Resize and save the mini image
            $fileUrlMini        = $folderUpload . $filenameNotExtension . '-mini.' . $extension;
            $widthImageMini     = config('image.resize_mini_width');
            $heightImageMini    = $widthImageMini / $percentPixel;
            $gcsDisk->put($fileUrlMini, $imageTmp->encode($extension, config('image.quality'))->resize($widthImageMini, $heightImageMini)->stream());
        }
        return $result;
    }

    public static function deleteWallpaper($urlCloud){
        $flag   = false;
        if(!empty($urlCloud)){
            $tmp = pathinfo($urlCloud);
            $filename = $tmp['filename'];
            $extension = $tmp['extension'];
            $foldername = $tmp['dirname'];
            /* xóa wallpaper trong google_cloud_storage */
            Storage::disk('gcs')->delete($urlCloud);
            /* xóa wallpaper Large trong google_cloud_storage */
            Storage::disk('gcs')->delete($foldername.'/'.$filename.'-large.'.$extension);
            /* xóa wallpaper Small trong google_cloud_storage */
            Storage::disk('gcs')->delete($foldername.'/'.$filename.'-small.'.$extension);
            /* xóa wallpaper Mini trong google_cloud_storage */
            Storage::disk('gcs')->delete($foldername.'/'.$filename.'-mini.'.$extension);
            $flag = true;
        }
        return $flag;
    }

    public static function uploadDocument($requestFile, $filename, $folderUpload) {
        $result = null;
        // Kiểm tra xem có file được chọn không
        if (!empty($requestFile)) {
            
            // ===== Folder upload
            $pdfFile = $requestFile;

            // ===== Set filename & check exists
            $extension = pathinfo($filename)['extension'];

            // Chỉ chấp nhận file PDF
            if ($extension !== 'pdf') {
                return $result; // Trả về null nếu không phải file PDF
            }

            // Đường dẫn lưu file
            $fileUrl = $folderUpload . $filename;

            // Sử dụng disk GCS (Google Cloud Storage)
            $gcsDisk = Storage::disk('gcs');

            // Lưu file PDF trực tiếp lên storage
            $gcsDisk->put($fileUrl, file_get_contents($pdfFile->getRealPath()));

            // Trả về đường dẫn file
            $result = $fileUrl;
        }

        return $result;
    }
    
}