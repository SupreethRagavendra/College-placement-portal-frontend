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
        // Add indexes to assessments table
        Schema::table('assessments', function (Blueprint $table) {
            $table->index(['status', 'is_active'], 'idx_assessments_status_active');
            $table->index(['category'], 'idx_assessments_category');
            $table->index(['created_by'], 'idx_assessments_created_by');
            $table->index(['start_date', 'end_date'], 'idx_assessments_dates');
            $table->index(['created_at'], 'idx_assessments_created_at');
        });

        // Add indexes to student_results table
        Schema::table('student_results', function (Blueprint $table) {
            $table->index(['student_id', 'assessment_id'], 'idx_student_results_student_assessment');
            $table->index(['student_id'], 'idx_student_results_student');
            $table->index(['assessment_id'], 'idx_student_results_assessment');
            $table->index(['submitted_at'], 'idx_student_results_submitted_at');
        });

        // Add indexes to student_assessments table if it exists
        if (Schema::hasTable('student_assessments')) {
            Schema::table('student_assessments', function (Blueprint $table) {
                $table->index(['student_id', 'assessment_id'], 'idx_student_assessments_student_assessment');
                $table->index(['status'], 'idx_student_assessments_status');
                $table->index(['start_time'], 'idx_student_assessments_start_time');
            });
        }

        // Add indexes to questions table
        Schema::table('questions', function (Blueprint $table) {
            $table->index(['category'], 'idx_questions_category');
            $table->index(['is_active'], 'idx_questions_active');
            $table->index(['difficulty'], 'idx_questions_difficulty');
        });

        // Add indexes to assessment_questions pivot table
        Schema::table('assessment_questions', function (Blueprint $table) {
            $table->index(['assessment_id'], 'idx_assessment_questions_assessment');
            $table->index(['question_id'], 'idx_assessment_questions_question');
        });

        // Add indexes to users table for better performance
        Schema::table('users', function (Blueprint $table) {
            $table->index(['role', 'status'], 'idx_users_role_status');
            $table->index(['created_at'], 'idx_users_created_at');
        });

        // Add indexes for chatbot tables if they exist
        if (Schema::hasTable('chatbot_conversations')) {
            Schema::table('chatbot_conversations', function (Blueprint $table) {
                $table->index(['student_id', 'status'], 'idx_chatbot_conversations_student_status');
                $table->index(['created_at'], 'idx_chatbot_conversations_created_at');
            });
        }

        if (Schema::hasTable('chatbot_messages')) {
            Schema::table('chatbot_messages', function (Blueprint $table) {
                $table->index(['conversation_id', 'created_at'], 'idx_chatbot_messages_conversation_time');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assessments', function (Blueprint $table) {
            $table->dropIndex('idx_assessments_status_active');
            $table->dropIndex('idx_assessments_category');
            $table->dropIndex('idx_assessments_created_by');
            $table->dropIndex('idx_assessments_dates');
            $table->dropIndex('idx_assessments_created_at');
        });

        Schema::table('student_results', function (Blueprint $table) {
            $table->dropIndex('idx_student_results_student_assessment');
            $table->dropIndex('idx_student_results_student');
            $table->dropIndex('idx_student_results_assessment');
            $table->dropIndex('idx_student_results_submitted_at');
        });

        if (Schema::hasTable('student_assessments')) {
            Schema::table('student_assessments', function (Blueprint $table) {
                $table->dropIndex('idx_student_assessments_student_assessment');
                $table->dropIndex('idx_student_assessments_status');
                $table->dropIndex('idx_student_assessments_start_time');
            });
        }

        Schema::table('questions', function (Blueprint $table) {
            $table->dropIndex('idx_questions_category');
            $table->dropIndex('idx_questions_active');
            $table->dropIndex('idx_questions_difficulty');
        });

        Schema::table('assessment_questions', function (Blueprint $table) {
            $table->dropIndex('idx_assessment_questions_assessment');
            $table->dropIndex('idx_assessment_questions_question');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('idx_users_role_status');
            $table->dropIndex('idx_users_created_at');
        });

        if (Schema::hasTable('chatbot_conversations')) {
            Schema::table('chatbot_conversations', function (Blueprint $table) {
                $table->dropIndex('idx_chatbot_conversations_student_status');
                $table->dropIndex('idx_chatbot_conversations_created_at');
            });
        }

        if (Schema::hasTable('chatbot_messages')) {
            Schema::table('chatbot_messages', function (Blueprint $table) {
                $table->dropIndex('idx_chatbot_messages_conversation_time');
            });
        }
    }
};
