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
        if (!Schema::hasTable('student_results')) {
            Schema::create('student_results', function (Blueprint $table) {
                $table->id();
                $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
                $table->foreignId('assessment_id')->constrained()->onDelete('cascade');
                $table->integer('score')->default(0);
                $table->integer('total_questions')->default(0);
                $table->integer('time_taken')->default(0); // in seconds
                $table->json('answers')->nullable(); // Store student answers
                $table->timestamp('submitted_at');
                $table->timestamps();
                
                // Indexes for better performance
                $table->index('student_id');
                $table->index('assessment_id');
                $table->index('submitted_at');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_results');
    }
};