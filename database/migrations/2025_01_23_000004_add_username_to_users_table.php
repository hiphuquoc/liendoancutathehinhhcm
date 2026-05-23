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
        if (Schema::hasTable('users')) {
            if (!Schema::hasColumn('users', 'username')) {
                Schema::table('users', function (Blueprint $table) {
                    $table->string('username')->nullable()->after('email');
                });
                
                // Copy email to username for all existing users
                DB::table('users')->update([
                    'username' => DB::raw('email')
                ]);
                
                // Make username unique after copying
                Schema::table('users', function (Blueprint $table) {
                    $table->unique('username');
                });
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
        if (Schema::hasTable('users')) {
            if (Schema::hasColumn('users', 'username')) {
                Schema::table('users', function (Blueprint $table) {
                    $table->dropUnique(['username']);
                    $table->dropColumn('username');
                });
            }
        }
    }
};

