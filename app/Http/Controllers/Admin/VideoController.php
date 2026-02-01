<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Auth;
use App\Models\Video;
use App\Helpers\Upload;

class VideoController extends Controller
{
    /**
     * Danh sách video (admin only)
     */
    public function list(Request $request)
    {
        // Chỉ admin mới được xem
        if (!Auth::user()->hasRole('admin')) {
            abort(403, 'Bạn không có quyền truy cập trang này.');
        }

        $params = [];
        
        // Tìm kiếm theo tên
        if (!empty($request->get('search_name'))) {
            $params['search_name'] = $request->get('search_name');
        }

        // Lọc theo category
        if (!empty($request->get('category'))) {
            $params['category'] = $request->get('category');
        }

        // Lọc theo status
        if ($request->has('status')) {
            $params['status'] = $request->get('status');
        }

        // Pagination
        $viewPerPage = Cookie::get('viewVideoInfo') ?? 20;
        $params['paginate'] = $viewPerPage;
        
        $list = Video::getList($params);
        $categories = Video::getCategories();

        return view('admin.video.list', compact('list', 'params', 'viewPerPage', 'categories'));
    }

    /**
     * Hiển thị form create/edit
     */
    public function view(Request $request)
    {
        // Chỉ admin mới được xem
        if (!Auth::user()->hasRole('admin')) {
            abort(403, 'Bạn không có quyền truy cập trang này.');
        }

        $id = $request->get('id') ?? 0;
        $type = $request->get('type') ?? 'create';
        $message = $request->get('message') ?? null;

        $item = null;
        if (!empty($id) && $type === 'edit') {
            $item = Video::find($id);
            if (empty($item)) {
                return redirect()->route('admin.video.list')
                    ->with('message', [
                        'type' => 'danger',
                        'message' => 'Không tìm thấy video.'
                    ]);
            }
        }

        $categories = Video::getCategories();

        return view('admin.video.view', compact('item', 'type', 'message', 'categories'));
    }

