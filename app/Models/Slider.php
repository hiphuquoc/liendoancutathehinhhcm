<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Slider extends Model
{
    use HasFactory;
    
    protected $table = 'slider_info';
    
    protected $fillable = [
        'seo_id',
        'image',
        'image_mobile',
        'title',
        'description',
        'position',
        'button_text',
        'button_icon',
        'button_link',
        'ordering',
        'flag_show',
        'notes',
    ];
    
    public $timestamps = true;
    
    /**
     * Lấy danh sách slider hiển thị
     */
    public static function getActiveSliders($language = 'vi')
    {
        return self::select('*')
            ->where('flag_show', 1)
            ->orderBy('ordering', 'ASC')
            ->orderBy('id', 'ASC')
            ->get();
    }
    
    /**
     * Lấy slider theo ID
     */
    public static function getById($id)
    {
        return self::where('id', $id)->first();
    }
    
    /**
     * Thêm mới slider
     */
    public static function insertItem($params)
    {
        $id = 0;
        if (!empty($params)) {
            $model = new Slider();
            foreach ($params as $key => $value) {
                $model->{$key} = $value;
            }
            $model->save();
            $id = $model->id;
        }
        return $id;
    }
    
    /**
     * Cập nhật slider
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
                $flag = $model->update();
            }
        }
        return $flag;
    }
    
    /**
     * Xóa slider
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
}
