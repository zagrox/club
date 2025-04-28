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