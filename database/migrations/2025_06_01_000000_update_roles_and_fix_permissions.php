<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\Role;
use App\Models\User;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Find the roles to remove
        $managerRole = DB::table('roles')->where('slug', 'manager')->first();
        $memberRole = DB::table('roles')->where('slug', 'member')->first();
        
        if ($managerRole || $memberRole) {
            // Get admin role as fallback
            $adminRole = DB::table('roles')->where('slug', 'admin')->first();
            
            // If admin role doesn't exist, create it
            if (!$adminRole) {
                DB::table('roles')->insert([
                    'name' => 'Administrator',
                    'slug' => 'admin',
                    'description' => 'Full system administrator with all permissions',
                    'is_default' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $adminRole = DB::table('roles')->where('slug', 'admin')->first();
            }
            
            // Get all users with manager or member roles
            if ($managerRole) {
                $managerUsers = DB::table('role_user')
                    ->where('role_id', $managerRole->id)
                    ->pluck('user_id')
                    ->toArray();
                
                // Migrate users to admin role if they don't already have it
                foreach ($managerUsers as $userId) {
                    $hasAdminRole = DB::table('role_user')
                        ->where('user_id', $userId)
                        ->where('role_id', $adminRole->id)
                        ->exists();
                        
                    if (!$hasAdminRole) {
                        DB::table('role_user')->insert([
                            'role_id' => $adminRole->id,
                            'user_id' => $userId,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
                
                // Delete all role_user entries for manager role
                DB::table('role_user')->where('role_id', $managerRole->id)->delete();
                
                // Delete all permission_role entries for manager role
                DB::table('permission_role')->where('role_id', $managerRole->id)->delete();
                
                // Delete the manager role
                DB::table('roles')->where('id', $managerRole->id)->delete();
            }
            
            if ($memberRole) {
                $memberUsers = DB::table('role_user')
                    ->where('role_id', $memberRole->id)
                    ->pluck('user_id')
                    ->toArray();
                
                // Migrate users to admin role if they don't already have it
                foreach ($memberUsers as $userId) {
                    $hasAdminRole = DB::table('role_user')
                        ->where('user_id', $userId)
                        ->where('role_id', $adminRole->id)
                        ->exists();
                        
                    if (!$hasAdminRole) {
                        DB::table('role_user')->insert([
                            'role_id' => $adminRole->id,
                            'user_id' => $userId,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
                
                // Delete all role_user entries for member role
                DB::table('role_user')->where('role_id', $memberRole->id)->delete();
                
                // Delete all permission_role entries for member role
                DB::table('permission_role')->where('role_id', $memberRole->id)->delete();
                
                // Delete the member role
                DB::table('roles')->where('id', $memberRole->id)->delete();
            }
        }
        
        // Update any users with 'role' column set to 'manager' or 'member'
        if (Schema::hasColumn('users', 'role')) {
            DB::table('users')->where('role', 'manager')->update(['role' => 'admin']);
            DB::table('users')->where('role', 'member')->update(['role' => 'admin']);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This migration is not reversible as it deletes data
    }
}; 