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
        // Add composite indexes for better query performance
        Schema::table('users', function (Blueprint $table) {
            $table->index(['role', 'is_approved'], 'users_role_approved_index');
            $table->index(['role', 'status'], 'users_role_status_index');
            $table->index(['role', 'email_verified_at', 'is_approved', 'admin_rejected_at'], 'users_student_filter_index');
        });

        Schema::table('assessments', function (Blueprint $table) {
            $table->index(['is_active', 'category'], 'assessments_active_category_index');
        });

        Schema::table('student_results', function (Blueprint $table) {
            $table->index(['student_id', 'submitted_at'], 'student_results_student_submitted_index');
            $table->index(['assessment_id', 'submitted_at'], 'student_results_assessment_submitted_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('users_role_approved_index');
            $table->dropIndex('users_role_status_index');
            $table->dropIndex('users_student_filter_index');
        });

        Schema::table('assessments', function (Blueprint $table) {
            $table->dropIndex('assessments_active_category_index');
        });

        Schema::table('student_results', function (Blueprint $table) {
            $table->dropIndex('student_results_student_submitted_index');
            $table->dropIndex('student_results_assessment_submitted_index');
        });
    }
};