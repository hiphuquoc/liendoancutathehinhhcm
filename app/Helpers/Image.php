<?php

namespace App\Helpers;
use Illuminate\Support\Facades\DB;
use App\Models\Seo;

use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image as I;
use GuzzleHttp\Client;

class Image {

    /**
     * Host bucket GCS cũ (virtual-hosted) — rewrite sang GCS_PUBLIC_URL hiện tại.
     */
    private static array $legacyCloudHosts = [
        'https://liendoancutathehinhhcm.storage.googleapis.com/',
        'http://liendoancutathehinhhcm.storage.googleapis.com/',
        'https://storage.googleapis.com/liendoancutathehinhhcm/',
        'http://storage.googleapis.com/liendoancutathehinhhcm/',
    ];

    public static function getActionImageByType($image){
        $arrayAction            = [];
        if(!empty($image)){
            $infoImage          = pathinfo($image);
            $filename           = $infoImage['filename'];
            $tmp                = explode(config('image.keyType'), $filename);
            $key                = end($tmp);
            $fullAction         = config('image.type');
            if(key_exists($key, $fullAction)){
                $arrayAction    = $fullAction[$key];
            }else {
                $arrayAction    = $fullAction['default'];
            }
        }
        return $arrayAction;
    }

    public static function streamResizedImage($imageUrl, $width = 300){
        // Tạo một phiên làm việc mới với Guzzle
        $client = new Client();

        // Tải ảnh từ URL
        $response = $client->get(env('APP_URL').'/'.$imageUrl);

        // Đọc dữ liệu ảnh
        $imageData = $response->getBody()->getContents();

        // Xử lý ảnh với Intervention Image
        $image = I::make($imageData);

        // Kích thước mới (ví dụ: 300px chiều rộng, tự động chiều cao)
        $image->resize($width, null, function ($constraint) {
            $constraint->aspectRatio();
        });

        $resizedImageData = $image->encode('data-url');

        return $resizedImageData;
    }

    /**
     * Base URL public GCS theo config dự án (.env GCS_PUBLIC_URL).
     */
    public static function getCloudDomain(): string
    {
        $domain = config('main_'.env('APP_NAME').'.google_cloud_storage.default_domain');
        if (empty($domain)) {
            $domain = rtrim((string) config('services.gcs.public_url', ''), '/').'/';
        }
        return rtrim((string) $domain, '/').'/';
    }

    /**
     * Chuẩn hóa path/URL ảnh về domain GCS hiện tại (đồng bộ bucket mới + rewrite URL cũ).
     */
    public static function normalizeCloudUrl(?string $urlImage): ?string
    {
        if ($urlImage === null || $urlImage === '') {
            return null;
        }

        $domain = self::getCloudDomain();

        foreach (self::$legacyCloudHosts as $legacy) {
            if (stripos($urlImage, $legacy) === 0) {
                return $domain.ltrim(substr($urlImage, strlen($legacy)), '/');
            }
        }

        if (filter_var($urlImage, FILTER_VALIDATE_URL)) {
            return $urlImage;
        }

        // Path local Storage::url() thừa public — chỉ normalize path, vẫn gắn GCS
        $urlImage = str_replace('/storage/public/', '/storage/', $urlImage);

        return $domain.ltrim($urlImage, '/');
    }

    public static function getUrlImageCloud($urlImage){
        return self::normalizeCloudUrl($urlImage);
    }

    public static function getUrlImageMiniByUrlImage($urlImage){
        $result     = null;
        if(!empty($urlImage)){
            $url = self::normalizeCloudUrl($urlImage);
            $tmp    = pathinfo($url);
            $result = $tmp['dirname'].'/'.$tmp['filename'].'-mini.'.$tmp['extension'];
        }
        return $result;
    }

    public static function getUrlImageSmallByUrlImage($urlImage){
        $result     = null;
        if(!empty($urlImage)){
            $url = self::normalizeCloudUrl($urlImage);
            $tmp    = pathinfo($url);
            $result = $tmp['dirname'].'/'.$tmp['filename'].'-small.'.$tmp['extension'];
        }
        return $result;
    }

    public static function getUrlImageLargeByUrlImage($urlImage){
        $result     = null;
        if(!empty($urlImage)){
            $url = self::normalizeCloudUrl($urlImage);
            $tmp    = pathinfo($url);
            $result = $tmp['dirname'].'/'.$tmp['filename'].'-large.'.$tmp['extension'];
        }
        return $result;
    }

    public static function isValidImageUrl($url) {
        // Kiểm tra URL có hợp lệ không
        if (filter_var($url, FILTER_VALIDATE_URL) === false) {
            return false;
        }
    
        // Sử dụng cURL để kiểm tra xem tệp có tồn tại không
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_NOBODY, true); // Chỉ kiểm tra xem URL có tồn tại
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
    
        // Kiểm tra mã trạng thái HTTP
        if ($httpCode == 200) {
            // Kiểm tra nếu có thể lấy kích thước hình ảnh
            $size = @getimagesize($url);
            return $size !== false;
        }
    
        return false;
    }
}
