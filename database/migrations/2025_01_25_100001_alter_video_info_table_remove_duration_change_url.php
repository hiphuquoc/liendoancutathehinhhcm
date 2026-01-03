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
        // Đổi tên cột video_url thành file_cloud (dùng DB::statement vì renameColumn có thể không hoạt động với một số driver)
        if (Schema::hasColumn('video_info', 'video_url')) {
            Schema::table('video_info', function (Blueprint $table) {
                $table->renameColumn('video_url', 'file_cloud');
            });
        }
        
        // Bỏ cột duration
        if (Schema::hasColumn('video_info', 'duration')) {
            Schema::table('video_info', function (Blueprint $table) {
                $table->dropColumn('duration');
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
        // Thêm lại cột duration
        if (!Schema::hasColumn('video_info', 'duration')) {
            Schema::table('video_info', function (Blueprint $table) {
                $table->integer('duration')->nullable()->after('thumbnail');
            });
        }
        
        // Đổi lại file_cloud thành video_url
        if (Schema::hasColumn('video_info', 'file_cloud')) {
            Schema::table('video_info', function (Blueprint $table) {
                $table->renameColumn('file_cloud', 'video_url');
            });
        }
    }
};
