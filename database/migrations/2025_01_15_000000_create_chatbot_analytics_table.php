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
        Schema::create('chatbot_analytics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->text('query');
            $table->string('query_type', 50)->nullable();
            $table->text('response')->nullable();
            $table->string('model_used', 100)->nullable();
            $table->integer('response_time_ms')->nullable();
            $table->integer('tokens_used')->nullable();
            $table->boolean('from_cache')->default(false);
            $table->boolean('failed')->default(false);
            $table->string('error_message')->nullable();
            $table->timestamps();
            
            $table->index(['student_id', 'created_at']);
            $table->index('query_type');
            $table->index('from_cache');
            $table->index('failed');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chatbot_analytics');
    }
};

