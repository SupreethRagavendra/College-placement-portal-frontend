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
        Schema::create('student_assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('assessment_id')->constrained('assessments')->onDelete('cascade');
            $table->timestamp('start_time')->nullable();
            $table->timestamp('end_time')->nullable();
            $table->timestamp('submit_time')->nullable();
            $table->string('status')->default('in_progress'); // in_progress, completed, abandoned
            $table->integer('total_marks')->default(0);
            $table->integer('obtained_marks')->default(0);
            $table->decimal('percentage', 5, 2)->default(0);
            $table->string('pass_status')->nullable(); // pass, fail
            $table->integer('time_taken')->default(0); // in seconds
            $table->timestamps();
            
            // Indexes for performance
            $table->index('student_id');
            $table->index('assessment_id');
            $table->index('status');
            $table->index(['student_id', 'assessment_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_assessments');
    }
};
