<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 1. Xóa tất cả users có role trainer hoặc referee
        if (Schema::hasTable('users_roles') && Schema::hasTable('roles')) {
            // Lấy role_id của trainer và referee
            $trainerRoleId = DB::table('roles')->where('slug', 'trainer')->value('id');
            $refereeRoleId = DB::table('roles')->where('slug', 'referee')->value('id');
            
            if ($trainerRoleId || $refereeRoleId) {
                $userIdsToDelete = [];
                
                if ($trainerRoleId) {
                    $trainerUserIds = DB::table('users_roles')
                        ->where('role_id', $trainerRoleId)
                        ->pluck('user_id')
                        ->toArray();
                    $userIdsToDelete = array_merge($userIdsToDelete, $trainerUserIds);
                }
                
                if ($refereeRoleId) {
                    $refereeUserIds = DB::table('users_roles')
                        ->where('role_id', $refereeRoleId)
                        ->pluck('user_id')
                        ->toArray();
                    $userIdsToDelete = array_merge($userIdsToDelete, $refereeUserIds);
                }
                
                // Loại bỏ duplicate
                $userIdsToDelete = array_unique($userIdsToDelete);
                
                if (!empty($userIdsToDelete)) {
                    // Xóa trong users_roles
                    DB::table('users_roles')
                        ->whereIn('user_id', $userIdsToDelete)
                        ->delete();
                    
                    // Xóa users có role trainer hoặc referee
                    DB::table('users')
                        ->whereIn('id', $userIdsToDelete)
                        ->delete();
                }
            }
        }
        
        // 2. Reset user_id trong trainer_info và referee_info về null
        if (Schema::hasTable('trainer_info')) {
            DB::table('trainer_info')->update(['user_id' => null]);
        }
        
        if (Schema::hasTable('referee_info')) {
            DB::table('referee_info')->update(['user_id' => null]);
        }
        
        // 3. Tạo lại users từ trainer_info và referee_info
        if (Schema::hasTable('trainer_info') && Schema::hasTable('referee_info') && 
            Schema::hasTable('seo') && Schema::hasTable('users')) {
            
            // Map để lưu trữ user đã tạo
            $userMapByEmail = []; // key = email (lowercase), value = user_id
            $userMapByUsername = []; // key = username, value = user_id
            $usedUsernames = []; // array of usernames đã sử dụng (để check unique khi cần tạo mới)
            
            // Lấy role_id
            $trainerRoleId = DB::table('roles')->where('slug', 'trainer')->value('id');
            $refereeRoleId = DB::table('roles')->where('slug', 'referee')->value('id');
            
            // Helper function để tạo username từ slug
            $generateUsername = function($slug) {
                return str_replace('-', '', strtolower($slug));
            };
            
            // Helper function để tạo username unique (chỉ dùng khi email khác)
            $generateUniqueUsername = function($baseUsername, &$usedUsernames) {
                $username = $baseUsername;
                $counter = 2;
                $baseUsernameClean = $username;
                
                while (in_array($username, $usedUsernames)) {
                    if (preg_match('/^(.+?)(\d+)$/', $username, $matches)) {
                        $baseUsernameClean = $matches[1];
                        $counter = (int)$matches[2] + 1;
                    }
                    $username = $baseUsernameClean . $counter;
                    $counter++;
                }
                
                $usedUsernames[] = $username;
                return $username;
            };
            
            // ===== XỬ LÝ TRAINER_INFO =====
            $trainers = DB::table('trainer_info')
                ->join('seo', 'trainer_info.seo_id', '=', 'seo.id')
                ->whereNotNull('trainer_info.email')
                ->where('trainer_info.email', '!=', '')
                ->whereNotNull('seo.slug')
                ->where('seo.slug', '!=', '')
                ->select(
                    'trainer_info.id as trainer_id',
                    'trainer_info.name',
                    'trainer_info.email',
                    'trainer_info.phone',
                    'trainer_info.position',
                    'seo.slug'
                )
                ->get();
            
            foreach ($trainers as $trainer) {
                $email = strtolower(trim($trainer->email));
                $slug = $trainer->slug;
                $baseUsername = $generateUsername($slug);
                
                // Tìm user đã tồn tại bằng email hoặc username
                $existingUser = null;
                $userId = null;
                
                // 1. Kiểm tra theo email trước (ưu tiên)
                if (isset($userMapByEmail[$email])) {
                    $userId = $userMapByEmail[$email];
                    $existingUser = DB::table('users')->where('id', $userId)->first();
                } else {
                    // 2. Kiểm tra theo username (nếu username trùng → cùng 1 người)
                    if (isset($userMapByUsername[$baseUsername])) {
                        $userId = $userMapByUsername[$baseUsername];
                        $existingUser = DB::table('users')->where('id', $userId)->first();
                        // Cập nhật map email
                        $userMapByEmail[$email] = $userId;
                    } else {
                        // 3. Kiểm tra trong database
                        $dbUserByEmail = DB::table('users')->where('email', $trainer->email)->first();
                        if ($dbUserByEmail) {
                            $userId = $dbUserByEmail->id;
                            $existingUser = $dbUserByEmail;
                            $userMapByEmail[$email] = $userId;
                            $userMapByUsername[$dbUserByEmail->username] = $userId;
                            $usedUsernames[] = $dbUserByEmail->username;
                        } else {
                            $dbUserByUsername = DB::table('users')->where('username', $baseUsername)->first();
                            if ($dbUserByUsername) {
                                // Username trùng → cùng 1 người
                                $userId = $dbUserByUsername->id;
                                $existingUser = $dbUserByUsername;
                                $userMapByEmail[$email] = $userId;
                                $userMapByUsername[$baseUsername] = $userId;
                            }
                        }
                    }
                }
                
                if (!$existingUser) {
                    // Tạo user mới
                    $username = $generateUniqueUsername($baseUsername, $usedUsernames);
                    
                    $userId = DB::table('users')->insertGetId([
                        'name' => $trainer->name,
                        'email' => $trainer->email,
                        'username' => $username,
                        'password' => Hash::make($username),
                        'position' => $trainer->position ?? 'Huấn luyện viên cá nhân (PT)',
                        'phone' => $trainer->phone,
                        'role' => 'trainer',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    
                    $userMapByEmail[$email] = $userId;
                    $userMapByUsername[$username] = $userId;
                    
                    // Thêm role trainer
                    if ($trainerRoleId) {
                        DB::table('users_roles')->insert([
                            'user_id' => $userId,
                            'role_id' => $trainerRoleId,
                        ]);
                    }
                } else {
                    // User đã tồn tại, đảm bảo có role trainer
                    if ($trainerRoleId) {
                        $hasTrainerRole = DB::table('users_roles')
                            ->where('user_id', $userId)
                            ->where('role_id', $trainerRoleId)
                            ->exists();
                        
                        if (!$hasTrainerRole) {
                            DB::table('users_roles')->insert([
                                'user_id' => $userId,
                                'role_id' => $trainerRoleId,
                            ]);
                        }
                    }
                    
                    // Cập nhật role column nếu cần (ưu tiên trainer nếu chưa có)
                    $userRole = DB::table('users')->where('id', $userId)->value('role');
                    if ($userRole !== 'trainer' && $userRole !== 'referee') {
                        DB::table('users')
                            ->where('id', $userId)
                            ->update(['role' => 'trainer']);
                    }
                }
                
                // Link trainer với user
                DB::table('trainer_info')
                    ->where('id', $trainer->trainer_id)
                    ->update(['user_id' => $userId]);
            }
            
            // ===== XỬ LÝ REFEREE_INFO =====
            $referees = DB::table('referee_info')
                ->join('seo', 'referee_info.seo_id', '=', 'seo.id')
                ->whereNotNull('referee_info.email')
                ->where('referee_info.email', '!=', '')
                ->whereNotNull('seo.slug')
                ->where('seo.slug', '!=', '')
                ->select(
                    'referee_info.id as referee_id',
                    'referee_info.name',
                    'referee_info.email',
                    'referee_info.phone',
                    'referee_info.position',
                    'seo.slug'
                )
                ->get();
            
            foreach ($referees as $referee) {
                $email = strtolower(trim($referee->email));
                $slug = $referee->slug;
                $baseUsername = $generateUsername($slug);
                
                // Tìm user đã tồn tại bằng email hoặc username
                $existingUser = null;
                $userId = null;
                
                // 1. Kiểm tra theo email trước (ưu tiên)
                if (isset($userMapByEmail[$email])) {
                    $userId = $userMapByEmail[$email];
                    $existingUser = DB::table('users')->where('id', $userId)->first();
                } else {
                    // 2. Kiểm tra theo username (nếu username trùng → cùng 1 người)
                    if (isset($userMapByUsername[$baseUsername])) {
                        $userId = $userMapByUsername[$baseUsername];
                        $existingUser = DB::table('users')->where('id', $userId)->first();
                        // Cập nhật map email
                        $userMapByEmail[$email] = $userId;
                    } else {
                        // 3. Kiểm tra trong database
                        $dbUserByEmail = DB::table('users')->where('email', $referee->email)->first();
                        if ($dbUserByEmail) {
                            $userId = $dbUserByEmail->id;
                            $existingUser = $dbUserByEmail;
                            $userMapByEmail[$email] = $userId;
                            $userMapByUsername[$dbUserByEmail->username] = $userId;
                            $usedUsernames[] = $dbUserByEmail->username;
                        } else {
                            $dbUserByUsername = DB::table('users')->where('username', $baseUsername)->first();
                            if ($dbUserByUsername) {
                                // Username trùng → cùng 1 người
                                $userId = $dbUserByUsername->id;
                                $existingUser = $dbUserByUsername;
                                $userMapByEmail[$email] = $userId;
                                $userMapByUsername[$baseUsername] = $userId;
                            }
                        }
                    }
                }
                
                if (!$existingUser) {
                    // Tạo user mới
                    $username = $generateUniqueUsername($baseUsername, $usedUsernames);
                    
                    $userId = DB::table('users')->insertGetId([
                        'name' => $referee->name,
                        'email' => $referee->email,
                        'username' => $username,
                        'password' => Hash::make($username),
                        'position' => $referee->position ?? 'Trọng tài',
                        'phone' => $referee->phone,
                        'role' => 'referee',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    
                    $userMapByEmail[$email] = $userId;
                    $userMapByUsername[$username] = $userId;
                    
                    // Thêm role referee
                    if ($refereeRoleId) {
                        DB::table('users_roles')->insert([
                            'user_id' => $userId,
                            'role_id' => $refereeRoleId,
                        ]);
                    }
                } else {
                    // User đã tồn tại (có thể từ trainer), đảm bảo có role referee
                    if ($refereeRoleId) {
                        $hasRefereeRole = DB::table('users_roles')
                            ->where('user_id', $userId)
                            ->where('role_id', $refereeRoleId)
                            ->exists();
                        
                        if (!$hasRefereeRole) {
                            DB::table('users_roles')->insert([
                                'user_id' => $userId,
                                'role_id' => $refereeRoleId,
                            ]);
                        }
                    }
                    
                    // Nếu user có cả trainer và referee, không cần update role column (giữ nguyên)
                }
                
                // Link referee với user
                DB::table('referee_info')
                    ->where('id', $referee->referee_id)
                    ->update(['user_id' => $userId]);
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
        // User can manually revert if needed
    }
};

