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
        // Add ordering to trainer_achievement
        if (Schema::hasTable('trainer_achievement') && !Schema::hasColumn('trainer_achievement', 'ordering')) {
            Schema::table('trainer_achievement', function (Blueprint $table) {
                $table->integer('ordering')->default(0)->after('content');
            });
        }
        
        // Add ordering to trainer_skill
        if (Schema::hasTable('trainer_skill') && !Schema::hasColumn('trainer_skill', 'ordering')) {
            Schema::table('trainer_skill', function (Blueprint $table) {
                $table->integer('ordering')->default(0)->after('percent');
            });
        }
        
        // Add ordering to trainer_experience
        if (Schema::hasTable('trainer_experience') && !Schema::hasColumn('trainer_experience', 'ordering')) {
            Schema::table('trainer_experience', function (Blueprint $table) {
                $table->integer('ordering')->default(0)->after('company');
            });
        }
        
        // Add ordering to trainer_degree
        if (Schema::hasTable('trainer_degree') && !Schema::hasColumn('trainer_degree', 'ordering')) {
            Schema::table('trainer_degree', function (Blueprint $table) {
                $table->integer('ordering')->default(0)->after('school');
            });
        }
        
        // Update existing records to set ordering based on id
        if (Schema::hasTable('trainer_achievement')) {
            DB::statement('UPDATE trainer_achievement SET ordering = id WHERE ordering IS NULL OR ordering = 0');
        }
        if (Schema::hasTable('trainer_skill')) {
            DB::statement('UPDATE trainer_skill SET ordering = id WHERE ordering IS NULL OR ordering = 0');
        }
        if (Schema::hasTable('trainer_experience')) {
            DB::statement('UPDATE trainer_experience SET ordering = id WHERE ordering IS NULL OR ordering = 0');
        }
        if (Schema::hasTable('trainer_degree')) {
            DB::statement('UPDATE trainer_degree SET ordering = id WHERE ordering IS NULL OR ordering = 0');
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('trainer_achievement') && Schema::hasColumn('trainer_achievement', 'ordering')) {
            Schema::table('trainer_achievement', function (Blueprint $table) {
                $table->dropColumn('ordering');
            });
        }
        
        if (Schema::hasTable('trainer_skill') && Schema::hasColumn('trainer_skill', 'ordering')) {
            Schema::table('trainer_skill', function (Blueprint $table) {
                $table->dropColumn('ordering');
            });
        }
        
        if (Schema::hasTable('trainer_experience') && Schema::hasColumn('trainer_experience', 'ordering')) {
            Schema::table('trainer_experience', function (Blueprint $table) {
                $table->dropColumn('ordering');
            });
        }
        
        if (Schema::hasTable('trainer_degree') && Schema::hasColumn('trainer_degree', 'ordering')) {
            Schema::table('trainer_degree', function (Blueprint $table) {
                $table->dropColumn('ordering');
            });
        }
    }
};

