<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trainer extends Model {
    use HasFactory;
    protected $table        = 'trainer_info';
    protected $fillable     = [
        'full_name', 
        'job',
        'phone',
        'email',
        'summary',
    ];
    public $timestamps = true;

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
