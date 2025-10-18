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
        Schema::create('student_performance_analytics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('assessment_id')->nullable()->constrained('assessments')->onDelete('set null');
            $table->string('category'); // Aptitude, Technical, etc
            $table->string('topic')->nullable(); // Specific topic
            $table->string('difficulty_level', 20)->default('Medium'); // Changed from enum for PostgreSQL
            
            // Performance Metrics
            $table->integer('total_questions')->default(0);
            $table->integer('correct_answers')->default(0);
            $table->integer('incorrect_answers')->default(0);
            $table->decimal('accuracy_percentage', 5, 2)->default(0);
            
            // Time Management Metrics
            $table->integer('avg_time_per_question')->nullable(); // in seconds
            $table->integer('questions_too_slow')->default(0); // Questions taking > avg time
            $table->integer('questions_too_fast')->default(0); // Questions taking < 30% avg time
            $table->integer('questions_skipped')->default(0);
            
            // Mistake Patterns
            $table->json('common_mistakes')->nullable(); // Array of mistake patterns
            $table->json('weak_topics')->nullable(); // Topics with < 50% accuracy
            $table->json('strong_topics')->nullable(); // Topics with > 80% accuracy
            
            // Progress Tracking
            $table->decimal('improvement_rate', 5, 2)->nullable(); // % improvement over time
            $table->decimal('consistency_score', 5, 2)->nullable(); // How consistent performance is
            $table->integer('streak_correct')->default(0); // Current correct answer streak
            $table->integer('max_streak')->default(0); // Best streak achieved
            
            // Learning Velocity
            $table->decimal('learning_pace', 5, 2)->nullable(); // Questions per hour average
            $table->integer('study_time_minutes')->default(0); // Total time spent
            $table->date('last_activity_date')->nullable();
            
            $table->timestamps();
            
            $table->unique(['student_id', 'assessment_id', 'category', 'difficulty_level']);
            $table->index(['student_id', 'category']);
            $table->index(['student_id', 'accuracy_percentage']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_performance_analytics');
    }
};
