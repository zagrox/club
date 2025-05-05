<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AddTestRolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create basic roles
        $roles = [
            [
                'name' => 'Administrator',
                'slug' => 'admin',
                'description' => 'Full system access',
                'is_default' => false,
            ],
            [
                'name' => 'Manager',
                'slug' => 'manager',
                'description' => 'Manages users and content',
                'is_default' => false,
            ],
            [
                'name' => 'Member',
                'slug' => 'member',
                'description' => 'Regular user with limited access',
                'is_default' => true,
            ]
        ];
        
        foreach ($roles as $role) {
            DB::table('roles')->insertOrIgnore($role);
        }
    }
}
