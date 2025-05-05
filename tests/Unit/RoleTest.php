<?php

namespace Tests\Unit;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test role creation.
     */
    public function test_role_can_be_created(): void
    {
        $role = Role::create([
            'name' => 'Test Role',
            'slug' => 'test-role',
            'description' => 'A test role',
            'permissions' => ['users.view', 'roles.view'],
        ]);

        $this->assertDatabaseHas('roles', [
            'name' => 'Test Role',
            'slug' => 'test-role',
            'description' => 'A test role',
        ]);

        $this->assertCount(2, $role->permissions);
        $this->assertTrue($role->hasPermission('users.view'));
        $this->assertTrue($role->hasPermission('roles.view'));
        $this->assertFalse($role->hasPermission('users.create'));
    }

    /**
     * Test role assignment to user.
     */
    public function test_role_can_be_assigned_to_user(): void
    {
        // Create a role and a user
        $role = Role::create([
            'name' => 'Editor',
            'slug' => 'editor',
            'description' => 'Editor role',
            'permissions' => ['users.view', 'content.edit'],
        ]);

        $user = User::factory()->create();

        // Assign role to user
        $user->assignRole($role);

        // Check the relationship
        $this->assertTrue($user->hasRole('editor'));
        $this->assertCount(1, $user->roles);
        $this->assertEquals('Editor', $user->roles->first()->name);
    }

    /**
     * Test role removal from user.
     */
    public function test_role_can_be_removed_from_user(): void
    {
        // Create a role and a user
        $role = Role::create([
            'name' => 'Moderator',
            'slug' => 'moderator',
            'description' => 'Moderator role',
            'permissions' => ['users.view', 'content.moderate'],
        ]);

        $user = User::factory()->create();

        // Assign and then remove role
        $user->assignRole($role);
        $this->assertTrue($user->hasRole('moderator'));

        $user->removeRole($role);
        $this->assertFalse($user->hasRole('moderator'));
        $this->assertCount(0, $user->roles);
    }

    /**
     * Test permission checking through role.
     */
    public function test_user_has_permission_through_role(): void
    {
        // Create a role with permissions
        $role = Role::create([
            'name' => 'Admin',
            'slug' => 'admin',
            'description' => 'Admin role',
            'permissions' => ['users.view', 'users.create', 'users.edit', 'users.delete'],
        ]);

        $user = User::factory()->create();
        $user->assignRole($role);

        // Check permissions
        $this->assertTrue($user->hasPermission('users.view'));
        $this->assertTrue($user->hasPermission('users.create'));
        $this->assertFalse($user->hasPermission('settings.access'));
    }

    /**
     * Test adding and removing permissions from a role.
     */
    public function test_role_permissions_can_be_modified(): void
    {
        // Create a role
        $role = Role::create([
            'name' => 'Customer',
            'slug' => 'customer',
            'description' => 'Customer role',
            'permissions' => ['profile.view'],
        ]);

        // Add a permission
        $role->addPermission('profile.edit');
        $role->save();

        $this->assertCount(2, $role->permissions);
        $this->assertTrue($role->hasPermission('profile.edit'));

        // Remove a permission
        $role->removePermission('profile.view');
        $role->save();

        $this->assertCount(1, $role->permissions);
        $this->assertFalse($role->hasPermission('profile.view'));
        $this->assertTrue($role->hasPermission('profile.edit'));
    }

    /**
     * Test syncing multiple roles to a user.
     */
    public function test_sync_multiple_roles_to_user(): void
    {
        // Create two roles
        $roleOne = Role::create([
            'name' => 'Writer',
            'slug' => 'writer',
            'permissions' => ['content.create'],
        ]);

        $roleTwo = Role::create([
            'name' => 'Reviewer',
            'slug' => 'reviewer',
            'permissions' => ['content.review'],
        ]);

        $user = User::factory()->create();

        // Sync multiple roles
        $user->syncRoles([$roleOne, $roleTwo]);

        $this->assertCount(2, $user->roles);
        $this->assertTrue($user->hasRole('writer'));
        $this->assertTrue($user->hasRole('reviewer'));

        // Sync with only one role should remove other roles
        $user->syncRoles([$roleOne]);
        $user->refresh();

        $this->assertCount(1, $user->roles);
        $this->assertTrue($user->hasRole('writer'));
        $this->assertFalse($user->hasRole('reviewer'));
    }
} 