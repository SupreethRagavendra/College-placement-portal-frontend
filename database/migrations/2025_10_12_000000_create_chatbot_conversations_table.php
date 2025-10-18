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
        Schema::create('chatbot_conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->string('session_id')->index();
            $table->json('messages')->nullable(); // Store conversation messages (last 10)
            $table->string('title')->nullable();
            $table->string('topic')->nullable(); // Main topic of conversation
            $table->json('context')->nullable(); // Conversation context/memory
            $table->json('metadata')->nullable(); // Additional metadata
            $table->string('status', 20)->default('active'); // Changed from enum for PostgreSQL
            $table->timestamp('last_activity')->nullable();
            $table->timestamp('last_message_at')->nullable(); // Track last message time
            $table->timestamps();
            
            $table->index(['student_id', 'status']);
            $table->index(['student_id', 'created_at']);
            $table->index(['student_id', 'session_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chatbot_conversations');
    }
};
