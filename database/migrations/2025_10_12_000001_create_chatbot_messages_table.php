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
        Schema::create('chatbot_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained('chatbot_conversations')->onDelete('cascade');
            $table->string('sender', 20)->default('student'); // Changed from enum for PostgreSQL
            $table->text('message');
            $table->text('formatted_message')->nullable(); // Rich formatted version
            $table->json('context_used')->nullable(); // What context was used for this response
            $table->json('knowledge_retrieved')->nullable(); // What knowledge was retrieved
            $table->string('model_used')->nullable();
            $table->string('intent')->nullable(); // Detected intent
            $table->json('entities')->nullable(); // Extracted entities
            $table->integer('confidence_score')->nullable(); // Response confidence (0-100)
            $table->boolean('is_helpful')->nullable(); // User feedback
            $table->string('reaction')->nullable(); // User reaction emoji
            $table->timestamps();
            
            $table->index(['conversation_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chatbot_messages');
    }
};
