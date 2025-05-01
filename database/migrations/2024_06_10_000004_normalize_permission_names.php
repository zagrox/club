<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class NormalizePermissionNames extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update permission names to be consistent with slugs for Spatie compatibility
        if (Schema::hasTable('permissions')) {
            // First make sure all have guard_name
            DB::table('permissions')
                ->whereNull('guard_name')
                ->update(['guard_name' => 'web']);
                
            // Convert descriptive names to match slug format (if slug exists)
            $permissions = DB::table('permissions')
                ->whereNotNull('slug')
                ->get();
                
            foreach ($permissions as $permission) {
                if (isset($permission->slug) && $permission->slug != $permission->name) {
                    DB::table('permissions')
                        ->where('id', $permission->id)
                        ->update(['name' => $permission->slug]);
                }
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