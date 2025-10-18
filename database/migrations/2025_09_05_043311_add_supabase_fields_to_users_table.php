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
            $table->string('supabase_id')->nullable()->unique()->after('id');
            $table->timestamp('admin_approved_at')->nullable()->after('email_verified_at');
            $table->timestamp('admin_rejected_at')->nullable()->after('admin_approved_at');
            $table->string('status')->default('pending')->after('role'); // pending, approved, rejected
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['supabase_id', 'admin_approved_at', 'admin_rejected_at', 'status']);
        });
    }
};
