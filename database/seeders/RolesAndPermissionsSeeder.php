<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Illuminate\Cache\CacheManager::class]->forget('roles');
        app()[\Illuminate\Cache\CacheManager::class]->forget('permissions');
        
        DB::transaction(function () {
            // Create permissions
            $permissions = [
                // User management
                ['name' => 'View Users', 'slug' => 'users.view', 'description' => 'Can view users'],
                ['name' => 'Create Users', 'slug' => 'users.create', 'description' => 'Can create users'],
                ['name' => 'Edit Users', 'slug' => 'users.edit', 'description' => 'Can edit users'],
                ['name' => 'Delete Users', 'slug' => 'users.delete', 'description' => 'Can delete users'],
                
                // Role management
                ['name' => 'View Roles', 'slug' => 'roles.view', 'description' => 'Can view roles'],
                ['name' => 'Create Roles', 'slug' => 'roles.create', 'description' => 'Can create roles'],
                ['name' => 'Edit Roles', 'slug' => 'roles.edit', 'description' => 'Can edit roles'],
                ['name' => 'Delete Roles', 'slug' => 'roles.delete', 'description' => 'Can delete roles'],
                
                // Permission management
                ['name' => 'View Permissions', 'slug' => 'permissions.view', 'description' => 'Can view permissions'],
                ['name' => 'Create Permissions', 'slug' => 'permissions.create', 'description' => 'Can create permissions'],
                ['name' => 'Edit Permissions', 'slug' => 'permissions.edit', 'description' => 'Can edit permissions'],
                ['name' => 'Delete Permissions', 'slug' => 'permissions.delete', 'description' => 'Can delete permissions'],
                
                // Notification management
                ['name' => 'View Notifications', 'slug' => 'notifications.view', 'description' => 'Can view notifications'],
                ['name' => 'Create Notifications', 'slug' => 'notifications.create', 'description' => 'Can create notifications'],
                ['name' => 'Edit Notifications', 'slug' => 'notifications.edit', 'description' => 'Can edit notifications'],
                ['name' => 'Delete Notifications', 'slug' => 'notifications.delete', 'description' => 'Can delete notifications'],
            ];
            
            foreach ($permissions as $permission) {
                Permission::create($permission);
            }
            
            // Create roles
            $adminRole = Role::create([
                'name' => 'Administrator',
                'slug' => 'admin',
                'description' => 'Full system access',
                'is_default' => false,
            ]);
            
            $managerRole = Role::create([
                'name' => 'Manager',
                'slug' => 'manager',
                'description' => 'Manages users and content',
                'is_default' => false,
            ]);
            
            $memberRole = Role::create([
                'name' => 'Member',
                'slug' => 'member',
                'description' => 'Regular user with limited access',
                'is_default' => true,
            ]);
            
            // Assign permissions to roles
            $adminRole->syncPermissions(Permission::all());
            
            $managerRole->syncPermissions([
                'users.view', 'users.create', 'users.edit',
                'notifications.view', 'notifications.create', 'notifications.edit', 'notifications.delete',
            ]);
            
            $memberRole->syncPermissions([
                'notifications.view',
            ]);
            
            // Assign admin role to first user if it exists
            $adminUser = User::first();
            if ($adminUser) {
                $adminUser->syncRoles($adminRole);
            }
        });
    }
} 