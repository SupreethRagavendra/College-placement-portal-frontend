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
        if (!Schema::hasTable('student_answers')) {
            Schema::create('student_answers', function (Blueprint $table) {
                $table->id();
                $table->foreignId('student_assessment_id')->constrained('student_assessments')->onDelete('cascade');
                $table->foreignId('question_id')->constrained('questions')->onDelete('cascade');
                $table->text('student_answer')->nullable();
                $table->boolean('is_correct')->default(false);
                $table->integer('marks_obtained')->default(0);
                $table->integer('time_spent')->default(0); // in seconds
                $table->timestamps();
                
                // Indexes for performance
                $table->index('student_assessment_id');
                $table->index('question_id');
                $table->index(['student_assessment_id', 'question_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_answers');
    }
};
