<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('slider_info', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('seo_id')->nullable();
            $table->string('image', 255)->nullable()->comment('Ảnh slider desktop');
            $table->string('image_mobile', 255)->nullable()->comment('Ảnh slider mobile');
            $table->string('title', 500)->nullable()->comment('Tiêu đề');
            $table->text('description')->nullable()->comment('Mô tả');
            $table->string('position', 20)->default('left')->comment('Vị trí hiển thị: left, center, right');
            $table->string('button_text', 255)->nullable()->comment('Text nút');
            $table->string('button_icon', 100)->nullable()->comment('Icon cho button');
            $table->string('button_link', 500)->nullable()->comment('Link nút');
            $table->integer('ordering')->default(0)->comment('Thứ tự hiển thị');
            $table->tinyInteger('flag_show')->default(1)->comment('Hiển thị: 1-có, 0-không');
            $table->text('notes')->nullable()->comment('Ghi chú nội bộ');
            $table->timestamps();
            
            $table->index(['flag_show', 'ordering']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('slider_info');
    }
};
