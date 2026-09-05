<?php

namespace App\Support;

/**
 * Cấp STT cho mã hồ sơ khi import Excel.
 *
 * Ưu tiên STT cột 1 trong file. Chỉ sinh số mới khi dòng không có STT.
 * Dòng bị bỏ (trùng / lỗi) không chiếm số — tránh đôn STT các dòng sau.
 */
class PersonnelImportOrderAllocator
{
    /** @var array<int, true> */
    private array $used = [];

    public static function fromExistingCodes(iterable $codes): self
    {
        $allocator = new self();
        foreach ($codes as $code) {
            $number = self::extractOrderNumber((string) $code);
            if ($number !== null) {
                $allocator->used[$number] = true;
            }
        }

        return $allocator;
    }

    public static function parseFileStt($raw): ?int
    {
        if ($raw === null) {
            return null;
        }
        if (is_float($raw) || is_int($raw)) {
            if (!is_finite((float) $raw)) {
                return null;
            }
            $number = (int) $raw;
            if ($number <= 0 || abs(((float) $raw) - $number) > 0.0001) {
                return null;
            }

            return $number;
        }

        $value = trim((string) $raw);
        if ($value === '' || !preg_match('/^\d+$/', $value)) {
            return null;
        }
        $number = (int) $value;

        return $number > 0 ? $number : null;
    }

    public static function extractOrderNumber(string $code): ?int
    {
        if (preg_match('/^N\.O:(\d+)\./', $code, $matches)) {
            $number = (int) $matches[1];

            return $number > 0 ? $number : null;
        }

        return null;
    }

    public function isUsed(int $number): bool
    {
        return isset($this->used[$number]);
    }

    /**
     * Cấp STT cho một dòng sắp insert. Chỉ gọi khi chắc sẽ tạo hồ sơ.
     */
    public function allocate(?int $fileStt): int
    {
        if ($fileStt !== null) {
            $this->used[$fileStt] = true;

            return $fileStt;
        }

        $number = 1;
        while (isset($this->used[$number])) {
            $number++;
        }
        $this->used[$number] = true;

        return $number;
    }

    public function release(int $number): void
    {
        unset($this->used[$number]);
    }
}