    /**
     * Create/Update video
     */
    public function createAndUpdate(Request $request)
    {
        // Chỉ admin mới được thực hiện
        if (!Auth::user()->hasRole('admin')) {
            abort(403, 'Bạn không có quyền thực hiện thao tác này.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'video_file' => 'required_without:video_info_id|file|mimes:mp4,webm,mov,avi,mkv,flv|max:104857600', // Max 100GB (value in KB: 100*1024*1024)
            'thumbnail' => 'nullable|string|max:1000', // Allow both URL and GCS path
            'thumbnail_file' => 'nullable|image|mimes:jpeg,jpg,png,gif|max:5120', // Max 5MB
            'category' => 'nullable|string|max:100',
            'ordering' => 'nullable|integer|min:0',
            'status' => 'nullable|boolean',
        ], [
            'title.required' => 'Vui lòng nhập tiêu đề video.',
            'title.max' => 'Tiêu đề không được vượt quá 255 ký tự.',
            'video_file.required_without' => 'Vui lòng chọn file video.',
            'video_file.file' => 'File video không hợp lệ.',
            'video_file.mimes' => 'File video phải có định dạng: mp4, webm, mov, avi, mkv, flv.',
            'video_file.max' => 'File video không được vượt quá 100GB.',
            'thumbnail.max' => 'URL thumbnail không được vượt quá 1000 ký tự.',
            'thumbnail_file.image' => 'File thumbnail phải là ảnh.',
            'thumbnail_file.mimes' => 'File thumbnail phải có định dạng: jpeg, jpg, png, gif.',
            'thumbnail_file.max' => 'File thumbnail không được vượt quá 5MB.',
            'category.max' => 'Danh mục không được vượt quá 100 ký tự.',
            'ordering.integer' => 'Thứ tự sắp xếp phải là số nguyên.',
            'ordering.min' => 'Thứ tự sắp xếp phải lớn hơn hoặc bằng 0.',
        ]);

        try {
            DB::beginTransaction();

            $id = $request->get('video_info_id') ?? 0;
            $type = $request->get('type') ?? 'create';

            // Upload video file nếu có
            $videoFileCloud = null;
            if ($request->hasFile('video_file')) {
                $name = !empty($request->get('title')) ? \Illuminate\Support\Str::slug($request->get('title')) : time();
                $fileName = $name . '-' . time() . '.' . $request->file('video_file')->getClientOriginalExtension();
                $folderUpload = config('main_' . env('APP_NAME') . '.google_cloud_storage.videos');
                $videoFileCloud = Upload::uploadVideo($request->file('video_file'), $fileName, $folderUpload);
            }

            // Upload thumbnail nếu có file
            $thumbnailUrl = $request->get('thumbnail');
            if ($request->hasFile('thumbnail_file')) {
                $name = !empty($request->get('title')) ? \Illuminate\Support\Str::slug($request->get('title')) : time();
                $fileName = $name . '-thumb.' . config('image.extension');
                $folderUpload = config('main_' . env('APP_NAME') . '.google_cloud_storage.wallpapers');
                $thumbnailPath = Upload::uploadWallpaper($request->file('thumbnail_file'), $fileName, $folderUpload);
                if (!empty($thumbnailPath)) {
                    $thumbnailUrl = $thumbnailPath;
                }
            }

            // Handle status - checkbox sends '1' when checked, hidden input sends '0' when unchecked
            $status = 0;
            if ($request->has('status')) {
                $statusValue = $request->get('status');
                // Checkbox sends '1' when checked, or we get 'on' from old checkbox behavior
                $status = ($statusValue == '1' || $statusValue == 'on') ? 1 : 0;
            } else {
                // If no status sent, check old value for edit mode
                if (!empty($id) && $type === 'edit') {
                    $existingVideo = Video::find($id);
                    if ($existingVideo) {
                        $status = $existingVideo->status ? 1 : 0;
                    }
                }
            }

            $data = [
                'title' => $request->get('title'),
                'description' => $request->get('description'),
                'thumbnail' => $thumbnailUrl,
                'category' => $request->get('category'),
                'ordering' => $request->get('ordering') ? (int)$request->get('ordering') : 0,
                'status' => $status,
            ];

            // Chỉ cập nhật file_cloud nếu có file mới
            if (!empty($videoFileCloud)) {
                $data['file_cloud'] = $videoFileCloud;
            }

            $userId = Auth::id();

            if (!empty($id) && $type === 'edit') {
                // Update - chỉ update file_cloud nếu có file mới
                if (empty($videoFileCloud)) {
                    // Giữ nguyên file_cloud cũ, không cập nhật
                    unset($data['file_cloud']);
                }
                $data['updated_by'] = $userId;
                Video::updateItem($id, $data);
                $message = [
                    'type' => 'success',
                    'message' => '<strong>Thành công!</strong> Đã cập nhật video.'
                ];
            } else {
                // Create - bắt buộc phải có file video
                if (empty($videoFileCloud)) {
                    throw new \Exception('Vui lòng chọn file video để upload.');
                }
                $data['created_by'] = $userId;
                $data['updated_by'] = $userId;
                Video::insertItem($data);
                $message = [
                    'type' => 'success',
                    'message' => '<strong>Thành công!</strong> Đã thêm video mới.'
                ];
            }

            DB::commit();

            return redirect()->route('admin.video.list')->with('message', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            $message = [
                'type' => 'danger',
                'message' => '<strong>Lỗi!</strong> ' . $e->getMessage()
            ];
            return redirect()->back()->withInput()->with('message', $message);
        }
    }

    /**
     * Xóa video
     */
    public function delete(Request $request)
    {
        // Chỉ admin mới được xóa
        if (!Auth::user()->hasRole('admin')) {
            abort(403, 'Bạn không có quyền thực hiện thao tác này.');
        }

        $id = $request->get('id');
        
        if (empty($id)) {
            return redirect()->route('admin.video.list')
                ->with('message', [
                    'type' => 'danger',
                    'message' => 'Không tìm thấy video để xóa.'
                ]);
        }

        try {
            // Lấy thông tin video trước khi xóa để xóa file trên GCS
            $video = Video::find($id);
            
            if ($video && !empty($video->file_cloud)) {
                // Xóa file video trên Google Cloud Storage
                Upload::deleteVideo($video->file_cloud);
            }

            $result = Video::deleteItem($id);
            
            if ($result) {
                $message = [
                    'type' => 'success',
                    'message' => '<strong>Thành công!</strong> Đã xóa video.'
                ];
            } else {
                $message = [
                    'type' => 'danger',
                    'message' => '<strong>Lỗi!</strong> Không thể xóa video.'
                ];
            }

            return redirect()->route('admin.video.list')->with('message', $message);

        } catch (\Exception $e) {
            return redirect()->route('admin.video.list')
                ->with('message', [
                    'type' => 'danger',
                    'message' => '<strong>Lỗi!</strong> ' . $e->getMessage()
                ]);
        }
    }

    /**
     * Xem video (admin có thể xem cả video inactive)
     */
    public function watch(Request $request)
    {
        // Chỉ admin mới được xem
        if (!Auth::user()->hasRole('admin')) {
            abort(403, 'Bạn không có quyền truy cập trang này.');
        }

        $id = $request->get('id');
        
        if (empty($id)) {
            return redirect()->route('admin.video.list')
                ->with('message', [
                    'type' => 'danger',
                    'message' => 'Không tìm thấy video.'
                ]);
        }

        $video = Video::find($id);

        if (empty($video)) {
            return redirect()->route('admin.video.list')
                ->with('message', [
                    'type' => 'danger',
                    'message' => 'Video không tồn tại.'
                ]);
        }

        // Lấy các video liên quan (cùng category, bất kể status)
        $relatedVideos = Video::where('id', '!=', $id)
                             ->when($video->category, function($query) use ($video) {
                                 return $query->where('category', $video->category);
                             })
                             ->orderBy('ordering', 'ASC')
                             ->orderBy('created_at', 'DESC')
                             ->limit(6)
                             ->get();

        return view('admin.video.watch', compact('video', 'relatedVideos'));
    }
}

