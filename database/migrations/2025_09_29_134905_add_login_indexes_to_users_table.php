<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Add index for login queries (email and password verification)
            if (!Schema::hasIndex('users', 'users_email_password_index')) {
                $table->index(['email', 'password'], 'users_email_password_index');
            }
            
            // Add index for role-based login checks
            if (!Schema::hasIndex('users', 'users_role_login_index')) {
                $table->index(['role', 'is_approved', 'is_verified'], 'users_role_login_index');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasIndex('users', 'users_email_password_index')) {
                $table->dropIndex('users_email_password_index');
            }
            
            if (Schema::hasIndex('users', 'users_role_login_index')) {
                $table->dropIndex('users_role_login_index');
            }
        });
    }
};