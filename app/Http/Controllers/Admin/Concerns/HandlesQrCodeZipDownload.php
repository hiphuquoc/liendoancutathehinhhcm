<?php

namespace App\Http\Controllers\Admin\Concerns;

use App\Services\QrCodeZipExportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;

trait HandlesQrCodeZipDownload
{
    abstract protected function qrZipType(): string;

    public function downloadAll(Request $request): JsonResponse
    {
        try {
            $payload = app(QrCodeZipExportService::class)->start(
                (int) auth()->id(),
                $this->qrZipType(),
                $this->selectedIds($request)
            );

            return response()->json(['status' => true] + $payload);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 422);
        } catch (\Throwable $e) {
            Log::error('QR ZIP start failed', ['error' => $e->getMessage()]);

            return response()->json([
                'status' => false,
                'message' => 'Không thể khởi tạo file ZIP.',
            ], 500);
        }
    }

    public function processZip(Request $request): JsonResponse
    {
        try {
            $payload = app(QrCodeZipExportService::class)->process(
                (int) auth()->id(),
                (string) $request->input('token', '')
            );

            return response()->json(['status' => true] + $payload);
        } catch (HttpException $e) {
            throw $e;
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 422);
        } catch (\Throwable $e) {
            Log::error('QR ZIP process failed', ['error' => $e->getMessage()]);

            return response()->json([
                'status' => false,
                'message' => 'Không thể tạo file ZIP. Vui lòng thử lại.',
            ], 500);
        }
    }

    public function downloadZip(string $token): BinaryFileResponse
    {
        try {
            return app(QrCodeZipExportService::class)->download((int) auth()->id(), $token);
        } catch (HttpException $e) {
            throw $e;
        } catch (\InvalidArgumentException $e) {
            abort(404, $e->getMessage());
        }
    }
}
