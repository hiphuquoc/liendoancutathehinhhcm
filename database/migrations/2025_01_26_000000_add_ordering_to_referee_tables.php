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
        // Add ordering to referee_achievement
        if (Schema::hasTable('referee_achievement') && !Schema::hasColumn('referee_achievement', 'ordering')) {
            Schema::table('referee_achievement', function (Blueprint $table) {
                $table->integer('ordering')->default(0)->after('content');
            });
        }
        
        // Add ordering to referee_skill
        if (Schema::hasTable('referee_skill') && !Schema::hasColumn('referee_skill', 'ordering')) {
            Schema::table('referee_skill', function (Blueprint $table) {
                $table->integer('ordering')->default(0)->after('percent');
            });
        }
        
        // Add ordering to referee_experience
        if (Schema::hasTable('referee_experience') && !Schema::hasColumn('referee_experience', 'ordering')) {
            Schema::table('referee_experience', function (Blueprint $table) {
                $table->integer('ordering')->default(0)->after('company');
            });
        }
        
        // Add ordering to referee_degree
        if (Schema::hasTable('referee_degree') && !Schema::hasColumn('referee_degree', 'ordering')) {
            Schema::table('referee_degree', function (Blueprint $table) {
                $table->integer('ordering')->default(0)->after('school');
            });
        }
        
        // Update existing records to set ordering based on id
        if (Schema::hasTable('referee_achievement')) {
            DB::statement('UPDATE referee_achievement SET ordering = id WHERE ordering IS NULL OR ordering = 0');
        }
        
        if (Schema::hasTable('referee_skill')) {
            DB::statement('UPDATE referee_skill SET ordering = id WHERE ordering IS NULL OR ordering = 0');
        }
        
        if (Schema::hasTable('referee_experience')) {
            DB::statement('UPDATE referee_experience SET ordering = id WHERE ordering IS NULL OR ordering = 0');
        }
        
        if (Schema::hasTable('referee_degree')) {
            DB::statement('UPDATE referee_degree SET ordering = id WHERE ordering IS NULL OR ordering = 0');
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('referee_achievement') && Schema::hasColumn('referee_achievement', 'ordering')) {
            Schema::table('referee_achievement', function (Blueprint $table) {
                $table->dropColumn('ordering');
            });
        }
        
        if (Schema::hasTable('referee_skill') && Schema::hasColumn('referee_skill', 'ordering')) {
            Schema::table('referee_skill', function (Blueprint $table) {
                $table->dropColumn('ordering');
            });
        }
        
        if (Schema::hasTable('referee_experience') && Schema::hasColumn('referee_experience', 'ordering')) {
            Schema::table('referee_experience', function (Blueprint $table) {
                $table->dropColumn('ordering');
            });
        }
        
        if (Schema::hasTable('referee_degree') && Schema::hasColumn('referee_degree', 'ordering')) {
            Schema::table('referee_degree', function (Blueprint $table) {
                $table->dropColumn('ordering');
            });
        }
    }
};

