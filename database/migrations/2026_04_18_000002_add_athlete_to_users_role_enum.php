<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('users') || ! Schema::hasColumn('users', 'role')) {
            return;
        }
        try {
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('user', 'admin', 'sub-admin', 'trainer', 'referee', 'athlete') DEFAULT 'user'");
        } catch (\Throwable $e) {
            // Enum may already include athlete or column type differs
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('users') || ! Schema::hasColumn('users', 'role')) {
            return;
        }
        try {
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('user', 'admin', 'sub-admin', 'trainer', 'referee') DEFAULT 'user'");
        } catch (\Throwable $e) {
        }
    }
};
