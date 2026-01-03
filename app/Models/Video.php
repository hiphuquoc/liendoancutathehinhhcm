<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    use HasFactory;

    protected $table = 'video_info';
    
    protected $fillable = [
        'title',
        'description',
        'file_cloud',
        'thumbnail',
        'category',
        'ordering',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'status' => 'boolean',
        'ordering' => 'integer',
        'created_by' => 'integer',
        'updated_by' => 'integer',
    ];

    /**
     * Lấy danh sách video với filter và pagination
     */
    public static function getList($params = [])
    {
        $query = self::query();

        // Tìm kiếm theo title
        if (!empty($params['search_name'])) {
            $searchName = $params['search_name'];
            $query->where('title', 'like', '%' . $searchName . '%');
        }

        // Lọc theo category
        if (!empty($params['category'])) {
            $query->where('category', $params['category']);
        }

        // Lọc theo status
        if (isset($params['status'])) {
            $query->where('status', $params['status']);
        }

        // Sắp xếp
        $query->orderBy('ordering', 'ASC')
              ->orderBy('created_at', 'DESC');

        // Pagination
        $paginate = $params['paginate'] ?? 20;
        return $query->paginate($paginate);
    }

    /**
     * Thêm mới video
     */
    public static function insertItem($params)
    {
        $id = 0;
        if (!empty($params)) {
            $model = new self();
            foreach ($params as $key => $value) {
                $model->{$key} = $value;
            }
            $model->save();
            $id = $model->id;
        }
        return $id;
    }

    /**
     * Cập nhật video
     */
    public static function updateItem($id, $params)
    {
        $flag = false;
        if (!empty($id) && !empty($params)) {
            $model = self::find($id);
            if ($model) {
                foreach ($params as $key => $value) {
                    $model->{$key} = $value;
                }
                $flag = $model->save();
            }
        }
        return $flag;
    }

    /**
     * Xóa video
     */
    public static function deleteItem($id)
    {
        $flag = false;
        if (!empty($id)) {
            $model = self::find($id);
            if ($model) {
                $flag = $model->delete();
            }
        }
        return $flag;
    }

    /**
     * Lấy danh sách category duy nhất
     */
    public static function getCategories()
    {
        return self::whereNotNull('category')
                   ->where('category', '!=', '')
                   ->distinct()
                   ->pluck('category')
                   ->toArray();
    }

    /**
     * Lấy video active cho academy (sub-admin)
     */
    public static function getActiveVideos($params = [])
    {
        $query = self::where('status', 1);

        // Tìm kiếm theo title
        if (!empty($params['search'])) {
            $search = $params['search'];
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%');
            });
        }

        // Lọc theo category
        if (!empty($params['category'])) {
            $query->where('category', $params['category']);
        }

        // Sắp xếp
        $query->orderBy('ordering', 'ASC')
              ->orderBy('created_at', 'DESC');

        // Pagination
        $paginate = $params['paginate'] ?? 12;
        return $query->paginate($paginate);
    }

    /**
     * Lấy URL video từ file_cloud
     */
    public function getVideoUrlAttribute()
    {
        if (empty($this->file_cloud)) {
            return null;
        }

        $defaultDomain = config('main_' . env('APP_NAME') . '.google_cloud_storage.default_domain', '');
        return $defaultDomain . $this->file_cloud;
    }

    /**
     * Lấy URL thumbnail
     */
    public function getThumbnailUrlAttribute()
    {
        if (empty($this->thumbnail)) {
            return null;
        }

        // Nếu thumbnail là URL đầy đủ, trả về trực tiếp
        if (filter_var($this->thumbnail, FILTER_VALIDATE_URL)) {
            return $this->thumbnail;
        }

        // Nếu là path trên GCS, dùng Image helper để lấy URL
        return \App\Helpers\Image::getUrlImageCloud($this->thumbnail);
    }
}
