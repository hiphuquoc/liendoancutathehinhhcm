<?php

namespace App\Helpers;

use Intervention\Image\ImageManagerStatic;
use App\Models\SystemFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

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
    
    public static function uploadVideo($requestFile, $filename, $folderUpload) {
        $result = null;

        Log::channel('single')->info('[VideoUpload] START', [
            'filename' => $filename,
            'folderUpload' => $folderUpload,
            'has_request_file' => !empty($requestFile),
            'request_file_class' => $requestFile ? get_class($requestFile) : null,
        ]);

        if (!empty($requestFile)) {
            try {
                $videoFile = $requestFile;
                $extension = pathinfo($filename, PATHINFO_EXTENSION);

                $allowedExtensions = ['mp4', 'webm', 'mov', 'avi', 'mkv', 'flv'];
                if (!in_array(strtolower($extension), $allowedExtensions)) {
                    Log::channel('single')->warning('[VideoUpload] Extension not allowed', [
                        'extension' => $extension,
                        'allowed' => $allowedExtensions,
                    ]);
                    return $result;
                }

                $fileUrl = $folderUpload . $filename;
                $realPath = $videoFile->getRealPath();
                $fileSizeBytes = $realPath && file_exists($realPath) ? filesize($realPath) : null;

                Log::channel('single')->info('[VideoUpload] Before GCS put', [
                    'fileUrl' => $fileUrl,
                    'fileSizeBytes' => $fileSizeBytes,
                    'fileSizeMB' => $fileSizeBytes ? round($fileSizeBytes / 1024 / 1024, 2) : null,
                    'gcs_disk_exists' => Storage::hasMacro('disk') ? 'yes' : 'check',
                ]);

                $gcsDisk = Storage::disk('gcs');
                Log::channel('single')->info('[VideoUpload] GCS disk obtained', [
                    'driver' => config('filesystems.disks.gcs.driver') ?? 'unknown',
                    'bucket' => config('filesystems.disks.gcs.bucket') ?? 'not_set',
                ]);

                Log::channel('single')->info('[VideoUpload] Reading file contents...');
                $contents = file_get_contents($realPath);
                $contentsSize = $contents !== false ? strlen($contents) : 0;
                Log::channel('single')->info('[VideoUpload] File contents read', [
                    'contentsSize' => $contentsSize,
                    'memory_usage_mb' => round(memory_get_usage(true) / 1024 / 1024, 2),
                ]);

                if ($contents === false || $contentsSize === 0) {
                    Log::channel('single')->error('[VideoUpload] file_get_contents failed or empty', [
                        'realPath' => $realPath,
                    ]);
                    return $result;
                }

                Log::channel('single')->info('[VideoUpload] Calling gcsDisk->put()...');
                $putResult = $gcsDisk->put($fileUrl, $contents);
                Log::channel('single')->info('[VideoUpload] GCS put completed', [
                    'putResult' => $putResult,
                    'fileUrl' => $fileUrl,
                ]);

                if ($putResult !== true) {
                    Log::channel('single')->error('[VideoUpload] GCS put() returned false - upload failed', [
                        'fileUrl' => $fileUrl,
                        'bucket' => config('filesystems.disks.gcs.bucket'),
                    ]);
                    throw new \RuntimeException(
                        'Không thể tải video lên Google Cloud Storage. Kiểm tra quyền ghi bucket và credentials (service account) trên server.'
                    );
                }

                $result = $fileUrl;
            } catch (\Throwable $e) {
                Log::channel('single')->error('[VideoUpload] Exception in uploadVideo', [
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'exception_class' => get_class($e),
                ]);
                Log::channel('single')->error('[VideoUpload] Stack trace: ' . $e->getTraceAsString());
                throw $e;
            }
        } else {
            Log::channel('single')->warning('[VideoUpload] requestFile is empty, returning null');
        }

        Log::channel('single')->info('[VideoUpload] END', ['result' => $result]);
        return $result;
    }

    public static function deleteVideo($urlCloud){
        $flag = false;
        if(!empty($urlCloud)){
            /* xóa video trong google_cloud_storage */
            Storage::disk('gcs')->delete($urlCloud);
            $flag = true;
        }
        return $flag;
    }
    
}