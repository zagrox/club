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
        // Create permissions table if it doesn't exist
        if (!Schema::hasTable('permissions')) {
            Schema::create('permissions', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->text('description')->nullable();
                $table->timestamps();
                $table->softDeletes();
            });
        } else {
            // Add slug column if it doesn't exist
            if (!Schema::hasColumn('permissions', 'slug')) {
                Schema::table('permissions', function (Blueprint $table) {
                    $table->string('slug')->nullable()->after('name');
                });
                
                // Initialize slug values from name for existing records
                DB::statement("UPDATE permissions SET slug = REPLACE(LOWER(name), ' ', '-') WHERE slug IS NULL");
                
                // Make slug unique after populating it
                Schema::table('permissions', function (Blueprint $table) {
                    $table->string('slug')->unique()->change();
                });
            }
            
            // Add soft deletes if it doesn't exist
            if (!Schema::hasColumn('permissions', 'deleted_at')) {
                Schema::table('permissions', function (Blueprint $table) {
                    $table->softDeletes();
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // We won't do anything in down() to prevent data loss
    }
};
