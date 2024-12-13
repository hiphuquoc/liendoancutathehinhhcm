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
        Schema::create('trainer_info', function (Blueprint $table) {
            $table->id();
            $table->integer('seo_id');
            // $table->text('full_name');
            // $table->text('job'); không dùng job vì đa ngôn ngữ, sử dụng chung với title Seo
            $table->text('phone');
            $table->text('email');
            // $table->text('summary')->nullable();
            // $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(){
        // Schema::dropIfExists('trainer_info');
    }
};
