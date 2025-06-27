<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trainer extends Model {
    use HasFactory;
    protected $table        = 'trainer_info';
    protected $fillable     = [
        'phone',
        'email',
    ];
    public $timestamps = false;

    public static function getList($params = null){
        $result     = self::select('*')
                        /* tìm theo tên */
                        ->when(!empty($params['search_name']), function($query) use($params){
                            $searchName = $params['search_name'];
                            $query->whereHas('seo', function($subQuery) use($searchName){
                                $subQuery->where('title', 'like', '%'.$searchName.'%');
                            });
                        })
                        ->with('seo')
                        ->orderBy('id', 'DESC')
                        ->paginate($params['paginate']);
        return $result;
    }

    public static function insertItem($params){
        $id             = 0;
        if(!empty($params)){
            $model      = new Trainer();
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

    public function seo() {
        return $this->hasOne(\App\Models\Seo::class, 'id', 'seo_id');
    }

    public function seos() {
        return $this->hasMany(\App\Models\RelationSeoTrainerInfo::class, 'trainer_info_id', 'id');
    }

    public function achievements() {
        return $this->hasMany(\App\Models\TrainerAchievement::class, 'trainer_info_id', 'id');
    }

    public function skills() {
        return $this->hasMany(\App\Models\TrainerSkill::class, 'trainer_info_id', 'id');
    }

    public function experiences() {
        return $this->hasMany(\App\Models\TrainerExperience::class, 'trainer_info_id', 'id');
    }

    public function degrees() {
        return $this->hasMany(\App\Models\TrainerDegree::class, 'trainer_info_id', 'id');
    }
}
