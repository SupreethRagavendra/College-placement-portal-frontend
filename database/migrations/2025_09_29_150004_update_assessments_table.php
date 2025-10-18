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
        Schema::table('assessments', function (Blueprint $table) {
            // Add missing columns if they don't exist
            if (!Schema::hasColumn('assessments', 'duration')) {
                $table->integer('duration')->default(30);
            }
            
            if (!Schema::hasColumn('assessments', 'total_marks')) {
                $table->integer('total_marks')->default(100);
            }
            
            if (!Schema::hasColumn('assessments', 'pass_percentage')) {
                $table->integer('pass_percentage')->default(50);
            }
            
            if (!Schema::hasColumn('assessments', 'start_date')) {
                $table->dateTime('start_date')->nullable();
            }
            
            if (!Schema::hasColumn('assessments', 'end_date')) {
                $table->dateTime('end_date')->nullable();
            }
            
            if (!Schema::hasColumn('assessments', 'status')) {
                $table->string('status', 20)->default('draft');
            }
            
            if (!Schema::hasColumn('assessments', 'category')) {
                $table->string('category')->default('General');
            }
            
            if (!Schema::hasColumn('assessments', 'difficulty_level')) {
                $table->string('difficulty_level', 20)->default('medium');
            }
            
            if (!Schema::hasColumn('assessments', 'created_by')) {
                $table->unsignedBigInteger('created_by')->default(1);
                $table->foreign('created_by')->references('id')->on('users');
            }
            
            if (!Schema::hasColumn('assessments', 'allow_multiple_attempts')) {
                $table->boolean('allow_multiple_attempts')->default(false);
            }
            
            if (!Schema::hasColumn('assessments', 'show_results_immediately')) {
                $table->boolean('show_results_immediately')->default(true);
            }
            
            if (!Schema::hasColumn('assessments', 'show_correct_answers')) {
                $table->boolean('show_correct_answers')->default(true);
            }
            
            if (!Schema::hasColumn('assessments', 'deleted_at')) {
                $table->softDeletes();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assessments', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropColumn([
                'duration',
                'total_marks',
                'pass_percentage',
                'start_date',
                'end_date',
                'status',
                'category',
                'difficulty_level',
                'created_by',
                'allow_multiple_attempts',
                'show_results_immediately',
                'show_correct_answers',
                'deleted_at'
            ]);
        });
    }
};