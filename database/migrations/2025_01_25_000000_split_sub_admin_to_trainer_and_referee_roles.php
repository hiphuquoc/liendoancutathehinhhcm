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
        // 1. Thêm roles trainer và referee vào bảng roles
        if (Schema::hasTable('roles')) {
            // Kiểm tra xem role trainer đã tồn tại chưa
            $trainerRole = DB::table('roles')->where('slug', 'trainer')->first();
            if (!$trainerRole) {
                DB::table('roles')->insert([
                    'name' => 'Trainer',
                    'slug' => 'trainer',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            
            // Kiểm tra xem role referee đã tồn tại chưa
            $refereeRole = DB::table('roles')->where('slug', 'referee')->first();
            if (!$refereeRole) {
                DB::table('roles')->insert([
                    'name' => 'Referee',
                    'slug' => 'referee',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
        
        // 2. Cập nhật enum role trong bảng users để thêm trainer và referee
        if (Schema::hasTable('users') && Schema::hasColumn('users', 'role')) {
            // Lấy tất cả giá trị role hiện tại
            $roles = DB::select("SHOW COLUMNS FROM users WHERE Field = 'role'");
            if (!empty($roles)) {
                $enumValues = $roles[0]->Type;
                
                // Kiểm tra xem trainer và referee đã có trong enum chưa
                if (strpos($enumValues, 'trainer') === false || strpos($enumValues, 'referee') === false) {
                    // Thêm trainer và referee vào enum
                    DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('user', 'admin', 'sub-admin', 'trainer', 'referee') DEFAULT 'user'");
                }
            }
        }
        
        // 3. Migrate dữ liệu: Cập nhật role cho users có trainer_info
        if (Schema::hasTable('trainer_info') && Schema::hasTable('users')) {
            // Lấy role_id của trainer
            $trainerRoleId = DB::table('roles')->where('slug', 'trainer')->value('id');
            
            if ($trainerRoleId) {
                // Cập nhật role trong bảng users
                DB::table('users')
                    ->join('trainer_info', 'users.id', '=', 'trainer_info.user_id')
                    ->whereNotNull('trainer_info.user_id')
                    ->where('users.role', 'sub-admin')
                    ->update(['users.role' => 'trainer']);
                
                // Xóa users_roles cũ (role_id = 2 là sub-admin)
                DB::table('users_roles')
                    ->join('trainer_info', 'users_roles.user_id', '=', 'trainer_info.user_id')
                    ->whereNotNull('trainer_info.user_id')
                    ->where('users_roles.role_id', 2)
                    ->delete();
                
                // Thêm users_roles mới cho trainer
                $trainerUsers = DB::table('trainer_info')
                    ->whereNotNull('user_id')
                    ->pluck('user_id')
                    ->toArray();
                
                foreach ($trainerUsers as $userId) {
                    // Kiểm tra xem đã có role trainer chưa
                    $exists = DB::table('users_roles')
                        ->where('user_id', $userId)
                        ->where('role_id', $trainerRoleId)
                        ->exists();
                    
                    if (!$exists) {
                        DB::table('users_roles')->insert([
                            'user_id' => $userId,
                            'role_id' => $trainerRoleId,
                        ]);
                    }
                }
            }
        }
        
        // 4. Migrate dữ liệu: Cập nhật role cho users có referee_info
        if (Schema::hasTable('referee_info') && Schema::hasTable('users')) {
            // Lấy role_id của referee
            $refereeRoleId = DB::table('roles')->where('slug', 'referee')->value('id');
            
            if ($refereeRoleId) {
                // Cập nhật role trong bảng users
                DB::table('users')
                    ->join('referee_info', 'users.id', '=', 'referee_info.user_id')
                    ->whereNotNull('referee_info.user_id')
                    ->where('users.role', 'sub-admin')
                    ->update(['users.role' => 'referee']);
                
                // Xóa users_roles cũ (role_id = 2 là sub-admin)
                DB::table('users_roles')
                    ->join('referee_info', 'users_roles.user_id', '=', 'referee_info.user_id')
                    ->whereNotNull('referee_info.user_id')
                    ->where('users_roles.role_id', 2)
                    ->delete();
                
                // Thêm users_roles mới cho referee
                $refereeUsers = DB::table('referee_info')
                    ->whereNotNull('user_id')
                    ->pluck('user_id')
                    ->toArray();
                
                foreach ($refereeUsers as $userId) {
                    // Kiểm tra xem đã có role referee chưa
                    $exists = DB::table('users_roles')
                        ->where('user_id', $userId)
                        ->where('role_id', $refereeRoleId)
                        ->exists();
                    
                    if (!$exists) {
                        DB::table('users_roles')->insert([
                            'user_id' => $userId,
                            'role_id' => $refereeRoleId,
                        ]);
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
        // Revert lại role sub-admin cho tất cả trainer và referee
        if (Schema::hasTable('users')) {
            DB::table('users')
                ->whereIn('role', ['trainer', 'referee'])
                ->update(['role' => 'sub-admin']);
        }
        
        // Xóa users_roles của trainer và referee
        if (Schema::hasTable('roles') && Schema::hasTable('users_roles')) {
            $trainerRoleId = DB::table('roles')->where('slug', 'trainer')->value('id');
            $refereeRoleId = DB::table('roles')->where('slug', 'referee')->value('id');
            
            if ($trainerRoleId) {
                DB::table('users_roles')->where('role_id', $trainerRoleId)->delete();
            }
            
            if ($refereeRoleId) {
                DB::table('users_roles')->where('role_id', $refereeRoleId)->delete();
            }
        }
        
        // Xóa roles trainer và referee
        if (Schema::hasTable('roles')) {
            DB::table('roles')->where('slug', 'trainer')->delete();
            DB::table('roles')->where('slug', 'referee')->delete();
        }
        
        // Revert enum role trong bảng users
        if (Schema::hasTable('users') && Schema::hasColumn('users', 'role')) {
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('user', 'admin', 'sub-admin') DEFAULT 'user'");
        }
    }
};

