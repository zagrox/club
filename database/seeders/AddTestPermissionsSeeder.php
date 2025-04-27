<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AddTestPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            // User management
            [
                'name' => 'View Users',
                'slug' => 'users.view',
                'description' => 'Can view users',
            ],
            [
                'name' => 'Create Users',
                'slug' => 'users.create',
                'description' => 'Can create users',
            ],
            [
                'name' => 'Edit Users',
                'slug' => 'users.edit',
                'description' => 'Can edit users',
            ],
            [
                'name' => 'Delete Users',
                'slug' => 'users.delete',
                'description' => 'Can delete users',
            ],
            
            // Role management
            [
                'name' => 'View Roles',
                'slug' => 'roles.view',
                'description' => 'Can view roles',
            ],
            [
                'name' => 'Create Roles',
                'slug' => 'roles.create',
                'description' => 'Can create roles',
            ],
            [
                'name' => 'Edit Roles',
                'slug' => 'roles.edit', 
                'description' => 'Can edit roles',
            ],
            [
                'name' => 'Delete Roles',
                'slug' => 'roles.delete',
                'description' => 'Can delete roles',
            ],
            
            // Notification management
            [
                'name' => 'View Notifications',
                'slug' => 'notifications.view',
                'description' => 'Can view notifications',
            ],
            [
                'name' => 'Create Notifications',
                'slug' => 'notifications.create',
                'description' => 'Can create notifications',
            ],
            [
                'name' => 'Edit Notifications',
                'slug' => 'notifications.edit',
                'description' => 'Can edit notifications',
            ],
            [
                'name' => 'Delete Notifications',
                'slug' => 'notifications.delete',
                'description' => 'Can delete notifications',
            ],
        ];
        
        foreach ($permissions as $permission) {
            DB::table('permissions')->insertOrIgnore($permission);
        }
    }
} 