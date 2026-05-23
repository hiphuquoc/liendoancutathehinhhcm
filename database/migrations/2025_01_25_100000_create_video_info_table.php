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
        Schema::create('video_info', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->text('video_url'); // YouTube, Vimeo, hoặc direct link
            $table->text('thumbnail')->nullable(); // URL ảnh thumbnail
            $table->integer('duration')->nullable(); // Thời lượng video (giây)
            $table->string('category')->nullable(); // Danh mục video
            $table->integer('ordering')->default(0); // Thứ tự sắp xếp
            $table->boolean('status')->default(1); // 1: active, 0: inactive
            $table->integer('created_by')->nullable(); // user_id người tạo
            $table->integer('updated_by')->nullable(); // user_id người cập nhật
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('video_info');
    }
};

