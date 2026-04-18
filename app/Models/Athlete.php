<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class Athlete extends Model
{
    use HasFactory;

    protected $table = 'athlete_info';

    protected $fillable = [
        'phone',
        'email',
        'name',
        'position',
        'description',
        'athlete_code',
        'user_id',
        'total_learner',
        'total_teaching_hour',
        'total_prize',
    ];

    public $timestamps = false;

    public static function getList($params = null)
    {
        $result = self::select('*')
            ->when(!empty($params['search_name']), function ($query) use ($params) {
                $searchName = $params['search_name'];
                $query->whereHas('seo', function ($subQuery) use ($searchName) {
                    $subQuery->where('title', 'like', '%'.$searchName.'%');
                });
            })
            ->with('seo')
            ->orderBy('id', 'DESC')
            ->paginate($params['paginate']);

        return $result;
    }

    public static function insertItem($params)
    {
        $id = 0;
        if (!empty($params)) {
            $model = new Athlete();
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

    public function seo()
    {
        return $this->hasOne(\App\Models\Seo::class, 'id', 'seo_id');
    }

    public function seos()
    {
        return $this->hasMany(\App\Models\RelationSeoAthleteInfo::class, 'athlete_info_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id', 'id');
    }

    public function achievements()
    {
        $query = $this->hasMany(\App\Models\AthleteAchievement::class, 'athlete_info_id', 'id');
        if (Schema::hasColumn('athlete_achievement', 'ordering')) {
            $query->orderBy('ordering')->orderBy('id');
        } else {
            $query->orderBy('id');
        }

        return $query;
    }

    public function skills()
    {
        $query = $this->hasMany(\App\Models\AthleteSkill::class, 'athlete_info_id', 'id');
        if (Schema::hasColumn('athlete_skill', 'ordering')) {
            $query->orderBy('ordering')->orderBy('id');
        } else {
            $query->orderBy('id');
        }

        return $query;
    }

    public function experiences()
    {
        $query = $this->hasMany(\App\Models\AthleteExperience::class, 'athlete_info_id', 'id');
        if (Schema::hasColumn('athlete_experience', 'ordering')) {
            $query->orderBy('ordering')->orderBy('id');
        } else {
            $query->orderBy('id');
        }

        return $query;
    }

    public function degrees()
    {
        $query = $this->hasMany(\App\Models\AthleteDegree::class, 'athlete_info_id', 'id');
        if (Schema::hasColumn('athlete_degree', 'ordering')) {
            $query->orderBy('ordering')->orderBy('id');
        } else {
            $query->orderBy('id');
        }

        return $query;
    }

    public function activityImages()
    {
        return $this->hasMany(\App\Models\ProfileActivityImage::class, 'owner_id', 'id')
            ->where('owner_type', \App\Models\ProfileActivityImage::OWNER_TYPE_ATHLETE)
            ->orderBy('ordering')
            ->orderBy('id');
    }

    /**
     * Format: N.O:001.T12.25/VDV-HWBF
     */
    public static function generateAthleteCode($athleteId = null, $seoId = null)
    {
        if (!empty($athleteId)) {
            $current = self::find($athleteId);
            if (!empty($current) && !empty($current->athlete_code)) {
                return $current->athlete_code;
            }
            if (empty($seoId) && !empty($current)) {
                $seoId = $current->seo_id;
            }
        }

        if (empty($seoId)) {
            return null;
        }

        $seo = \App\Models\Seo::where('id', $seoId)
            ->where('type', 'athlete_info')
            ->where('language', 'vi')
            ->first();

        if (empty($seo) || empty($seo->created_at)) {
            return null;
        }

        $date = \Carbon\Carbon::parse($seo->created_at);
        $month = $date->format('m');
        $year = $date->format('y');
        $monthFormatted = 'T'.$month;
        $yearFormatted = $year;
        $federationCode = 'HWBF';

        $inSameMonth = DB::table('athlete_info')
            ->join('seo', 'athlete_info.seo_id', '=', 'seo.id')
            ->where('seo.type', 'athlete_info')
            ->where('seo.language', 'vi')
            ->whereYear('seo.created_at', $date->year)
            ->whereMonth('seo.created_at', $date->month)
            ->select('athlete_info.id', 'athlete_info.seo_id')
            ->orderBy('athlete_info.id', 'ASC')
            ->get();

        $orderNumber = 1;
        if (!empty($athleteId)) {
            foreach ($inSameMonth as $index => $row) {
                if ($row->id == $athleteId) {
                    $orderNumber = $index + 1;
                    break;
                }
            }
        } elseif (!empty($seoId)) {
            foreach ($inSameMonth as $index => $row) {
                if ($row->seo_id == $seoId) {
                    $orderNumber = $index + 1;
                    break;
                }
            }
            if ($orderNumber == 1 && $inSameMonth->where('seo_id', $seoId)->isEmpty()) {
                $orderNumber = $inSameMonth->count() + 1;
            }
        } else {
            $orderNumber = $inSameMonth->count() + 1;
        }

        $orderNumberFormatted = str_pad($orderNumber, 3, '0', STR_PAD_LEFT);

        return "N.O:{$orderNumberFormatted}.{$monthFormatted}.{$yearFormatted}/VDV-{$federationCode}";
    }
}
