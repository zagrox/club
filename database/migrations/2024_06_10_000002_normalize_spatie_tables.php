<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class NormalizeSpatieTables extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First fix roles table
        if (Schema::hasTable('roles')) {
            // Check if slug column exists
            if (Schema::hasColumn('roles', 'slug')) {
                // Make slug nullable so we can add new roles without specifying it
                Schema::table('roles', function (Blueprint $table) {
                    $table->string('slug')->nullable()->change();
                });
            }
        }
        
        // Now add description field to permissions table if it doesn't exist
        if (Schema::hasTable('permissions') && !Schema::hasColumn('permissions', 'description')) {
            Schema::table('permissions', function (Blueprint $table) {
                $table->string('description')->nullable()->after('guard_name');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // We won't implement a down migration
    }
} 