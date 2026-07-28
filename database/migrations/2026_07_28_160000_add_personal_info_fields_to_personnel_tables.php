<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Khu vực / Kinh nghiệm (năm) / Ngôn ngữ — sidebar trang hồ sơ chi tiết.
     */
    public function up()
    {
        $tables = ['trainer_info', 'referee_info', 'athlete_info'];

        foreach ($tables as $tableName) {
            if (!Schema::hasTable($tableName)) {
                continue;
            }

            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (!Schema::hasColumn($tableName, 'area')) {
                    $table->string('area')->nullable()->after('position');
                }
                if (!Schema::hasColumn($tableName, 'years_experience')) {
                    $table->unsignedInteger('years_experience')->nullable()->after('area');
                }
                if (!Schema::hasColumn($tableName, 'languages')) {
                    $table->string('languages')->nullable()->after('years_experience');
                }
            });
        }
    }

    public function down()
    {
        $tables = ['trainer_info', 'referee_info', 'athlete_info'];
        $columns = ['area', 'years_experience', 'languages'];

        foreach ($tables as $tableName) {
            if (!Schema::hasTable($tableName)) {
                continue;
            }

            Schema::table($tableName, function (Blueprint $table) use ($tableName, $columns) {
                $drop = [];
                foreach ($columns as $col) {
                    if (Schema::hasColumn($tableName, $col)) {
                        $drop[] = $col;
                    }
                }
                if (!empty($drop)) {
                    $table->dropColumn($drop);
                }
            });
        }
    }
};
