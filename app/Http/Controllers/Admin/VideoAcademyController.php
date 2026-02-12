<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cookie;
use App\Models\Video;
use App\Helpers\Upload;
use App\Helpers\Image;

class VideoAcademyController extends Controller
{
    /**
     * Trang Academy - xem video (admin, sub-admin, trainer, referee)
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $canAccess = $user->hasRole('admin') || $user->hasRole('sub-admin')
            || $user->hasRole('trainer') || $user->hasRole('referee');
        if (!$canAccess) {
            abort(403, 'Bạn không có quyền truy cập trang này.');
        }

        $params = [];

        // Tìm kiếm
        if (!empty($request->get('search'))) {
            $params['search'] = $request->get('search');
        }

        // Lọc theo category
        if (!empty($request->get('category'))) {
            $params['category'] = $request->get('category');
        }

        // Pagination
        $params['paginate'] = 12; // Grid 3x4 hoặc responsive

        $videos = Video::getActiveVideos($params);
        $categories = Video::getCategories();

        return view('admin.videoAcademy.index', compact('videos', 'categories', 'params'));
    }

    /**
     * Xem chi tiết video
     */
    public function show($id)
    {
        $user = Auth::user();
        $canAccess = $user->hasRole('admin') || $user->hasRole('sub-admin')
            || $user->hasRole('trainer') || $user->hasRole('referee');
        if (!$canAccess) {
            abort(403, 'Bạn không có quyền truy cập trang này.');
        }

        $video = Video::where('id', $id)
                     ->where('status', 1)
                     ->first();

        if (empty($video)) {
            abort(404, 'Video không tồn tại hoặc đã bị vô hiệu hóa.');
        }

        // Lấy các video liên quan (cùng category, tối đa 12)
        $relatedVideos = Video::where('status', 1)
                             ->where('id', '!=', $id)
                             ->when($video->category, function($query) use ($video) {
                                 return $query->where('category', $video->category);
                             })
                             ->orderBy('ordering', 'ASC')
                             ->orderBy('created_at', 'DESC')
                             ->limit(12)
                             ->get();

        // Video trước / tiếp theo (cùng danh mục, theo thứ tự ordering)
        $orderedIds = Video::where('status', 1)
            ->when($video->category, function($q) use ($video) {
                return $q->where('category', $video->category);
            })
            ->orderBy('ordering', 'ASC')
            ->orderBy('created_at', 'DESC')
            ->orderBy('id', 'ASC')
            ->pluck('id')
            ->values()
            ->all();

        $currentPosition = array_search((int) $id, $orderedIds);
        $prevVideo = null;
        $nextVideo = null;
        if ($currentPosition !== false) {
            if ($currentPosition > 0) {
                $prevVideo = Video::where('id', $orderedIds[$currentPosition - 1])->where('status', 1)->first();
            }
            if ($currentPosition < count($orderedIds) - 1) {
                $nextVideo = Video::where('id', $orderedIds[$currentPosition + 1])->where('status', 1)->first();
            }
        }

        return view('admin.videoAcademy.show', compact('video', 'relatedVideos', 'prevVideo', 'nextVideo'));
    }

