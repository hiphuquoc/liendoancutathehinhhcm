<?php

namespace App\Services;

use App\Models\Athlete;
use App\Models\Referee;
use App\Models\Trainer;
use App\Support\QrCodeDownloadName;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;
use ZipArchive;

/**
 * Tạo ZIP mã QR theo lô rồi tải file trên đĩa.
 * Tránh timeout/memory khi generate hết PNG + nhồi ZIP vào RAM trình duyệt.
 */
class QrCodeZipExportService
{
    public const TYPE_TRAINER = 'trainer';
    public const TYPE_ATHLETE = 'athlete';
    public const TYPE_REFEREE = 'referee';

    private const BATCH_SIZE = 10;
    private const TTL_SECONDS = 7200;
    private const TOKEN_PATTERN = '/^[a-f0-9]{32}$/';

    public function start(int $userId, string $type, array $ids): array
    {
        $this->cleanupExpired();

        $ids = $this->orderedExistingIds($type, $ids);
        if (empty($ids)) {
            throw new \InvalidArgumentException('Vui lòng chọn ít nhất một hồ sơ để tải mã QR.');
        }

        $token = bin2hex(random_bytes(16));
        $zipName = $this->zipDownloadName($type);
        $meta = [
            'user_id' => $userId,
            'type' => $type,
            'ids' => $ids,
            'processed' => 0,
            'total' => count($ids),
            'zip_name' => $zipName,
            'used_filenames' => [],
            'status' => 'processing',
            'error' => null,
            'created_at' => time(),
        ];

        $this->writeMeta($token, $meta);

        return [
            'token' => $token,
            'processed' => 0,
            'total' => $meta['total'],
            'done' => false,
        ];
    }

    public function process(int $userId, string $token): array
    {
        $this->assertToken($token);
        @set_time_limit(90);

        $handle = $this->lockMeta($token);
        try {
            $meta = $this->readMetaFromHandle($handle);
            $this->assertOwner($meta, $userId);

            if ($meta['status'] === 'ready') {
                return $this->progressPayload($token, $meta, true);
            }
            if ($meta['status'] === 'failed') {
                throw new \RuntimeException($meta['error'] ?: 'Không thể tạo file ZIP.');
            }

            $ids = $meta['ids'];
            $processed = (int) $meta['processed'];
            if ($processed >= (int) $meta['total']) {
                $meta['status'] = 'ready';
                $this->writeMetaToHandle($handle, $meta);

                return $this->progressPayload($token, $meta, true);
            }

            $batchIds = array_slice($ids, $processed, self::BATCH_SIZE);
            $zipPath = $this->zipPath($token);
            $zip = new ZipArchive();
            $openFlags = file_exists($zipPath) ? 0 : ZipArchive::CREATE;
            if ($zip->open($zipPath, $openFlags) !== true) {
                throw new \RuntimeException('Không thể tạo file ZIP.');
            }

            $used = is_array($meta['used_filenames'] ?? null) ? $meta['used_filenames'] : [];
            try {
                $models = $this->loadBatch($meta['type'], $batchIds);

                foreach ($batchIds as $id) {
                    $model = $models->get($id);
                    if ($model && $model->seo) {
                        $entry = $this->zipEntry($meta['type'], $model, $used);
                        if ($entry !== null) {
                            $zip->addFromString($entry['filename'], $entry['png']);
                        }
                    }
                    $processed++;
                }
            } finally {
                $zip->close();
            }

            $meta['processed'] = $processed;
            $meta['used_filenames'] = $used;
            $meta['status'] = $processed >= $meta['total'] ? 'ready' : 'processing';
            $this->writeMetaToHandle($handle, $meta);

            return $this->progressPayload($token, $meta, $meta['status'] === 'ready');
        } catch (HttpException $e) {
            throw $e;
        } catch (\InvalidArgumentException $e) {
            throw $e;
        } catch (\Throwable $e) {
            if (isset($meta) && is_array($meta)) {
                $meta['status'] = 'failed';
                $meta['error'] = $e->getMessage();
                if (isset($handle) && is_resource($handle)) {
                    $this->writeMetaToHandle($handle, $meta);
                }
            }
            throw $e;
        } finally {
            if (isset($handle) && is_resource($handle)) {
                flock($handle, LOCK_UN);
                fclose($handle);
            }
        }
    }

