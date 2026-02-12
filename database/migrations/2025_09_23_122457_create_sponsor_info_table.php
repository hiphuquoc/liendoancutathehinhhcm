<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('sponsor_info', function (Blueprint $table) {
            $table->id();
            // Liên kết tới bảng SEO (để lấy hình ảnh / title / meta)
            $table->unsignedBigInteger('seo_id')->nullable();
            
            // Thông tin cơ bản
            $table->string('name'); // Tên đối tác/nhà tài trợ
            $table->string('logo')->nullable();
            $table->string('banner')->nullable();
            $table->text('description')->nullable();
            
            // Thông tin liên hệ
            $table->string('phone', 20)->nullable();
            $table->string('email', 191)->nullable();
            $table->string('website', 191)->nullable();
            $table->string('address', 255)->nullable();

            // Thông tin gói tài trợ
            $table->string('package_name')->nullable();
            $table->decimal('package_price', 15, 2)->nullable();
            $table->integer('package_duration')->nullable(); // số tháng
            
            // Thời gian và trạng thái
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('priority_order')->default(0);

            // Link đối tác
            $table->string('link_partner')->nullable();

            $table->timestamps();
        });
    }

    public function down()
    {
        // Schema::dropIfExists('sponsor_info');
    }
};
