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
        // User Management Permissions
        $this->createPermission('View Users', 'users.view', 'Ability to view user list and details');
        $this->createPermission('Create Users', 'users.create', 'Ability to create new users');
        $this->createPermission('Edit Users', 'users.edit', 'Ability to edit existing users');
        $this->createPermission('Delete Users', 'users.delete', 'Ability to delete users');

        // Role Management Permissions
        $this->createPermission('View Roles', 'roles.view', 'Ability to view role list and details');
        $this->createPermission('Create Roles', 'roles.create', 'Ability to create new roles');
        $this->createPermission('Edit Roles', 'roles.edit', 'Ability to edit existing roles');
        $this->createPermission('Delete Roles', 'roles.delete', 'Ability to delete roles');

        // Permission Management
        $this->createPermission('Manage Permissions', 'permissions.manage', 'Ability to manage all permissions');

        // Notification Permissions
        $this->createPermission('View Notifications', 'notifications.view', 'Ability to view notifications');
        $this->createPermission('Create Notifications', 'notifications.create', 'Ability to create notifications');
        $this->createPermission('Edit Notifications', 'notifications.edit', 'Ability to edit notifications');
        $this->createPermission('Delete Notifications', 'notifications.delete', 'Ability to delete notifications');
        $this->createPermission('Send Notifications', 'notifications.send', 'Ability to send notifications');
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