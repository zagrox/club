<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AssignUsersToRolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the roles
        $adminRole = DB::table('roles')->where('slug', 'admin')->first();
        $managerRole = DB::table('roles')->where('slug', 'manager')->first();
        $memberRole = DB::table('roles')->where('slug', 'member')->first();
        
        if (!$adminRole || !$managerRole || !$memberRole) {
            $this->command->info('Roles not found. Please run the AddTestRolesSeeder first.');
            return;
        }
        
        // Get users by role
        $adminUsers = DB::table('users')->where('role', 'Admin')->orWhere('role', 'admin')->get();
        $managerUsers = DB::table('users')->where('role', 'Editor')->orWhere('role', 'editor')->orWhere('role', 'Manager')->orWhere('role', 'manager')->get();
        $memberUsers = DB::table('users')->where('role', 'User')->orWhere('role', 'user')->orWhere('role', 'Member')->orWhere('role', 'member')->orWhere('role', 'Author')->orWhere('role', 'author')->get();
        $allUsers = DB::table('users')->whereNull('role')->orWhere('role', '')->get();
        
        // Assign admin role to admin users
        foreach ($adminUsers as $user) {
            DB::table('model_has_roles')->updateOrInsert(
                [
                    'role_id' => $adminRole->id,
                    'model_id' => $user->id,
                    'model_type' => 'App\\Models\\User'
                ],
                [
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            );
        }
        
        // Assign manager role to manager users
        foreach ($managerUsers as $user) {
            DB::table('model_has_roles')->updateOrInsert(
                [
                    'role_id' => $managerRole->id,
                    'model_id' => $user->id,
                    'model_type' => 'App\\Models\\User'
                ],
                [
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            );
        }
        
        // Assign member role to member users and to any user without a role
        $usersToAssignAsMember = $memberUsers->merge($allUsers);
        foreach ($usersToAssignAsMember as $user) {
            DB::table('model_has_roles')->updateOrInsert(
                [
                    'role_id' => $memberRole->id,
                    'model_id' => $user->id,
                    'model_type' => 'App\\Models\\User'
                ],
                [
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            );
        }
        
        // Log the counts
        $this->command->info('Assigned ' . $adminUsers->count() . ' users to Admin role.');
        $this->command->info('Assigned ' . $managerUsers->count() . ' users to Manager role.');
        $this->command->info('Assigned ' . $usersToAssignAsMember->count() . ' users to Member role.');
    }
}
