<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, check if the column exists and what type it is
        if (Schema::hasColumn('assessments', 'category')) {
            // Get current column type
            $columnType = DB::select("SELECT data_type FROM information_schema.columns WHERE table_name = 'assessments' AND column_name = 'category'")[0]->data_type ?? null;
            
            // If it's a USER-DEFINED type (enum), we need to drop and recreate it
            if ($columnType === 'USER-DEFINED') {
                Schema::table('assessments', function (Blueprint $table) {
                    $table->dropColumn('category');
                });
                
                Schema::table('assessments', function (Blueprint $table) {
                    $table->string('category', 100)->default('Technical')->after('description');
                });
            }
        } else {
            // If column doesn't exist, create it
            Schema::table('assessments', function (Blueprint $table) {
                $table->string('category', 100)->default('Technical')->after('description');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Convert back to enum if needed
        Schema::table('assessments', function (Blueprint $table) {
            $table->dropColumn('category');
        });
        
        Schema::table('assessments', function (Blueprint $table) {
            $table->enum('category', ['Aptitude', 'Technical'])->default('Technical')->after('description');
        });
    }
};
