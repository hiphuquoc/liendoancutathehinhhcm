<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RedirectInfo extends Model {
    use HasFactory;
    protected $table        = 'redirect_info';
    protected $fillable     = [
        'old_url', 
        'new_url'
    ];
    public $timestamps      = false;

    public static function getList($params = []){
        $query = self::select('*');
        
        // Tìm kiếm theo URL cũ hoặc URL mới
        if (!empty($params['search'])) {
            $search = $params['search'];
            $query->where(function($q) use ($search) {
                $q->where('old_url', 'like', '%' . $search . '%')
                  ->orWhere('new_url', 'like', '%' . $search . '%');
            });
        }
        
        $result = $query->orderBy('id', 'DESC')
                        ->paginate($params['paginate'] ?? 20);
        return $result;
    }

    public static function insertItem($params){
        $id             = 0;
        if(!empty($params)){
            $model      = new RedirectInfo();
            foreach($params as $key => $value) $model->{$key}  = $value;
            $model->save();
            $id         = $model->id;
        }
        return $id;
    }

    public static function updateItem($id, $params){
        $flag           = false;
        if(!empty($id)&&!empty($params)){
            $model      = self::find($id);
            foreach($params as $key => $value) $model->{$key}  = $value;
            $flag       = $model->update();
        }
        return $flag;
    }
}
