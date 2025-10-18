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
        Schema::table('questions', function (Blueprint $table) {
            // Add missing columns if they don't exist
            if (!Schema::hasColumn('questions', 'question_type')) {
                $table->string('question_type', 20)->default('mcq'); // Changed from enum for PostgreSQL
            }
            
            if (!Schema::hasColumn('questions', 'marks')) {
                $table->integer('marks')->default(1);
            }
            
            if (!Schema::hasColumn('questions', 'difficulty_level')) {
                $table->string('difficulty_level', 20)->default('medium'); // Changed from enum for PostgreSQL
            }
            
            if (!Schema::hasColumn('questions', 'order')) {
                $table->integer('order')->default(0);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->dropColumn([
                'question_type',
                'marks',
                'difficulty_level',
                'order'
            ]);
        });
    }
};