    public function download(int $userId, string $token): BinaryFileResponse
    {
        $this->assertToken($token);
        $meta = $this->readMeta($token);
        $this->assertOwner($meta, $userId);

        if (($meta['status'] ?? '') !== 'ready') {
            abort(409, 'File ZIP chưa sẵn sàng.');
        }

        $zipPath = $this->zipPath($token);
        if (!is_file($zipPath)) {
            abort(404, 'Không tìm thấy file ZIP.');
        }

        $jsonPath = $this->metaPath($token);
        register_shutdown_function(static function () use ($jsonPath) {
            if (is_file($jsonPath)) {
                @unlink($jsonPath);
            }
        });

        return response()->download($zipPath, $meta['zip_name'] ?? 'qrcode.zip', [
            'Content-Type' => 'application/zip',
            'Cache-Control' => 'no-store, no-cache, must-revalidate',
            'Pragma' => 'no-cache',
            'X-Accel-Buffering' => 'no',
        ])->deleteFileAfterSend(true);
    }

    private function progressPayload(string $token, array $meta, bool $done): array
    {
        $processed = (int) $meta['processed'];
        $total = (int) $meta['total'];

        return [
            'token' => $token,
            'processed' => $processed,
            'total' => $total,
            'done' => $done,
            'download_url' => $done ? $this->fileUrl($meta['type'], $token) : null,
        ];
    }

    private function fileUrl(string $type, string $token): string
    {
        $names = [
            self::TYPE_TRAINER => 'admin.trainerQrcode.downloadAll.file',
            self::TYPE_ATHLETE => 'admin.athleteQrcode.downloadAll.file',
            self::TYPE_REFEREE => 'admin.refereeQrcode.downloadAll.file',
        ];

        return route($names[$type], ['token' => $token]);
    }

    private function orderedExistingIds(string $type, array $ids): array
    {
        $ids = array_values(array_unique(array_filter(array_map('intval', $ids))));
        if ($ids === []) {
            return [];
        }

        $query = $this->baseQuery($type)->whereIn('id', $ids);

        return $query->pluck('id')->all();
    }

    private function loadBatch(string $type, array $ids)
    {
        if ($ids === []) {
            return collect();
        }

        return $this->baseQuery($type)
            ->whereIn('id', $ids)
            ->get()
            ->keyBy('id');
    }

    private function baseQuery(string $type): Builder
    {
        if ($type === self::TYPE_TRAINER) {
            return Trainer::with('seo')
                ->whereHas('seo', function ($q) {
                    $q->where('type', 'trainer_info')->where('language', 'vi');
                })
                ->orderBy('trainer_code', 'ASC');
        }

        if ($type === self::TYPE_ATHLETE) {
            return Athlete::with('seo')
                ->whereHas('seo', function ($q) {
                    $q->where('type', 'athlete_info')->where('language', 'vi');
                })
                ->orderBy('athlete_code', 'ASC');
        }

        if ($type === self::TYPE_REFEREE) {
            return Referee::with('seo')
                ->whereHas('seo', function ($q) {
                    $q->where('language', 'vi');
                })
                ->orderBy('id', 'DESC');
        }

        throw new \InvalidArgumentException('Loại hồ sơ không hợp lệ.');
    }

    /**
     * @return array{filename: string, png: string}|null
     */
    private function zipEntry(string $type, Model $model, array &$used): ?array
    {
        $url = $this->profileUrl($type, $model);
        if ($url === null) {
            return null;
        }

        $code = $type === self::TYPE_TRAINER
            ? ($model->trainer_code ?? null)
            : ($type === self::TYPE_ATHLETE ? ($model->athlete_code ?? null) : null);

        $filename = $this->uniqueFilename(
            QrCodeDownloadName::png($code, $model->seo->slug ?? null, $model->name ?? null),
            $used
        );

        $png = (string) QrCode::encoding('UTF-8')
            ->format('png')
            ->size(500)
            ->margin(2)
            ->backgroundColor(255, 255, 255)
            ->style('round')
            ->eye('circle')
            ->generate($url);

        return [
            'filename' => $filename,
            'png' => $png,
        ];
    }

    private function profileUrl(string $type, Model $model): ?string
    {
        if (!empty($model->seo->slug_full)) {
            return url('/' . $model->seo->slug_full);
        }
        if (empty($model->seo->slug)) {
            return null;
        }

        if ($type === self::TYPE_TRAINER) {
            $parent = config('main_' . env('APP_NAME') . '.slug_trainer_parent', 'huan-luyen-vien');
            return url('/' . $parent . '/' . $model->seo->slug);
        }
        if ($type === self::TYPE_ATHLETE) {
            $parent = config('main_' . env('APP_NAME') . '.slug_athlete_parent', 'van-dong-vien');
            return url('/' . $parent . '/' . $model->seo->slug);
        }

        return url('/trong-tai/' . $model->seo->slug);
    }

