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
        // Add user_id column to referee_info table
        if (Schema::hasTable('referee_info')) {
            if (!Schema::hasColumn('referee_info', 'user_id')) {
                Schema::table('referee_info', function (Blueprint $table) {
                    $table->unsignedBigInteger('user_id')->nullable()->after('position');
                    $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
                });
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
        if (Schema::hasTable('referee_info')) {
            if (Schema::hasColumn('referee_info', 'user_id')) {
                Schema::table('referee_info', function (Blueprint $table) {
                    $table->dropForeign(['user_id']);
                    $table->dropColumn('user_id');
                });
            }
        }
    }
};

