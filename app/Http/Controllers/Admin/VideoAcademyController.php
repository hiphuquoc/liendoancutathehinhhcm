<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Video;

class VideoAcademyController extends Controller
{
    /**
     * Trang Academy - xem video (sub-admin và admin)
     */
    public function index(Request $request)
    {
        // Chỉ sub-admin và admin mới được xem
        $user = Auth::user();
        if (!$user->hasRole('sub-admin') && !$user->hasRole('admin')) {
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
        // Chỉ sub-admin và admin mới được xem
        $user = Auth::user();
        if (!$user->hasRole('sub-admin') && !$user->hasRole('admin')) {
            abort(403, 'Bạn không có quyền truy cập trang này.');
        }

        $video = Video::where('id', $id)
                     ->where('status', 1)
                     ->first();

        if (empty($video)) {
            abort(404, 'Video không tồn tại hoặc đã bị vô hiệu hóa.');
        }

        // Lấy các video liên quan (cùng category)
        $relatedVideos = Video::where('status', 1)
                             ->where('id', '!=', $id)
                             ->when($video->category, function($query) use ($video) {
                                 return $query->where('category', $video->category);
                             })
                             ->orderBy('ordering', 'ASC')
                             ->orderBy('created_at', 'DESC')
                             ->limit(6)
                             ->get();

        return view('admin.videoAcademy.show', compact('video', 'relatedVideos'));
    }
}

