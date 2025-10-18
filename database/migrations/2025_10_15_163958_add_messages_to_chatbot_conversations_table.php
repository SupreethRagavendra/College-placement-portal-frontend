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
        Schema::table('chatbot_conversations', function (Blueprint $table) {
            if (!Schema::hasColumn('chatbot_conversations', 'messages')) {
                $table->json('messages')->nullable()->after('session_id');
            }
            if (!Schema::hasColumn('chatbot_conversations', 'last_message_at')) {
                $table->timestamp('last_message_at')->nullable()->after('last_activity');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('chatbot_conversations', function (Blueprint $table) {
            $table->dropColumn(['messages', 'last_message_at']);
        });
    }
};
