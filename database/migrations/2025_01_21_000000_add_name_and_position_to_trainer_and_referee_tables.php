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
        // Add name and position columns to trainer_info table
        if (Schema::hasTable('trainer_info')) {
            if (!Schema::hasColumn('trainer_info', 'name')) {
                Schema::table('trainer_info', function (Blueprint $table) {
                    $table->string('name')->nullable()->after('email');
                });
            }
            if (!Schema::hasColumn('trainer_info', 'position')) {
                Schema::table('trainer_info', function (Blueprint $table) {
                    $table->string('position')->nullable()->after('name');
                });
            }
        }
        
        // Add name and position columns to referee_info table
        if (Schema::hasTable('referee_info')) {
            if (!Schema::hasColumn('referee_info', 'name')) {
                Schema::table('referee_info', function (Blueprint $table) {
                    $table->string('name')->nullable()->after('email');
                });
            }
            if (!Schema::hasColumn('referee_info', 'position')) {
                Schema::table('referee_info', function (Blueprint $table) {
                    $table->string('position')->nullable()->after('name');
                });
            }
        }
        
        // Migrate existing data: copy from seo to trainer_info and referee_info
        if (Schema::hasTable('trainer_info') && Schema::hasTable('seo') 
            && Schema::hasColumn('trainer_info', 'name') && Schema::hasColumn('trainer_info', 'position')) {
            $trainers = DB::table('trainer_info')
                ->join('seo', 'trainer_info.seo_id', '=', 'seo.id')
                ->where('seo.type', 'trainer_info')
                ->where('seo.language', 'vi')
                ->whereNull('trainer_info.name') // Only update if name is null (not already migrated)
                ->select('trainer_info.id', 'seo.title')
                ->get();
            
            foreach ($trainers as $trainer) {
                $title = $trainer->title;
                $name = $title;
                $position = null;
                
                // If title contains " | ", split it
                if (strpos($title, ' | ') !== false) {
                    $parts = explode(' | ', $title, 2);
                    $name = trim($parts[0]);
                    $position = isset($parts[1]) ? trim($parts[1]) : null;
                }
                
                DB::table('trainer_info')
                    ->where('id', $trainer->id)
                    ->update([
                        'name' => $name,
                        'position' => $position
                    ]);
            }
        }
        
        if (Schema::hasTable('referee_info') && Schema::hasTable('seo')
            && Schema::hasColumn('referee_info', 'name') && Schema::hasColumn('referee_info', 'position')) {
            $referees = DB::table('referee_info')
                ->join('seo', 'referee_info.seo_id', '=', 'seo.id')
                ->where('seo.type', 'referee_info')
                ->where('seo.language', 'vi')
                ->whereNull('referee_info.name') // Only update if name is null (not already migrated)
                ->select('referee_info.id', 'seo.title')
                ->get();
            
            foreach ($referees as $referee) {
                $title = $referee->title;
                $name = $title;
                $position = null;
                
                // If title contains " | ", split it
                if (strpos($title, ' | ') !== false) {
                    $parts = explode(' | ', $title, 2);
                    $name = trim($parts[0]);
                    $position = isset($parts[1]) ? trim($parts[1]) : null;
                }
                
                DB::table('referee_info')
                    ->where('id', $referee->id)
                    ->update([
                        'name' => $name,
                        'position' => $position
                    ]);
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
            if (Schema::hasColumn('trainer_info', 'position')) {
                Schema::table('trainer_info', function (Blueprint $table) {
                    $table->dropColumn('position');
                });
            }
            if (Schema::hasColumn('trainer_info', 'name')) {
                Schema::table('trainer_info', function (Blueprint $table) {
                    $table->dropColumn('name');
                });
            }
        }
        
        if (Schema::hasTable('referee_info')) {
            if (Schema::hasColumn('referee_info', 'position')) {
                Schema::table('referee_info', function (Blueprint $table) {
                    $table->dropColumn('position');
                });
            }
            if (Schema::hasColumn('referee_info', 'name')) {
                Schema::table('referee_info', function (Blueprint $table) {
                    $table->dropColumn('name');
                });
            }
        }
    }
};

