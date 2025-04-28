<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;

class SyncUserRolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        foreach ($users as $user) {
            if ($user->role) {
                $role = Role::where('slug', $user->role)->first();
                if ($role && !$user->roles->contains($role->id)) {
                    $user->roles()->attach($role->id);
                }
            }
        }
        $this->command->info('User roles synced to model_has_roles table.');
    }
} 