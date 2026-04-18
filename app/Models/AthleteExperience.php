<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AthleteExperience extends Model
{
    use HasFactory;

    protected $table = 'athlete_experience';

    protected $fillable = [
        'title',
        'company',
        'ordering',
    ];

    public $timestamps = false;

    public static function insertItem($params)
    {
        $id = 0;
        if (!empty($params)) {
            $model = new AthleteExperience();
            foreach ($params as $key => $value) {
                $model->{$key} = $value;
            }
            $model->save();
            $id = $model->id;
        }

        return $id;
    }

    public static function updateItem($id, $params)
    {
        $flag = false;
        if (!empty($id) && !empty($params)) {
            $model = self::find($id);
            foreach ($params as $key => $value) {
                $model->{$key} = $value;
            }
            $flag = $model->update();
        }

        return $flag;
    }

    public function contents()
    {
        return $this->hasMany(\App\Models\AthleteExperienceContent::class, 'athlete_experience_id', 'id');
    }
}