    private function uniqueFilename(string $filename, array &$used): string
    {
        if (!isset($used[$filename])) {
            $used[$filename] = true;
            return $filename;
        }

        $pos = strrpos($filename, '.');
        $base = $pos === false ? $filename : substr($filename, 0, $pos);
        $ext = $pos === false ? '' : substr($filename, $pos);
        $i = 2;
        do {
            $candidate = $base . '-' . $i . $ext;
            $i++;
        } while (isset($used[$candidate]));

        $used[$candidate] = true;

        return $candidate;
    }

    private function zipDownloadName(string $type): string
    {
        $prefix = [
            self::TYPE_TRAINER => 'qrcode_trainers',
            self::TYPE_ATHLETE => 'qrcode_athletes',
            self::TYPE_REFEREE => 'qrcode_referees',
        ][$type] ?? 'qrcode';

        return $prefix . '_' . date('Y-m-d_His') . '.zip';
    }

    private function assertToken(string $token): void
    {
        if (!preg_match(self::TOKEN_PATTERN, $token)) {
            throw new \InvalidArgumentException('Phiên tải ZIP không hợp lệ.');
        }
        if (!is_file($this->metaPath($token))) {
            throw new \InvalidArgumentException('Phiên tải ZIP đã hết hạn. Vui lòng thử lại.');
        }
    }

    private function assertOwner(array $meta, int $userId): void
    {
        if ((int) ($meta['user_id'] ?? 0) !== $userId) {
            abort(403, 'Bạn không có quyền tải file này.');
        }
    }

    private function dir(): string
    {
        $dir = storage_path('app/temp/qr-zip');
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        return $dir;
    }

    private function metaPath(string $token): string
    {
        return $this->dir() . DIRECTORY_SEPARATOR . $token . '.json';
    }

    private function zipPath(string $token): string
    {
        return $this->dir() . DIRECTORY_SEPARATOR . $token . '.zip';
    }

    private function writeMeta(string $token, array $meta): void
    {
        $path = $this->metaPath($token);
        $json = json_encode($meta, JSON_UNESCAPED_UNICODE);
        if (file_put_contents($path, $json, LOCK_EX) === false) {
            throw new \RuntimeException('Không thể khởi tạo phiên tải ZIP.');
        }
    }

    /**
     * @return resource
     */
    private function lockMeta(string $token)
    {
        $handle = fopen($this->metaPath($token), 'c+');
        if ($handle === false) {
            throw new \RuntimeException('Không thể đọc phiên tải ZIP.');
        }
        if (!flock($handle, LOCK_EX)) {
            fclose($handle);
            throw new \RuntimeException('Không thể khóa phiên tải ZIP.');
        }

        return $handle;
    }

    /**
     * @param resource $handle
     */
    private function readMetaFromHandle($handle): array
    {
        rewind($handle);
        $raw = stream_get_contents($handle);
        $meta = json_decode((string) $raw, true);
        if (!is_array($meta)) {
            throw new \RuntimeException('Phiên tải ZIP bị hỏng.');
        }

        return $meta;
    }

    /**
     * @param resource $handle
     */
    private function writeMetaToHandle($handle, array $meta): void
    {
        $json = json_encode($meta, JSON_UNESCAPED_UNICODE);
        rewind($handle);
        ftruncate($handle, 0);
        fwrite($handle, $json);
        fflush($handle);
    }

    private function readMeta(string $token): array
    {
        $raw = file_get_contents($this->metaPath($token));
        $meta = json_decode((string) $raw, true);
        if (!is_array($meta)) {
            throw new \RuntimeException('Phiên tải ZIP bị hỏng.');
        }

        return $meta;
    }

    private function cleanupExpired(): void
    {
        $dir = $this->dir();
        $expiredBefore = time() - self::TTL_SECONDS;
        $paths = array_merge(
            glob($dir . DIRECTORY_SEPARATOR . '*.json') ?: [],
            glob($dir . DIRECTORY_SEPARATOR . '*.zip') ?: []
        );
        foreach ($paths as $path) {
            $mtime = @filemtime($path);
            if ($mtime !== false && $mtime < $expiredBefore) {
                @unlink($path);
            }
        }
    }
}
