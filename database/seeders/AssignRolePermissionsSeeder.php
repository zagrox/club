<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AssignRolePermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get role IDs
        $adminRole = DB::table('roles')->where('slug', 'admin')->first();
        $managerRole = DB::table('roles')->where('slug', 'manager')->first();
        $memberRole = DB::table('roles')->where('slug', 'member')->first();
        
        if (!$adminRole || !$managerRole || !$memberRole) {
            $this->command->info('Roles not found. Please run the AddTestRolesSeeder first.');
            return;
        }
        
        // Get permission IDs
        $permissions = DB::table('permissions')->get();
        
        if ($permissions->isEmpty()) {
            $this->command->info('Permissions not found. Please run the AddTestPermissionsSeeder first.');
            return;
        }
        
        // Admin gets all permissions
        foreach ($permissions as $permission) {
            DB::table('permission_role')->insertOrIgnore([
                'permission_id' => $permission->id,
                'role_id' => $adminRole->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        
        // Manager gets user and notification permissions
        $managerPermissions = [
            'users.view',
            'users.create',
            'users.edit',
            'notifications.view',
            'notifications.create',
            'notifications.edit',
            'notifications.delete',
        ];
        
        foreach ($managerPermissions as $permSlug) {
            $permission = $permissions->where('slug', $permSlug)->first();
            if ($permission) {
                DB::table('permission_role')->insertOrIgnore([
                    'permission_id' => $permission->id,
                    'role_id' => $managerRole->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
        
        // Member just gets view notifications
        $memberPermissions = [
            'notifications.view',
        ];
        
        foreach ($memberPermissions as $permSlug) {
            $permission = $permissions->where('slug', $permSlug)->first();
            if ($permission) {
                DB::table('permission_role')->insertOrIgnore([
                    'permission_id' => $permission->id,
                    'role_id' => $memberRole->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
        
        $this->command->info('Permissions assigned to roles successfully.');
    }
}
