<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('trainer_info')) {
            if (!Schema::hasColumn('trainer_info', 'description')) {
                Schema::table('trainer_info', function (Blueprint $table) {
                    $table->text('description')->nullable()->after('position');
                });
            }
        }
        
        // Copy data from seo.seo_description to trainer_info.description
        if (Schema::hasTable('trainer_info') && Schema::hasTable('seo') 
            && Schema::hasColumn('trainer_info', 'description')) {
            
            DB::table('trainer_info')
                ->join('seo', 'trainer_info.seo_id', '=', 'seo.id')
                ->where('seo.type', 'trainer_info')
                ->where('seo.language', 'vi')
                ->whereNull('trainer_info.description')
                ->whereNotNull('seo.seo_description')
                ->update([
                    'trainer_info.description' => DB::raw('seo.seo_description')
                ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('trainer_info')) {
            if (Schema::hasColumn('trainer_info', 'description')) {
                Schema::table('trainer_info', function (Blueprint $table) {
                    $table->dropColumn('description');
                });
            }
        }
    }
};

