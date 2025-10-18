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
        // Create categories table if it doesn't exist
        if (!Schema::hasTable('categories')) {
            Schema::create('categories', function (Blueprint $table) {
                $table->id();
                $table->string('name')->unique();
                $table->text('description')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // Seed initial categories if empty
        if (DB::table('categories')->count() === 0) {
            DB::table('categories')->insert([
                ['name' => 'Aptitude', 'description' => 'Logical reasoning and mathematical skills', 'is_active' => true],
                ['name' => 'Technical', 'description' => 'Technical knowledge and skills', 'is_active' => true]
            ]);
        }

        // Add category_id column to questions table if it doesn't exist
        if (!Schema::hasColumn('questions', 'category_id')) {
            Schema::table('questions', function (Blueprint $table) {
                $table->unsignedBigInteger('category_id')->nullable();
                
                // Add foreign key constraint
                $table->foreign('category_id')
                    ->references('id')
                    ->on('categories')
                    ->onDelete('restrict');
            });
            
            // Set category_id for existing questions based on category column
            $categories = DB::table('categories')->pluck('id', 'name')->toArray();
            
            // Update questions with Aptitude category
            if (isset($categories['Aptitude'])) {
                DB::table('questions')
                    ->where('category', 'Aptitude')
                    ->update(['category_id' => $categories['Aptitude']]);
            }
            
            // Update questions with Technical category
            if (isset($categories['Technical'])) {
                DB::table('questions')
                    ->where('category', 'Technical')
                    ->update(['category_id' => $categories['Technical']]);
            }
            
            // Set default category_id for any remaining questions
            $defaultCategoryId = $categories['Aptitude'] ?? DB::table('categories')->first()->id;
            DB::table('questions')
                ->whereNull('category_id')
                ->update(['category_id' => $defaultCategoryId]);
                
            // Make category_id not nullable
            Schema::table('questions', function (Blueprint $table) {
                $table->unsignedBigInteger('category_id')->nullable(false)->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropColumn('category_id');
        });

        Schema::dropIfExists('categories');
    }
};