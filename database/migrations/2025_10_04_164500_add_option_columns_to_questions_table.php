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
            // Add individual option columns if they don't exist
            if (!Schema::hasColumn('questions', 'option_a')) {
                $table->text('option_a')->nullable()->after('question_text');
            }
            if (!Schema::hasColumn('questions', 'option_b')) {
                $table->text('option_b')->nullable()->after('option_a');
            }
            if (!Schema::hasColumn('questions', 'option_c')) {
                $table->text('option_c')->nullable()->after('option_b');
            }
            if (!Schema::hasColumn('questions', 'option_d')) {
                $table->text('option_d')->nullable()->after('option_c');
            }
            
            // Add 'question' column as an alias for question_text
            if (!Schema::hasColumn('questions', 'question')) {
                $table->text('question')->nullable()->after('id');
            }
            
            // Add explanation column
            if (!Schema::hasColumn('questions', 'explanation')) {
                $table->text('explanation')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->dropColumn(['option_a', 'option_b', 'option_c', 'option_d', 'question', 'explanation']);
        });
    }
};
