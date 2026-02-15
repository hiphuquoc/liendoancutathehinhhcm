<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Referee extends Model {
    use HasFactory;
    protected $table        = 'referee_info';
    protected $fillable     = [
        'phone',
        'email',
        'name',
        'position',
        'user_id',
        'total_learner',
        'total_teaching_hour',
        'total_prize',
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
            $model      = new Referee();
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
        return $this->hasMany(\App\Models\RelationSeoRefereeInfo::class, 'referee_info_id', 'id');
    }

    public function achievements() {
        return $this->hasMany(\App\Models\RefereeAchievement::class, 'referee_info_id', 'id');
    }

    public function skills() {
        return $this->hasMany(\App\Models\RefereeSkill::class, 'referee_info_id', 'id');
    }

    public function experiences() {
        return $this->hasMany(\App\Models\RefereeExperience::class, 'referee_info_id', 'id');
    }

    public function degrees() {
        return $this->hasMany(\App\Models\RefereeDegree::class, 'referee_info_id', 'id');
    }

    public function user() {
        return $this->belongsTo(\App\Models\User::class, 'user_id', 'id');
    }
}
