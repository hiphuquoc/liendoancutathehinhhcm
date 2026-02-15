<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class Trainer extends Model {
    use HasFactory;
    protected $table        = 'trainer_info';
    protected $fillable     = [
        'phone',
        'email',
        'name',
        'position',
        'description',
        'trainer_code',
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

    public function user() {
        return $this->belongsTo(\App\Models\User::class, 'user_id', 'id');
    }

    public function achievements() {
        $query = $this->hasMany(\App\Models\TrainerAchievement::class, 'trainer_info_id', 'id');
        // Check if ordering column exists before ordering by it
        if (Schema::hasColumn('trainer_achievement', 'ordering')) {
            $query->orderBy('ordering')->orderBy('id');
        } else {
            $query->orderBy('id');
        }
        return $query;
    }

    public function skills() {
        $query = $this->hasMany(\App\Models\TrainerSkill::class, 'trainer_info_id', 'id');
        // Check if ordering column exists before ordering by it
        if (Schema::hasColumn('trainer_skill', 'ordering')) {
            $query->orderBy('ordering')->orderBy('id');
        } else {
            $query->orderBy('id');
        }
        return $query;
    }

    public function experiences() {
        $query = $this->hasMany(\App\Models\TrainerExperience::class, 'trainer_info_id', 'id');
        // Check if ordering column exists before ordering by it
        if (Schema::hasColumn('trainer_experience', 'ordering')) {
            $query->orderBy('ordering')->orderBy('id');
        } else {
            $query->orderBy('id');
        }
        return $query;
    }

    public function degrees() {
        $query = $this->hasMany(\App\Models\TrainerDegree::class, 'trainer_info_id', 'id');
        // Check if ordering column exists before ordering by it
        if (Schema::hasColumn('trainer_degree', 'ordering')) {
            $query->orderBy('ordering')->orderBy('id');
        } else {
            $query->orderBy('id');
        }
        return $query;
    }

    /**
     * Generate trainer code automatically
     * Format: N.O:001.T12.25/HLV-HWBF
     * 
     * @param int|null $trainerId The trainer_info id
     * @param int|null $seoId The seo_id to get created_at from
     * @return string|null
     */
    public static function generateTrainerCode($trainerId = null, $seoId = null) {
        // If trainer already has a code, return it
        if (!empty($trainerId)) {
            $currentTrainer = self::find($trainerId);
            if (!empty($currentTrainer) && !empty($currentTrainer->trainer_code)) {
                return $currentTrainer->trainer_code;
            }
            // Get seo_id from trainer if not provided
            if (empty($seoId) && !empty($currentTrainer)) {
                $seoId = $currentTrainer->seo_id;
            }
        }

        if (empty($seoId)) {
            return null;
        }

        // Get seo created_at
        $seo = \App\Models\Seo::where('id', $seoId)
            ->where('type', 'trainer_info')
            ->where('language', 'vi')
            ->first();

        if (empty($seo) || empty($seo->created_at)) {
            return null;
        }

        $date = \Carbon\Carbon::parse($seo->created_at);
        $month = $date->format('m'); // 01-12
        $year = $date->format('y'); // 25, 26, etc.
        $monthFormatted = 'T' . $month; // T01, T02, ..., T12
        $yearFormatted = $year; // 25, 26, etc.
        $federationCode = 'HWBF'; // Liên Đoàn Cử Tạ - Thể Hình HCM

        // Get all trainers created in the same month/year, ordered by trainer_info.id
        $trainersInSameMonth = DB::table('trainer_info')
            ->join('seo', 'trainer_info.seo_id', '=', 'seo.id')
            ->where('seo.type', 'trainer_info')
            ->where('seo.language', 'vi')
            ->whereYear('seo.created_at', $date->year)
            ->whereMonth('seo.created_at', $date->month)
            ->select('trainer_info.id', 'trainer_info.seo_id')
            ->orderBy('trainer_info.id', 'ASC')
            ->get();

        // Find the order number for this trainer (based on trainer_info.id)
        $orderNumber = 1;
        if (!empty($trainerId)) {
            // Find trainer by id
            foreach ($trainersInSameMonth as $index => $trainer) {
                if ($trainer->id == $trainerId) {
                    $orderNumber = $index + 1;
                    break;
                }
            }
        } else if (!empty($seoId)) {
            // Find trainer by seo_id
            foreach ($trainersInSameMonth as $index => $trainer) {
                if ($trainer->seo_id == $seoId) {
                    $orderNumber = $index + 1;
                    break;
                }
            }
            // If not found, it's a new trainer, order number is count + 1
            if ($orderNumber == 1 && $trainersInSameMonth->where('seo_id', $seoId)->isEmpty()) {
                $orderNumber = $trainersInSameMonth->count() + 1;
            }
        } else {
            // If creating new trainer, order number is count + 1
            $orderNumber = $trainersInSameMonth->count() + 1;
        }

        // Generate new code
        $orderNumberFormatted = str_pad($orderNumber, 3, '0', STR_PAD_LEFT); // 001, 002, etc.
        $trainerCode = "N.O:{$orderNumberFormatted}.{$monthFormatted}.{$yearFormatted}/HLV-{$federationCode}";

        return $trainerCode;
    }
}
