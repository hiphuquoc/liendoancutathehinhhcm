<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Hình ảnh hoạt động của HLV / Trọng tài (upload Google Cloud).
     *
     * @return void
     */
    public function up()
    {
        Schema::create('profile_activity_images', function (Blueprint $table) {
            $table->id();
            $table->string('owner_type', 32)->comment('trainer_info|referee_info');
            $table->unsignedBigInteger('owner_id')->comment('trainer_info.id hoặc referee_info.id');
            $table->string('image')->comment('Đường dẫn ảnh trên Google Cloud');
            $table->unsignedSmallInteger('ordering')->default(0);
            $table->timestamps();
        });

        Schema::table('profile_activity_images', function (Blueprint $table) {
            $table->index(['owner_type', 'owner_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('profile_activity_images');
    }
};
