<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations to add performance optimization indexes
     */
    public function up(): void
    {
        // Add indexes for student_assessments table
        Schema::table('student_assessments', function (Blueprint $table) {
            // Composite index for most common queries
            $table->index(['student_id', 'assessment_id', 'status'], 'idx_student_assessment_lookup');
            
            // Index for recent assessments queries
            $table->index(['created_at', 'status'], 'idx_assessment_recent');
            
            // Index for performance queries
            $table->index(['assessment_id', 'percentage'], 'idx_assessment_performance');
            
            // Index for time-based queries
            $table->index(['start_time', 'end_time'], 'idx_assessment_time');
        });
        
        // Add indexes for student_answers table
        Schema::table('student_answers', function (Blueprint $table) {
            // Composite index for answer lookups
            $table->index(['student_assessment_id', 'question_id'], 'idx_answer_lookup');
            
            // Index for correctness queries
            $table->index(['student_assessment_id', 'is_correct'], 'idx_answer_correctness');
            
            // Index for marks calculation
            $table->index(['student_assessment_id', 'marks_obtained'], 'idx_answer_marks');
        });
        
        // Add indexes for assessments table
        Schema::table('assessments', function (Blueprint $table) {
            // Index for active assessments
            $table->index(['is_active', 'start_date', 'end_date'], 'idx_active_assessments');
            
            // Index for category-based queries
            $table->index(['category', 'is_active'], 'idx_assessment_category');
            
            // Index for difficulty queries
            $table->index(['difficulty_level', 'is_active'], 'idx_assessment_difficulty');
            
            // Index for soft deletes
            $table->index('deleted_at', 'idx_assessment_deleted');
        });
        
        // Add indexes for questions table
        Schema::table('questions', function (Blueprint $table) {
            // Index for assessment questions
            $table->index(['assessment_id', 'is_active'], 'idx_question_assessment');
            
            // Index for category queries
            $table->index('category', 'idx_question_category');
            
            // Index for difficulty queries
            $table->index(['difficulty', 'is_active'], 'idx_question_difficulty');
        });
        
        // Add indexes for users table
        Schema::table('users', function (Blueprint $table) {
            // Index for role-based queries
            $table->index(['role', 'status'], 'idx_user_role_status');
            
            // Index for email verification
            $table->index(['email_verified_at', 'status'], 'idx_user_verification');
            
            // Index for login queries (email is already indexed as unique)
            $table->index('status', 'idx_user_status');
        });
        
        // Add indexes for assessment_questions pivot table
        Schema::table('assessment_questions', function (Blueprint $table) {
            // Composite index for joins
            $table->index(['assessment_id', 'question_id'], 'idx_assessment_question_pivot');
            
            // Index for ordering
            $table->index(['assessment_id', 'order'], 'idx_assessment_question_order');
        });
        
        // Add indexes for student_results table if it exists
        if (Schema::hasTable('student_results')) {
            Schema::table('student_results', function (Blueprint $table) {
                // Similar indexes as student_assessments
                $table->index(['student_id', 'assessment_id'], 'idx_result_lookup');
                $table->index(['assessment_id', 'score'], 'idx_result_score');
                $table->index('created_at', 'idx_result_created');
            });
        }
        
        // Add indexes for categories table if it exists
        if (Schema::hasTable('categories')) {
            Schema::table('categories', function (Blueprint $table) {
                $table->index('is_active', 'idx_category_active');
                $table->index('name', 'idx_category_name');
            });
        }
        
        // Add indexes for sessions table if using database sessions
        if (Schema::hasTable('sessions')) {
            Schema::table('sessions', function (Blueprint $table) {
                $table->index('last_activity', 'idx_session_activity');
                $table->index('user_id', 'idx_session_user');
            });
        }
        
        // Add indexes for cache table if using database cache
        if (Schema::hasTable('cache')) {
            Schema::table('cache', function (Blueprint $table) {
                $table->index('expiration', 'idx_cache_expiration');
            });
        }
        
        // Add indexes for jobs table if using database queue
        if (Schema::hasTable('jobs')) {
            Schema::table('jobs', function (Blueprint $table) {
                $table->index(['queue', 'reserved_at'], 'idx_jobs_queue_reserved');
                $table->index('available_at', 'idx_jobs_available');
            });
        }
    }

    /**
     * Reverse the migrations
     */
    public function down(): void
    {
        // Drop indexes from student_assessments
        Schema::table('student_assessments', function (Blueprint $table) {
            $table->dropIndex('idx_student_assessment_lookup');
            $table->dropIndex('idx_assessment_recent');
            $table->dropIndex('idx_assessment_performance');
            $table->dropIndex('idx_assessment_time');
        });
        
        // Drop indexes from student_answers
        Schema::table('student_answers', function (Blueprint $table) {
            $table->dropIndex('idx_answer_lookup');
            $table->dropIndex('idx_answer_correctness');
            $table->dropIndex('idx_answer_marks');
        });
        
        // Drop indexes from assessments
        Schema::table('assessments', function (Blueprint $table) {
            $table->dropIndex('idx_active_assessments');
            $table->dropIndex('idx_assessment_category');
            $table->dropIndex('idx_assessment_difficulty');
            $table->dropIndex('idx_assessment_deleted');
        });
        
        // Drop indexes from questions
        Schema::table('questions', function (Blueprint $table) {
            $table->dropIndex('idx_question_assessment');
            $table->dropIndex('idx_question_category');
            $table->dropIndex('idx_question_difficulty');
        });
        
        // Drop indexes from users
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('idx_user_role_status');
            $table->dropIndex('idx_user_verification');
            $table->dropIndex('idx_user_status');
        });
        
        // Drop indexes from assessment_questions
        Schema::table('assessment_questions', function (Blueprint $table) {
            $table->dropIndex('idx_assessment_question_pivot');
            $table->dropIndex('idx_assessment_question_order');
        });
        
        // Drop indexes from other tables if they exist
        if (Schema::hasTable('student_results')) {
            Schema::table('student_results', function (Blueprint $table) {
                $table->dropIndex('idx_result_lookup');
                $table->dropIndex('idx_result_score');
                $table->dropIndex('idx_result_created');
            });
        }
        
        if (Schema::hasTable('categories')) {
            Schema::table('categories', function (Blueprint $table) {
                $table->dropIndex('idx_category_active');
                $table->dropIndex('idx_category_name');
            });
        }
        
        if (Schema::hasTable('sessions')) {
            Schema::table('sessions', function (Blueprint $table) {
                $table->dropIndex('idx_session_activity');
                $table->dropIndex('idx_session_user');
            });
        }
        
        if (Schema::hasTable('cache')) {
            Schema::table('cache', function (Blueprint $table) {
                $table->dropIndex('idx_cache_expiration');
            });
        }
        
        if (Schema::hasTable('jobs')) {
            Schema::table('jobs', function (Blueprint $table) {
                $table->dropIndex('idx_jobs_queue_reserved');
                $table->dropIndex('idx_jobs_available');
            });
        }
    }
};
