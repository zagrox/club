<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin Role
        $adminRole = $this->createRole(
            'Admin',
            'admin',
            'Full system administrator with all permissions',
            true
        );

        // Get all permissions and assign to admin
        $allPermissions = Permission::all();
        if ($allPermissions->isNotEmpty()) {
            $adminRole->givePermissionTo($allPermissions);
        }

        // Editor Role
        $editorRole = $this->createRole(
            'Editor',
            'editor',
            'Can manage content and some user operations'
        );

        // Assign editor permissions
        $editorPermissions = Permission::whereIn('slug', [
            'users.view',
            'notifications.view',
            'notifications.create',
            'notifications.edit',
            'notifications.send',
        ])->get();
        
        if ($editorPermissions->isNotEmpty()) {
            $editorRole->givePermissionTo($editorPermissions);
        }

        // User Role
        $userRole = $this->createRole(
            'User',
            'user',
            'Regular user with limited permissions'
        );

        // Assign user permissions
        $userPermissions = Permission::whereIn('slug', [
            'notifications.view',
        ])->get();
        
        if ($userPermissions->isNotEmpty()) {
            $userRole->givePermissionTo($userPermissions);
        }
    }

    /**
     * Create a role if it doesn't exist.
     */
    private function createRole(string $name, string $slug, string $description = null, bool $isDefault = false): Role
    {
        return Role::firstOrCreate(
            ['slug' => $slug],
            [
                'name' => $name,
                'description' => $description,
                'is_default' => $isDefault,
            ]
        );
    }
} 