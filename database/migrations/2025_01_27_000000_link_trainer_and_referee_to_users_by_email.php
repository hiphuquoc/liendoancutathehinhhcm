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
        // Link trainer_info records to users by email
        if (Schema::hasTable('trainer_info') && Schema::hasTable('users')) {
            // Lấy tất cả trainer_info chưa có user_id hoặc user_id = null
            $trainers = DB::table('trainer_info')
                ->where(function($query) {
                    $query->whereNull('user_id')
                          ->orWhere('user_id', 0);
                })
                ->whereNotNull('email')
                ->where('email', '!=', '')
                ->select('id', 'email')
                ->get();
            
            foreach ($trainers as $trainer) {
                // Tìm user có email trùng
                $user = DB::table('users')
                    ->where('email', $trainer->email)
                    ->first();
                
                if ($user) {
                    // Cập nhật user_id cho trainer_info
                    DB::table('trainer_info')
                        ->where('id', $trainer->id)
                        ->update(['user_id' => $user->id]);
                    
                    // Đảm bảo user có role 'trainer' trong bảng users_roles
                    $trainerRoleId = DB::table('roles')->where('slug', 'trainer')->value('id');
                    if ($trainerRoleId) {
                        $exists = DB::table('users_roles')
                            ->where('user_id', $user->id)
                            ->where('role_id', $trainerRoleId)
                            ->exists();
                        
                        if (!$exists) {
                            DB::table('users_roles')->insert([
                                'user_id' => $user->id,
                                'role_id' => $trainerRoleId,
                            ]);
                        }
                    }
                }
            }
        }
        
        // Link referee_info records to users by email
        if (Schema::hasTable('referee_info') && Schema::hasTable('users')) {
            // Lấy tất cả referee_info chưa có user_id hoặc user_id = null
            $referees = DB::table('referee_info')
                ->where(function($query) {
                    $query->whereNull('user_id')
                          ->orWhere('user_id', 0);
                })
                ->whereNotNull('email')
                ->where('email', '!=', '')
                ->select('id', 'email')
                ->get();
            
            foreach ($referees as $referee) {
                // Tìm user có email trùng
                $user = DB::table('users')
                    ->where('email', $referee->email)
                    ->first();
                
                if ($user) {
                    // Cập nhật user_id cho referee_info
                    DB::table('referee_info')
                        ->where('id', $referee->id)
                        ->update(['user_id' => $user->id]);
                    
                    // Đảm bảo user có role 'referee' trong bảng users_roles
                    $refereeRoleId = DB::table('roles')->where('slug', 'referee')->value('id');
                    if ($refereeRoleId) {
                        $exists = DB::table('users_roles')
                            ->where('user_id', $user->id)
                            ->where('role_id', $refereeRoleId)
                            ->exists();
                        
                        if (!$exists) {
                            DB::table('users_roles')->insert([
                                'user_id' => $user->id,
                                'role_id' => $refereeRoleId,
                            ]);
                        }
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
        // This is a data migration, no need to reverse
        // User can manually unlink if needed
    }
};

