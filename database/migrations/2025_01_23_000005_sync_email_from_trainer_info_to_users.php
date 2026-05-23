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
        // Update email in users table from trainer_info.email where user_id matches
        if (Schema::hasTable('users') && Schema::hasTable('trainer_info')) {
            $trainers = DB::table('trainer_info')
                ->whereNotNull('user_id')
                ->whereNotNull('email')
                ->select('user_id', 'email')
                ->get();
            
            foreach ($trainers as $trainer) {
                // Get current user email
                $user = DB::table('users')->where('id', $trainer->user_id)->first();
                
                if ($user) {
                    // Only update if email is different and the new email doesn't already exist for another user
                    if ($user->email !== $trainer->email) {
                        // Check if the new email already exists for another user
                        $existingUser = DB::table('users')
                            ->where('email', $trainer->email)
                            ->where('id', '!=', $trainer->user_id)
                            ->first();
                        
                        // Only update if email doesn't exist for another user
                        if (!$existingUser) {
                            DB::table('users')
                                ->where('id', $trainer->user_id)
                                ->update(['email' => $trainer->email]);
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
        // This migration is data migration, no need to reverse
    }
};

