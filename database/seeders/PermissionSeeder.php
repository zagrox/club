<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            // Users
            ['name' => 'View Users', 'slug' => 'users.view', 'description' => 'Can view list of users'],
            ['name' => 'Create Users', 'slug' => 'users.create', 'description' => 'Can create new users'],
            ['name' => 'Edit Users', 'slug' => 'users.edit', 'description' => 'Can edit existing users'],
            ['name' => 'Delete Users', 'slug' => 'users.delete', 'description' => 'Can delete users'],
            
            // Roles
            ['name' => 'View Roles', 'slug' => 'roles.view', 'description' => 'Can view list of roles'],
            ['name' => 'Create Roles', 'slug' => 'roles.create', 'description' => 'Can create new roles'],
            ['name' => 'Edit Roles', 'slug' => 'roles.edit', 'description' => 'Can edit existing roles'],
            ['name' => 'Delete Roles', 'slug' => 'roles.delete', 'description' => 'Can delete roles'],
            
            // Permissions
            ['name' => 'Manage Permissions', 'slug' => 'permissions.manage', 'description' => 'Can manage permissions'],
            
            // Orders
            ['name' => 'View Orders', 'slug' => 'orders.view', 'description' => 'Can view list of orders'],
            ['name' => 'Create Orders', 'slug' => 'orders.create', 'description' => 'Can create new orders'],
            ['name' => 'Edit Orders', 'slug' => 'orders.edit', 'description' => 'Can edit existing orders'],
            ['name' => 'Delete Orders', 'slug' => 'orders.delete', 'description' => 'Can delete orders'],
            
            // Notifications
            ['name' => 'View Notifications', 'slug' => 'notifications.view', 'description' => 'Can view notifications'],
            ['name' => 'Create Notifications', 'slug' => 'notifications.create', 'description' => 'Can create new notifications'],
            ['name' => 'Manage Notifications', 'slug' => 'notifications.manage', 'description' => 'Can manage notifications'],
            
            // Backup
            ['name' => 'View Backups', 'slug' => 'backups.view', 'description' => 'Can view backups'],
            ['name' => 'Create Backups', 'slug' => 'backups.create', 'description' => 'Can create new backups'],
            ['name' => 'Download Backups', 'slug' => 'backups.download', 'description' => 'Can download backups'],
            ['name' => 'Delete Backups', 'slug' => 'backups.delete', 'description' => 'Can delete backups'],
        ];

        foreach ($permissions as $permission) {
            $this->createPermission($permission['name'], $permission['slug'], $permission['description']);
        }
    }

    /**
     * Create a permission if it doesn't exist.
     */
    private function createPermission(string $name, string $slug, string $description = null): void
    {
        Permission::firstOrCreate(
            ['slug' => $slug],
            [
                'name' => $name,
                'description' => $description,
            ]
        );
    }
} 