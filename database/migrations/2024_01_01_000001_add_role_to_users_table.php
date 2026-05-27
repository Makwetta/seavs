<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// FILE NAME: database/migrations/2024_01_01_000001_add_role_to_users_table.php

return new class extends Migration
{
    public function up(): void
    {
        // Laravel Breeze already creates the users table.
        // We just add the 'role' column to it.
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'supervisor'])
                  ->default('supervisor')
                  ->after('email');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });
    }
};
