<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SpatieSeedPermissionsAndRoles extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // First clear out any existing roles and permissions
        $this->clearData();
        
        // Create roles
        $this->createRoles();
        
        // Create permissions
        $this->createPermissions();
        
        // Assign permissions to roles
        $this->assignPermissionsToRoles();
        
        // Assign admin role to user ID 1 if it exists
        $this->assignAdminToFirstUser();
    }
    
    /**
     * Clear out existing roles and permissions.
     */
    private function clearData(): void
    {
        // Disable foreign key checks
        Schema::disableForeignKeyConstraints();
        
        // Truncate tables
        DB::table('role_has_permissions')->truncate();
        DB::table('model_has_roles')->truncate();
        DB::table('model_has_permissions')->truncate();
        
        // Clear existing roles and permissions
        Role::query()->delete();
        Permission::query()->delete();
        
        // Re-enable foreign key checks
        Schema::enableForeignKeyConstraints();
    }
    
    /**
     * Create the standard roles.
     */
    private function createRoles(): void
    {
        $roles = [
            [
                'name' => 'admin',
                'guard_name' => 'web',
                'description' => 'Administrator with full access',
                'is_default' => false
            ],
            [
                'name' => 'editor',
                'guard_name' => 'web',
                'description' => 'Editor with limited access',
                'is_default' => false
            ],
            [
                'name' => 'user',
                'guard_name' => 'web',
                'description' => 'Regular user',
                'is_default' => true
            ],
        ];
        
        foreach ($roles as $role) {
            Role::create($role);
        }
    }
    
    /**
     * Create all permissions.
     */
    private function createPermissions(): void
    {
        $permissions = [
            // Users
            ['name' => 'users.view', 'guard_name' => 'web', 'description' => 'Can view list of users'],
            ['name' => 'users.create', 'guard_name' => 'web', 'description' => 'Can create new users'],
            ['name' => 'users.edit', 'guard_name' => 'web', 'description' => 'Can edit existing users'],
            ['name' => 'users.delete', 'guard_name' => 'web', 'description' => 'Can delete users'],
            
            // Roles
            ['name' => 'roles.view', 'guard_name' => 'web', 'description' => 'Can view list of roles'],
            ['name' => 'roles.create', 'guard_name' => 'web', 'description' => 'Can create new roles'],
            ['name' => 'roles.edit', 'guard_name' => 'web', 'description' => 'Can edit existing roles'],
            ['name' => 'roles.delete', 'guard_name' => 'web', 'description' => 'Can delete roles'],
            
            // Permissions
            ['name' => 'permissions.manage', 'guard_name' => 'web', 'description' => 'Can manage permissions'],
            
            // Orders
            ['name' => 'orders.view', 'guard_name' => 'web', 'description' => 'Can view list of orders'],
            ['name' => 'orders.create', 'guard_name' => 'web', 'description' => 'Can create new orders'],
            ['name' => 'orders.edit', 'guard_name' => 'web', 'description' => 'Can edit existing orders'],
            ['name' => 'orders.delete', 'guard_name' => 'web', 'description' => 'Can delete orders'],
            
            // Notifications
            ['name' => 'notifications.view', 'guard_name' => 'web', 'description' => 'Can view notifications'],
            ['name' => 'notifications.create', 'guard_name' => 'web', 'description' => 'Can create new notifications'],
            ['name' => 'notifications.manage', 'guard_name' => 'web', 'description' => 'Can manage notifications'],
            
            // Backup
            ['name' => 'backups.view', 'guard_name' => 'web', 'description' => 'Can view backups'],
            ['name' => 'backups.create', 'guard_name' => 'web', 'description' => 'Can create new backups'],
            ['name' => 'backups.download', 'guard_name' => 'web', 'description' => 'Can download backups'],
            ['name' => 'backups.delete', 'guard_name' => 'web', 'description' => 'Can delete backups'],
        ];
        
        foreach ($permissions as $permission) {
            Permission::create($permission);
        }
    }
    
    /**
     * Assign permissions to roles.
     */
    private function assignPermissionsToRoles(): void
    {
        // Get roles
        $adminRole = Role::where('name', 'admin')->first();
        $editorRole = Role::where('name', 'editor')->first();
        $userRole = Role::where('name', 'user')->first();
        
        // Admin gets all permissions
        $adminRole->givePermissionTo(Permission::all());
        
        // Editor permissions
        $editorRole->givePermissionTo([
            'users.view',
            'roles.view',
            'orders.view', 'orders.create', 'orders.edit',
            'notifications.view', 'notifications.create',
            'backups.view'
        ]);
        
        // User permissions
        $userRole->givePermissionTo([
            'users.view',
            'orders.view', 'orders.create',
            'notifications.view'
        ]);
    }
    
    /**
     * Assign admin role to first user if it exists.
     */
    private function assignAdminToFirstUser(): void
    {
        $user = User::find(1);
        $adminRole = Role::where('name', 'admin')->first();
        
        if ($user && $adminRole) {
            $user->assignRole($adminRole);
        }
    }
} 