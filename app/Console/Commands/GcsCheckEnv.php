<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;

/**
 * Chẩn đoán môi trường GCS trên hosting.
 * Chạy trên server thật: php artisan gcs:check-env
 * Dùng khi local upload OK nhưng hosting (tên miền thật) lỗi.
 */
class GcsCheckEnv extends Command
{
    protected $signature = 'gcs:check-env';

    protected $description = 'Kiểm tra môi trường upload Google Cloud Storage (file key, config, PHP, kết nối HTTPS tới Google)';

    public function handle(): int
    {
        $this->info('=== Chẩn đoán môi trường GCS ===');
        $this->newLine();

        $allOk = true;

        // 1. Config
        $keyPath = config('filesystems.disks.gcs.key_file_path');
        $bucket = config('filesystems.disks.gcs.bucket');

        $this->line('1. Config Laravel (disk gcs):');
        if ($keyPath) {
            $this->line('   key_file_path: ' . $keyPath);
        } else {
            $this->error('   key_file_path: RỖNG hoặc null → set GOOGLE_CLOUD_KEY_FILE trong .env');
            $allOk = false;
        }
        if ($bucket) {
            $this->line('   bucket: ' . $bucket);
        } else {
            $this->error('   bucket: RỖNG hoặc null → set GOOGLE_CLOUD_STORAGE_BUCKET trong .env');
            $allOk = false;
        }
        $this->newLine();

        // 2. File key
        $this->line('2. File key JSON:');
        if (empty($keyPath)) {
            $this->warn('   Bỏ qua (chưa có key_file_path).');
        } else {
            $exists = is_file($keyPath);
            $readable = $exists && is_readable($keyPath);
            if (!$exists) {
                $this->error('   FAIL: File không tồn tại: ' . $keyPath);
                $allOk = false;
            } elseif (!$readable) {
                $this->error('   FAIL: File tồn tại nhưng không đọc được (quyền / open_basedir?).');
                $allOk = false;
            } else {
                $json = @file_get_contents($keyPath);
                $parsed = $json !== false ? json_decode($json, true) : null;
                $hasEmail = $parsed && isset($parsed['client_email']);
                $hasKey = $parsed && isset($parsed['private_key']);
                if (!$hasEmail || !$hasKey) {
                    $this->error('   FAIL: File không phải JSON key hợp lệ (thiếu client_email hoặc private_key).');
                    $allOk = false;
                } else {
                    $this->info('   OK: File tồn tại, đọc được, có client_email và private_key.');
                    $this->line('   client_email: ' . $parsed['client_email']);
                }
            }
        }
        $this->newLine();

        // 3. PHP extensions
        $this->line('3. PHP extensions:');
        $exts = ['openssl', 'curl', 'json'];
        foreach ($exts as $ext) {
            if (extension_loaded($ext)) {
                $this->info('   ' . $ext . ': OK');
            } else {
                $this->error('   ' . $ext . ': FAIL (chưa bật)');
                $allOk = false;
            }
        }
        $this->newLine();

        // 4. open_basedir / disable_functions
        $this->line('4. PHP restrictions:');
        $basedir = ini_get('open_basedir');
        $disabled = ini_get('disable_functions');
        if ($basedir) {
            $this->warn('   open_basedir: ' . $basedir);
            if ($keyPath && $basedir) {
                $inBase = false;
                foreach (array_filter(explode(PATH_SEPARATOR, $basedir)) as $base) {
                    if (strpos($keyPath, rtrim($base, DIRECTORY_SEPARATOR)) === 0) {
                        $inBase = true;
                        break;
                    }
                }
                if (!$inBase) {
                    $this->error('   FAIL: Đường dẫn file key không nằm trong open_basedir.');
                    $allOk = false;
                }
            }
        } else {
            $this->info('   open_basedir: (trống)');
        }
        if ($disabled) {
            $list = array_map('trim', explode(',', $disabled));
            $need = ['curl_exec', 'file_get_contents'];
            $blocked = array_filter($need, fn($f) => in_array($f, $list));
            if (!empty($blocked)) {
                $this->error('   disable_functions chặn: ' . implode(', ', $blocked));
                $allOk = false;
            } else {
                $this->info('   disable_functions: không chặn curl_exec / file_get_contents');
            }
        } else {
            $this->info('   disable_functions: (trống)');
        }
        $this->newLine();

        // 5. Kết nối HTTPS tới Google
        $this->line('5. Kết nối HTTPS tới Google:');
        $testUrl = 'https://www.googleapis.com/oauth2/v1/certs';
        $ctx = stream_context_create([
            'http' => ['timeout' => 10],
            'ssl' => ['verify_peer' => true],
        ]);
        $body = @file_get_contents($testUrl, false, $ctx);
        if ($body === false || $body === '') {
            $this->error('   FAIL: Không kết nối được tới ' . $testUrl);
            $this->warn('   → Thường do: thiếu CA certificate (curl.cainfo / openssl.cafile) hoặc firewall chặn outbound.');
            $allOk = false;
        } else {
            $this->info('   OK: Kết nối được tới Google (oauth2).');
        }

        // Thử storage.googleapis.com (optional)
        $ch = curl_init('https://storage.googleapis.com');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_NOBODY => true,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_SSL_VERIFYPEER => true,
        ]);
        curl_exec($ch);
        $err = curl_error($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ($err) {
            $this->error('   FAIL: storage.googleapis.com - ' . $err);
            $allOk = false;
        } else {
            $this->info('   OK: Có thể gọi storage.googleapis.com (HTTP ' . $code . ').');
        }
        $this->newLine();

        if ($allOk) {
            $this->info('=== Kết quả: Tất cả kiểm tra đều OK. Nếu vẫn lỗi upload, xem log Laravel / lỗi chi tiết từ GCS. ===');
        } else {
            $this->warn('=== Kết quả: Có mục FAIL. Sửa theo Phần 3 trong docs/GOOGLE_CLOUD_STORAGE_CHECKLIST.md (SSL/CA, firewall, open_basedir, disable_functions). ===');
        }

        return $allOk ? Command::SUCCESS : Command::FAILURE;
    }
}
