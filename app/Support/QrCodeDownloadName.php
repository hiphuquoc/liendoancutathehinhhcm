<?php

namespace App\Support;

use App\Helpers\Charactor;

/**
 * Tên file PNG khi tải QR hồ sơ.
 * Giữ slug; prefix STT lấy từ mã hồ sơ (cùng extractOrderNumber lúc import Excel).
 */
class QrCodeDownloadName
{
    public static function png(?string $personnelCode, ?string $slug, ?string $fallbackName = null): string
    {
        $base = self::slugPart($slug, $fallbackName);
        $stt = PersonnelImportOrderAllocator::extractOrderNumber((string) $personnelCode);

        if ($stt !== null) {
            return $stt . '-' . $base . '.png';
        }

        return $base . '.png';
    }

    private static function slugPart(?string $slug, ?string $fallbackName): string
    {
        $slug = trim((string) $slug);
        if ($slug !== '') {
            return $slug;
        }

        $fromName = Charactor::convertStrToUrl((string) $fallbackName);

        return $fromName !== '' ? $fromName : 'qrcode';
    }
}
