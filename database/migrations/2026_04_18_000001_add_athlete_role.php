<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('roles')) {
            $exists = DB::table('roles')->where('slug', 'athlete')->first();
            if (!$exists) {
                DB::table('roles')->insert([
                    'name' => 'Vận động viên',
                    'slug' => 'athlete',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('roles')) {
            DB::table('roles')->where('slug', 'athlete')->delete();
        }
    }
};
