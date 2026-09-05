<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Models\Referee;
use App\Helpers\Charactor;
use App\Services\ProfileDeletionService;

class RefereeQrCodeController extends Controller
{
    /**
     * Hiển thị danh sách QR code Trọng tài với bộ lọc
     */
    public function index(Request $request)
    {
        $query = Referee::with('seo')
            ->whereHas('seo', function ($q) {
                $q->where('language', 'vi');
            });

        // Tìm kiếm theo tên (nếu có)
        $search = $request->get('search');
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhereHas('seo', function ($subQ) use ($search) {
                      $subQ->where('title', 'like', '%' . $search . '%');
                  });
            });
        }

        // Mặc định hiển thị tất cả trọng tài
        $referees = $query->orderBy('id', 'DESC')->get();

        // Generate QR code cho mỗi referee
        foreach ($referees as $referee) {
            if (!empty($referee->seo->slug_full)) {
                $url = url('/' . $referee->seo->slug_full);
            } elseif (!empty($referee->seo->slug)) {
                $url = url('/trong-tai/' . $referee->seo->slug);
            } else {
                $url = url('/');
            }

            // Generate QR code SVG
            $qrCode = QrCode::encoding('UTF-8')
                ->format('svg')
                ->size(300)
                ->margin(1)
                ->backgroundColor(255, 255, 255)
                ->style('round')
                ->eye('circle')
                ->generate($url);

            $referee->qr_code_svg = "data:image/svg+xml;base64," . base64_encode($qrCode);
            $referee->qr_url = $url;
        }

        return view('admin.refereeQrcode.index', compact('referees', 'search'));
    }

    /**
     * Tải xuống QR code dạng PNG
     */
    public function download(Request $request)
    {
        $id = $request->get('id');
        $referee = Referee::with('seo')->find($id);

        if (empty($referee) || empty($referee->seo)) {
            return response()->json([
                'status' => false,
                'message' => 'Không tìm thấy Trọng tài',
            ], 404);
        }

        // Tạo URL
        if (!empty($referee->seo->slug_full)) {
            $url = url('/' . $referee->seo->slug_full);
        } elseif (!empty($referee->seo->slug)) {
            $url = url('/trong-tai/' . $referee->seo->slug);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Không có URL cho Trọng tài này',
            ], 404);
        }

        // Generate QR code PNG
        $qrCode = QrCode::encoding('UTF-8')
            ->format('png')
            ->size(500)
            ->margin(2)
            ->backgroundColor(255, 255, 255)
            ->style('round')
            ->eye('circle')
            ->generate($url);

        $refereeName = Charactor::convertStrToUrl($referee->name);
        $filename = "QR_{$refereeName}.png";

        return response($qrCode)
            ->header('Content-Type', 'image/png')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    /**
     * Tải xuống tất cả QR code dạng ZIP
     */
    public function downloadAll(Request $request)
    {
        $query = Referee::with('seo')
            ->whereHas('seo', function ($q) {
                $q->where('language', 'vi');
            });

        $search = $request->get('search');
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhereHas('seo', function ($subQ) use ($search) {
                      $subQ->where('title', 'like', '%' . $search . '%');
                  });
            });
        }

        $ids = $this->selectedIds($request);
        if ($request->isMethod('post')) {
            if (empty($ids)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Vui lòng chọn ít nhất một hồ sơ để tải mã QR.',
                ], 422);
            }
            $query->whereIn('id', $ids);
        } elseif (!empty($ids)) {
            $query->whereIn('id', $ids);
        }

        $referees = $query->get();

        if ($referees->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'Không có hồ sơ nào để tải mã QR.',
            ], 422);
        }

        $zip = new \ZipArchive();
        $zipFileName = 'qrcode_referees_' . date('Y-m-d_His') . '.zip';
        $zipPath = storage_path('app/temp/' . $zipFileName);

        // Tạo thư mục temp nếu chưa có
        if (!file_exists(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }

        if ($zip->open($zipPath, \ZipArchive::CREATE) !== TRUE) {
            return response()->json([
                'status' => false,
                'message' => 'Không thể tạo file ZIP',
            ], 500);
        }

        foreach ($referees as $referee) {
            if (empty($referee->seo)) continue;

            // Tạo URL
            if (!empty($referee->seo->slug_full)) {
                $url = url('/' . $referee->seo->slug_full);
            } elseif (!empty($referee->seo->slug)) {
                $url = url('/trong-tai/' . $referee->seo->slug);
            } else {
                continue;
            }

            // Generate QR code PNG
            $qrCode = QrCode::encoding('UTF-8')
                ->format('png')
                ->size(500)
                ->margin(2)
                ->backgroundColor(255, 255, 255)
                ->style('round')
                ->eye('circle')
                ->generate($url);

            $refereeName = Charactor::convertStrToUrl($referee->name);
            $filename = "QR_{$refereeName}.png";

            $zip->addFromString($filename, $qrCode);
        }

        $zip->close();

        return response()->download($zipPath, $zipFileName)->deleteFileAfterSend(true);
    }

    public function deleteSelected(Request $request)
    {
        if (!auth()->user() || !auth()->user()->hasRole('admin')) {
            abort(403, 'Bạn không có quyền xóa hồ sơ trọng tài.');
        }

        $ids = $this->selectedIds($request);
        if (empty($ids)) {
            return response()->json([
                'status' => false,
                'message' => 'Vui lòng chọn ít nhất một hồ sơ để xóa.',
            ], 422);
        }

        $result = app(ProfileDeletionService::class)->deleteMany(ProfileDeletionService::TYPE_REFEREE, $ids);
        $deletedCount = count($result['deleted']);
        $failedCount = count($result['failed']);

        return response()->json([
            'status' => $deletedCount > 0,
            'deleted_ids' => $result['deleted'],
            'failed' => $result['failed'],
            'message' => $failedCount === 0
                ? "Đã xóa {$deletedCount} hồ sơ trọng tài."
                : "Đã xóa {$deletedCount} hồ sơ, {$failedCount} hồ sơ không xóa được.",
        ], $deletedCount > 0 ? 200 : 422);
    }

    private function selectedIds(Request $request): array
    {
        $ids = $request->input('ids', []);
        if (!is_array($ids)) {
            $ids = array_filter(explode(',', (string) $ids));
        }

        return array_values(array_unique(array_filter(array_map('intval', $ids))));
    }
}

