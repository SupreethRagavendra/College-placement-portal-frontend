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
            // Add timestamps if they don't exist
            if (!Schema::hasColumn('questions', 'created_at')) {
                $table->timestamp('created_at')->nullable();
            }
            if (!Schema::hasColumn('questions', 'updated_at')) {
                $table->timestamp('updated_at')->nullable();
            }
        });
        
        // Set default values for existing records
        \DB::table('questions')->whereNull('created_at')->update(['created_at' => now()]);
        \DB::table('questions')->whereNull('updated_at')->update(['updated_at' => now()]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            if (Schema::hasColumn('questions', 'created_at')) {
                $table->dropColumn('created_at');
            }
            if (Schema::hasColumn('questions', 'updated_at')) {
                $table->dropColumn('updated_at');
            }
        });
    }
};
