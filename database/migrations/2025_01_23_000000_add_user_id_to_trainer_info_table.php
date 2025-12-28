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
        // Add user_id column to trainer_info table
        if (Schema::hasTable('trainer_info')) {
            if (!Schema::hasColumn('trainer_info', 'user_id')) {
                Schema::table('trainer_info', function (Blueprint $table) {
                    $table->unsignedBigInteger('user_id')->nullable()->after('trainer_code');
                    $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
                });
            }
        }
        
        // Migrate existing data: match slug (from seo) with name (from users) and update user_id
        if (Schema::hasTable('trainer_info') && Schema::hasTable('seo') && Schema::hasTable('users')
            && Schema::hasColumn('trainer_info', 'user_id')) {
            
            // Get all trainers with their seo slug
            $trainers = DB::table('trainer_info')
                ->join('seo', 'trainer_info.seo_id', '=', 'seo.id')
                ->where('seo.type', 'trainer_info')
                ->where('seo.language', 'vi')
                ->whereNull('trainer_info.user_id') // Only update if user_id is null
                ->select('trainer_info.id', 'seo.slug')
                ->get();
            
            // Match slug with user name and update user_id
            foreach ($trainers as $trainer) {
                if (!empty($trainer->slug)) {
                    // Find user where name matches slug and has sub-admin role
                    $user = DB::table('users')
                        ->join('users_roles', 'users.id', '=', 'users_roles.user_id')
                        ->join('roles', 'users_roles.role_id', '=', 'roles.id')
                        ->where('users.name', $trainer->slug)
                        ->where('roles.name', 'sub-admin')
                        ->select('users.id')
                        ->first();
                    
                    if (!empty($user)) {
                        DB::table('trainer_info')
                            ->where('id', $trainer->id)
                            ->update(['user_id' => $user->id]);
                    }
                }
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
        if (Schema::hasTable('trainer_info')) {
            if (Schema::hasColumn('trainer_info', 'user_id')) {
                Schema::table('trainer_info', function (Blueprint $table) {
                    $table->dropForeign(['user_id']);
                    $table->dropColumn('user_id');
                });
            }
        }
    }
};

