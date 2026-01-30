<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Video;

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
}

