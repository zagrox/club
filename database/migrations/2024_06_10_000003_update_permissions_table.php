<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdatePermissionsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Make slug nullable in permissions table
        if (Schema::hasTable('permissions')) {
            if (Schema::hasColumn('permissions', 'slug')) {
                Schema::table('permissions', function (Blueprint $table) {
                    $table->string('slug')->nullable()->change();
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // We won't implement the down migration
    }
} 