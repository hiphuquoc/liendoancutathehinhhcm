<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Models\Trainer;
use App\Helpers\Charactor;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class QrCodeController extends Controller
{
    /**
     * Hiển thị danh sách QR code HLV với bộ lọc
     */
    public function index(Request $request)
    {
        $query = Trainer::with('seo')
            ->whereHas('seo', function ($q) {
                $q->where('type', 'trainer_info')
                  ->where('language', 'vi');
            });

        // Mặc định: không hiển thị kết quả nếu không có filter hoặc search
        $courseFilter = $request->get('course');
        $search = $request->get('search');
        
        // Chỉ query nếu có filter hoặc search
        if (!empty($courseFilter) || !empty($search)) {
            // Bộ lọc theo khóa học (mã tháng năm trong trainer_code)
            if (!empty($courseFilter)) {
                // Format: T12.25 (tháng.năm)
                $query->where('trainer_code', 'like', '%' . $courseFilter . '%');
            }

            // Tìm kiếm theo tên
            if (!empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%')
                      ->orWhere('trainer_code', 'like', '%' . $search . '%')
                      ->orWhereHas('seo', function ($subQ) use ($search) {
                          $subQ->where('title', 'like', '%' . $search . '%');
                      });
                });
            }

            $trainers = $query->orderBy('trainer_code', 'ASC')->get();
        } else {
            // Không có filter, trả về collection rỗng
            $trainers = collect();
        }

        // Lấy danh sách các khóa học (từ trainer_code)
        // Format: N.O:001.T12.25/HLV-HWBF -> Extract "T12.25"
        $courses = Trainer::whereNotNull('trainer_code')
            ->where('trainer_code', '!=', '')
            ->pluck('trainer_code')
            ->map(function ($code) {
                // Extract phần TMM.YY từ trainer_code
                // Format: N.O:001.T12.25/HLV-HWBF
                if (preg_match('/\.(T\d{2}\.\d{2})\//', $code, $matches)) {
                    return $matches[1];
                }
                return null;
            })
            ->filter()
            ->unique()
            ->map(function ($code) {
                // Parse để sắp xếp: T12.25 -> month=12, year=25
                if (preg_match('/T(\d{2})\.(\d{2})/', $code, $matches)) {
                    return [
                        'code' => $code,
                        'month' => (int)$matches[1],
                        'year' => (int)$matches[2],
                        'sort_key' => (int)$matches[2] * 100 + (int)$matches[1] // year*100 + month để sort
                    ];
                }
                return ['code' => $code, 'month' => 0, 'year' => 0, 'sort_key' => 0];
            })
            ->sortByDesc('sort_key') // Sắp xếp gần nhất -> xa nhất
            ->pluck('code')
            ->values();

        // Generate QR code cho mỗi trainer
        foreach ($trainers as $trainer) {
            if (!empty($trainer->seo->slug_full)) {
                $url = url('/' . $trainer->seo->slug_full);
            } elseif (!empty($trainer->seo->slug)) {
                $parentSlug = config('main_' . env('APP_NAME') . '.slug_trainer_parent', 'huan-luyen-vien');
                $url = url('/' . $parentSlug . '/' . $trainer->seo->slug);
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

            $trainer->qr_code_svg = "data:image/svg+xml;base64," . base64_encode($qrCode);
            $trainer->qr_url = $url;
        }

        return view('admin.qrcode.index', compact('trainers', 'courses', 'courseFilter', 'search'));
    }

    /**
     * Tải xuống QR code dạng PNG
     */
    public function download(Request $request)
    {
        $trainerId = $request->get('id');
        $trainer = Trainer::with('seo')->find($trainerId);

        if (empty($trainer) || empty($trainer->seo)) {
            return response()->json([
                'status' => false,
                'message' => 'Không tìm thấy HLV',
            ], 404);
        }

        // Tạo URL
        if (!empty($trainer->seo->slug_full)) {
            $url = url('/' . $trainer->seo->slug_full);
        } elseif (!empty($trainer->seo->slug)) {
            $parentSlug = config('main_' . env('APP_NAME') . '.slug_trainer_parent', 'huan-luyen-vien');
            $url = url('/' . $parentSlug . '/' . $trainer->seo->slug);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Không có URL cho HLV này',
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

        $trainerName = Charactor::convertStrToUrl($trainer->name);
        $filename = "QR_{$trainerName}.png";

        return response($qrCode)
            ->header('Content-Type', 'image/png')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    /**
     * Tải xuống tất cả QR code dạng ZIP
     */
    public function downloadAll(Request $request)
    {
        $query = Trainer::with('seo')
            ->whereHas('seo', function ($q) {
                $q->where('type', 'trainer_info')
                  ->where('language', 'vi');
            });

        // Bộ lọc theo khóa học
        $courseFilter = $request->get('course');
        if (!empty($courseFilter)) {
            $query->where('trainer_code', 'like', '%' . $courseFilter . '%');
        }

        $search = $request->get('search');
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('trainer_code', 'like', '%' . $search . '%')
                  ->orWhereHas('seo', function ($subQ) use ($search) {
                      $subQ->where('title', 'like', '%' . $search . '%');
                  });
            });
        }

        $trainers = $query->orderBy('trainer_code', 'ASC')->get();

        $zip = new \ZipArchive();
        $zipFileName = 'qrcode_trainers_' . date('Y-m-d_His') . '.zip';
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

        $parentSlug = config('main_' . env('APP_NAME') . '.slug_trainer_parent', 'huan-luyen-vien');

        foreach ($trainers as $trainer) {
            if (empty($trainer->seo)) continue;

            // Tạo URL
            if (!empty($trainer->seo->slug_full)) {
                $url = url('/' . $trainer->seo->slug_full);
            } elseif (!empty($trainer->seo->slug)) {
                $url = url('/' . $parentSlug . '/' . $trainer->seo->slug);
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

            $trainerName = Charactor::convertStrToUrl($trainer->name);
            $filename = "QR_{$trainerName}.png";

            $zip->addFromString($filename, $qrCode);
        }

        $zip->close();

        return response()->download($zipPath, $zipFileName)->deleteFileAfterSend(true);
    }

    /**
     * Tải xuống danh sách HLV QR code dạng Excel
     */
    public function downloadExcel(Request $request)
    {
        $query = Trainer::with('seo')
            ->whereHas('seo', function ($q) {
                $q->where('type', 'trainer_info')
                  ->where('language', 'vi');
            });

        $courseFilter = $request->get('course');
        if (!empty($courseFilter)) {
            $query->where('trainer_code', 'like', '%' . $courseFilter . '%');
        }

        $search = $request->get('search');
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('trainer_code', 'like', '%' . $search . '%')
                  ->orWhereHas('seo', function ($subQ) use ($search) {
                      $subQ->where('title', 'like', '%' . $search . '%');
                  });
            });
        }

        $trainers = $query->orderBy('trainer_code', 'ASC')->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Danh sach HLV');

        $sheet->fromArray([
            'STT',
            'Ma so HLV',
            'Ho ten',
            'Email',
            'So dien thoai',
            'Link QR'
        ], null, 'A1');

        $parentSlug = config('main_' . env('APP_NAME') . '.slug_trainer_parent', 'huan-luyen-vien');

        $row = 2;
        $stt = 1;
        foreach ($trainers as $trainer) {
            if (!empty($trainer->seo->slug_full)) {
                $url = url('/' . $trainer->seo->slug_full);
            } elseif (!empty($trainer->seo->slug)) {
                $url = url('/' . $parentSlug . '/' . $trainer->seo->slug);
            } else {
                $url = '';
            }

            $sheet->setCellValue('A' . $row, $stt);
            $sheet->setCellValue('B' . $row, $trainer->trainer_code ?? '');
            $sheet->setCellValue('C' . $row, $trainer->name ?? '');
            $sheet->setCellValue('D' . $row, $trainer->email ?? '');
            $sheet->setCellValue('E' . $row, $trainer->phone ?? '');
            $sheet->setCellValue('F' . $row, $url);

            $row++;
            $stt++;
        }

        foreach (range('A', 'F') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        if (!file_exists(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }

        $fileName = 'danh_sach_hlv_qrcode_' . date('Y-m-d_His') . '.xlsx';
        $filePath = storage_path('app/temp/' . $fileName);
        $writer = new Xlsx($spreadsheet);
        $writer->save($filePath);

        return response()->download($filePath, $fileName)->deleteFileAfterSend(true);
    }
}

