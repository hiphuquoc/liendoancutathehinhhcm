<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Add trainer_code column to trainer_info table
        if (Schema::hasTable('trainer_info')) {
            if (!Schema::hasColumn('trainer_info', 'trainer_code')) {
                Schema::table('trainer_info', function (Blueprint $table) {
                    $table->string('trainer_code', 100)->nullable()->after('position');
                });
            }
        }
        
        // Migrate existing data: generate trainer_code for all trainers
        if (Schema::hasTable('trainer_info') && Schema::hasTable('seo') 
            && Schema::hasColumn('trainer_info', 'trainer_code')) {
            
            // Get all trainers with their seo created_at, ordered by id
            $trainers = DB::table('trainer_info')
                ->join('seo', 'trainer_info.seo_id', '=', 'seo.id')
                ->where('seo.type', 'trainer_info')
                ->where('seo.language', 'vi')
                ->whereNull('trainer_info.trainer_code') // Only update if trainer_code is null
                ->select('trainer_info.id', 'seo.created_at')
                ->orderBy('trainer_info.id', 'ASC')
                ->get();
            
            // Group trainers by month and year
            $groupedByMonthYear = [];
            foreach ($trainers as $trainer) {
                $createdAt = $trainer->created_at;
                if ($createdAt) {
                    $date = \Carbon\Carbon::parse($createdAt);
                    $month = $date->format('m'); // 01-12
                    $year = $date->format('y'); // 25, 26, etc.
                    $key = $year . '-' . $month;
                    
                    if (!isset($groupedByMonthYear[$key])) {
                        $groupedByMonthYear[$key] = [];
                    }
                    $groupedByMonthYear[$key][] = $trainer;
                }
            }
            
            // Generate trainer_code for each group
            foreach ($groupedByMonthYear as $key => $trainerGroup) {
                list($year, $month) = explode('-', $key);
                $monthFormatted = 'T' . $month; // T01, T02, ..., T12
                $yearFormatted = $year; // 25, 26, etc.
                $federationCode = 'HWBF'; // Liên Đoàn Cử Tạ - Thể Hình HCM
                
                // Sort by id to get correct order
                usort($trainerGroup, function($a, $b) {
                    return $a->id <=> $b->id;
                });
                
                // Generate code for each trainer in this month/year
                foreach ($trainerGroup as $index => $trainer) {
                    $orderNumber = str_pad($index + 1, 3, '0', STR_PAD_LEFT); // 001, 002, etc.
                    $trainerCode = "N.O:{$orderNumber}.{$monthFormatted}.{$yearFormatted}/HLV-{$federationCode}";
                    
                    DB::table('trainer_info')
                        ->where('id', $trainer->id)
                        ->update(['trainer_code' => $trainerCode]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('trainer_info')) {
            if (Schema::hasColumn('trainer_info', 'trainer_code')) {
                Schema::table('trainer_info', function (Blueprint $table) {
                    $table->dropColumn('trainer_code');
                });
            }
        }
    }
};

