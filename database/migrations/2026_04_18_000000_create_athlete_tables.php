<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Bảng hệ VĐV — cấu trúc tương đương trainer_info và các bảng con.
     */
    public function up(): void
    {
        if (!Schema::hasTable('athlete_info')) {
            Schema::create('athlete_info', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('seo_id');
                $table->text('phone')->nullable();
                $table->text('email')->nullable();
                $table->string('name')->nullable();
                $table->string('position')->nullable();
                $table->text('description')->nullable();
                $table->string('athlete_code')->nullable();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->integer('total_learner')->nullable()->default(0);
                $table->integer('total_teaching_hour')->nullable()->default(0);
                $table->integer('total_prize')->nullable()->default(0);
            });
        }

        if (!Schema::hasTable('relation_seo_athlete_info')) {
            Schema::create('relation_seo_athlete_info', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('seo_id');
                $table->unsignedBigInteger('athlete_info_id');
            });
        }

        if (!Schema::hasTable('athlete_achievement')) {
            Schema::create('athlete_achievement', function (Blueprint $table) {
                $table->id();
                $table->integer('athlete_info_id');
                $table->text('content');
                $table->integer('ordering')->default(0);
            });
        }

        if (!Schema::hasTable('athlete_skill')) {
            Schema::create('athlete_skill', function (Blueprint $table) {
                $table->id();
                $table->integer('athlete_info_id');
                $table->text('skill');
                $table->text('percent');
                $table->integer('ordering')->default(0);
            });
        }

        if (!Schema::hasTable('athlete_experience')) {
            Schema::create('athlete_experience', function (Blueprint $table) {
                $table->id();
                $table->integer('athlete_info_id');
                $table->text('title');
                $table->text('company');
                $table->integer('ordering')->default(0);
            });
        }

        if (!Schema::hasTable('athlete_experience_content')) {
            Schema::create('athlete_experience_content', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('athlete_experience_id');
                $table->text('content');
            });
        }

        if (!Schema::hasTable('athlete_degree')) {
            Schema::create('athlete_degree', function (Blueprint $table) {
                $table->id();
                $table->integer('athlete_info_id');
                $table->text('title');
                $table->text('school');
                $table->integer('ordering')->default(0);
            });
        }

        if (!Schema::hasTable('athlete_degree_content')) {
            Schema::create('athlete_degree_content', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('athlete_degree_id');
                $table->text('content');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('athlete_degree_content');
        Schema::dropIfExists('athlete_degree');
        Schema::dropIfExists('athlete_experience_content');
        Schema::dropIfExists('athlete_experience');
        Schema::dropIfExists('athlete_skill');
        Schema::dropIfExists('athlete_achievement');
        Schema::dropIfExists('relation_seo_athlete_info');
        Schema::dropIfExists('athlete_info');
    }
};
