<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $columns = ['total_learner', 'total_teaching_hour', 'total_prize'];
        
        // Add to referee_info
        if (Schema::hasTable('referee_info')) {
            Schema::table('referee_info', function (Blueprint $table) use ($columns) {
                foreach ($columns as $col) {
                    if (!Schema::hasColumn('referee_info', $col)) {
                        $table->integer($col)->nullable()->default(0);
                    }
                }
            });
        }

        // Add to trainer_info
        if (Schema::hasTable('trainer_info')) {
            Schema::table('trainer_info', function (Blueprint $table) use ($columns) {
                foreach ($columns as $col) {
                    if (!Schema::hasColumn('trainer_info', $col)) {
                        $table->integer($col)->nullable()->default(0);
                    }
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $columns = ['total_learner', 'total_teaching_hour', 'total_prize'];
        
        if (Schema::hasTable('referee_info')) {
            Schema::table('referee_info', function (Blueprint $table) use ($columns) {
                $table->dropColumn($columns);
            });
        }

        if (Schema::hasTable('trainer_info')) {
            Schema::table('trainer_info', function (Blueprint $table) use ($columns) {
                $table->dropColumn($columns);
            });
        }
    }
};
