<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Default roles with permissions
        $roles = [
            [
                'name' => 'Administrator',
                'slug' => 'admin',
                'description' => 'Full access to all system features',
                'permissions' => [
                    'users.view', 'users.create', 'users.edit', 'users.delete',
                    'roles.view', 'roles.create', 'roles.edit', 'roles.delete',
                    'settings.access', 'notifications.manage', 'logs.view'
                ],
                'is_system' => true,
            ],
            [
                'name' => 'Moderator',
                'slug' => 'moderator',
                'description' => 'Manage content and users with limited access',
                'permissions' => [
                    'users.view', 'users.edit',
                    'roles.view',
                    'notifications.manage'
                ],
                'is_system' => true,
            ],
            [
                'name' => 'User',
                'slug' => 'user',
                'description' => 'Standard user access',
                'permissions' => [],
                'is_system' => true,
            ],
            [
                'name' => 'Guest',
                'slug' => 'guest',
                'description' => 'Limited access for guests',
                'permissions' => [],
                'is_system' => true,
            ],
        ];

        foreach ($roles as $roleData) {
            Role::updateOrCreate(
                ['slug' => $roleData['slug']],
                $roleData
            );
        }
    }
} 