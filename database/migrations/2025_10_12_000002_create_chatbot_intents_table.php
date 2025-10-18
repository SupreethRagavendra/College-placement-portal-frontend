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
        Schema::create('chatbot_intents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->string('intent_type'); // assessment_query, score_check, help_request, etc
            $table->integer('frequency')->default(1);
            $table->json('related_topics')->nullable();
            $table->timestamp('last_queried')->nullable();
            $table->timestamps();
            
            $table->unique(['student_id', 'intent_type']);
            $table->index(['student_id', 'frequency']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chatbot_intents');
    }
};