    /**
     * Danh sách video quản lý (chỉ admin)
     */
    public function list(Request $request)
    {
        $user = Auth::user();
        if (!$user->hasRole('admin')) {
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
        $viewPerPage = Cookie::get('viewVideoAcademy') ?? 20;
        $params['paginate'] = $viewPerPage;
        
        $list = Video::getList($params);
        $categories = Video::getCategories();
        
        return view('admin.videoAcademy.list', compact('list', 'params', 'viewPerPage', 'categories'));
    }

    /**
     * Form create/edit video (chỉ admin)
     */
    public function view(Request $request)
    {
        $user = Auth::user();
        if (!$user->hasRole('admin')) {
            abort(403, 'Bạn không có quyền truy cập trang này.');
        }

        $message = $request->get('message') ?? null;
        $id = $request->get('id') ?? 0;
        $type = $request->get('type') ?? 'create';
        
        $item = null;
        if ($id > 0 && $type !== 'create') {
            $item = Video::find($id);
            if (empty($item)) {
                return redirect()->route('admin.videoAcademy.list')->with('message', [
                    'type' => 'danger',
                    'message' => 'Video không tồn tại.'
                ]);
            }
            if ($type === 'copy') {
                $type = 'create';
                $item->id = 0;
            }
        }
        
        $categories = Video::getCategories();
        
        return view('admin.videoAcademy.form', compact('item', 'type', 'message', 'categories'));
    }

    /**
     * Create/Update video (chỉ admin)
     */
    public function createAndUpdate(Request $request)
    {
        $user = Auth::user();
        if (!$user->hasRole('admin')) {
            abort(403, 'Bạn không có quyền thực hiện thao tác này.');
        }

        try {
            DB::beginTransaction();
            
            $id = $request->get('id') ?? 0;
            $type = $request->get('type') ?? 'create';
            
            $data = [
                'title' => $request->get('title'),
                'description' => $request->get('description'),
                'category' => $request->get('category'),
                'ordering' => (int)($request->get('ordering') ?? 0),
                'status' => $request->get('status') === 'on' ? 1 : 0,
            ];
            
            // Upload video file
            if ($request->hasFile('file_cloud')) {
                $fileName = time() . '_' . $request->file('file_cloud')->getClientOriginalName();
                $folderUpload = config('main_' . env('APP_NAME') . '.google_cloud_storage.videos', 'videos');
                $dataPath = Upload::uploadWallpaper($request->file('file_cloud'), $fileName, $folderUpload);
                if (!empty($dataPath)) {
                    $data['file_cloud'] = $dataPath;
                }
            } elseif ($type === 'edit' && !empty($request->get('file_cloud_url'))) {
                // Giữ nguyên file_cloud nếu không upload mới
                $existingVideo = Video::find($id);
                if ($existingVideo && !empty($existingVideo->file_cloud)) {
                    $data['file_cloud'] = $existingVideo->file_cloud;
                }
            }
            
            // Upload thumbnail
            if ($request->hasFile('thumbnail')) {
                $fileName = time() . '_thumb_' . $request->file('thumbnail')->getClientOriginalName();
                $folderUpload = config('main_' . env('APP_NAME') . '.google_cloud_storage.wallpapers', 'wallpapers');
                $dataPath = Upload::uploadWallpaper($request->file('thumbnail'), $fileName, $folderUpload);
                if (!empty($dataPath)) {
                    $data['thumbnail'] = $dataPath;
                }
            } elseif ($type === 'edit' && !empty($request->get('thumbnail_url'))) {
                // Giữ nguyên thumbnail nếu không upload mới
                $existingVideo = Video::find($id);
                if ($existingVideo && !empty($existingVideo->thumbnail)) {
                    $data['thumbnail'] = $existingVideo->thumbnail;
                }
            } elseif (!empty($request->get('thumbnail_url'))) {
                // Nếu là URL trực tiếp
                $data['thumbnail'] = $request->get('thumbnail_url');
            }
            
            // Set created_by / updated_by
            if ($type === 'create') {
                $data['created_by'] = $user->id;
            }
            $data['updated_by'] = $user->id;
            
            if ($type === 'create' || empty($id)) {
                $id = Video::insertItem($data);
                $message = [
                    'type' => 'success',
                    'message' => '<strong>Thành công!</strong> Đã tạo video mới!'
                ];
            } else {
                Video::updateItem($id, $data);
                $message = [
                    'type' => 'success',
                    'message' => '<strong>Thành công!</strong> Đã cập nhật video!'
                ];
            }
            
            DB::commit();
            
        } catch (\Exception $exception) {
            DB::rollBack();
            $message = [
                'type' => 'danger',
                'message' => '<strong>Thất bại!</strong> Có lỗi xảy ra: ' . $exception->getMessage()
            ];
        }
        
        $request->session()->put('message', $message);
        return redirect()->route('admin.videoAcademy.view', ['id' => $id, 'type' => 'edit']);
    }

    /**
     * Delete video (chỉ admin)
     */
    public function delete(Request $request)
    {
        $user = Auth::user();
        if (!$user->hasRole('admin')) {
            abort(403, 'Bạn không có quyền thực hiện thao tác này.');
        }

        $id = $request->get('id');
        if (empty($id)) {
            return false;
        }
        
        try {
            DB::beginTransaction();
            
            $video = Video::find($id);
            if (empty($video)) {
                return false;
            }
            
            // Xóa file video trên cloud storage nếu có
            if (!empty($video->file_cloud)) {
                try {
                    Upload::deleteWallpaper($video->file_cloud);
                } catch (\Exception $e) {
                    // Log error nhưng không dừng quá trình xóa
                }
            }
            
            // Xóa thumbnail trên cloud storage nếu có
            if (!empty($video->thumbnail) && !filter_var($video->thumbnail, FILTER_VALIDATE_URL)) {
                try {
                    Upload::deleteWallpaper($video->thumbnail);
                } catch (\Exception $e) {
                    // Log error nhưng không dừng quá trình xóa
                }
            }
            
            Video::deleteItem($id);
            
            DB::commit();
            return true;
            
        } catch (\Exception $exception) {
            DB::rollBack();
            return false;
        }
    }
}

