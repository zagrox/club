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
        // Check if the permission_role table exists in the database
        $tableExists = DB::select("SHOW TABLES LIKE 'permission_role'");
        
        if (!empty($tableExists)) {
            Schema::dropIfExists('permission_role');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This is a cleanup migration, no need for reverse operation
    }
};
