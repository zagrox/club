<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RoleManagementTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * Setup admin user for tests.
     */
    private function setupAdmin()
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $adminRole = Role::create([
            'name' => 'Administrator',
            'slug' => 'admin',
            'description' => 'Administrator role',
            'permissions' => [
                'users.view', 'users.create', 'users.edit', 'users.delete',
                'roles.view', 'roles.create', 'roles.edit', 'roles.delete',
            ],
            'is_system' => true,
        ]);

        $admin->assignRole($adminRole);

        return $admin;
    }

    /**
     * Test roles index page.
     */
    public function test_admin_can_view_roles_page(): void
    {
        $admin = $this->setupAdmin();

        $response = $this->actingAs($admin)
            ->get(route('roles.index'));

        $response->assertStatus(200);
        $response->assertViewIs('pages.roles.index');
    }

    /**
     * Test role creation page.
     */
    public function test_admin_can_view_create_role_page(): void
    {
        $admin = $this->setupAdmin();

        $response = $this->actingAs($admin)
            ->get(route('roles.create'));

        $response->assertStatus(200);
        $response->assertViewIs('pages.roles.create');
    }

    /**
     * Test role creation.
     */
    public function test_admin_can_create_role(): void
    {
        $admin = $this->setupAdmin();

        $roleData = [
            'name' => 'Content Editor',
            'description' => 'Can edit content but not users',
            'permissions' => ['users.view', 'content.edit', 'content.create'],
        ];

        $response = $this->actingAs($admin)
            ->post(route('roles.store'), $roleData);

        $response->assertRedirect(route('roles.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('roles', [
            'name' => 'Content Editor',
            'description' => 'Can edit content but not users',
            'slug' => 'content-editor',
        ]);
    }

    /**
     * Test role show page.
     */
    public function test_admin_can_view_role_details(): void
    {
        $admin = $this->setupAdmin();

        $role = Role::create([
            'name' => 'Editor',
            'slug' => 'editor',
            'description' => 'Editor role',
            'permissions' => ['content.edit', 'content.view'],
        ]);

        $response = $this->actingAs($admin)
            ->get(route('roles.show', $role->id));

        $response->assertStatus(200);
        $response->assertViewIs('pages.roles.show');
        $response->assertSee('Editor');
    }

    /**
     * Test role edit page.
     */
    public function test_admin_can_view_edit_role_page(): void
    {
        $admin = $this->setupAdmin();

        $role = Role::create([
            'name' => 'Editor',
            'slug' => 'editor',
            'description' => 'Editor role',
            'permissions' => ['content.edit', 'content.view'],
        ]);

        $response = $this->actingAs($admin)
            ->get(route('roles.edit', $role->id));

        $response->assertStatus(200);
        $response->assertViewIs('pages.roles.edit');
        $response->assertSee('Editor');
    }

    /**
     * Test role update.
     */
    public function test_admin_can_update_role(): void
    {
        $admin = $this->setupAdmin();

        $role = Role::create([
            'name' => 'Editor',
            'slug' => 'editor',
            'description' => 'Editor role',
            'permissions' => ['content.edit', 'content.view'],
        ]);

        $updatedData = [
            'name' => 'Senior Editor',
            'description' => 'Senior Editor role with more permissions',
            'permissions' => ['content.edit', 'content.view', 'content.publish'],
        ];

        $response = $this->actingAs($admin)
            ->put(route('roles.update', $role->id), $updatedData);

        $response->assertRedirect(route('roles.index'));
        $response->assertSessionHas('success');

        $role->refresh();
        $this->assertEquals('Senior Editor', $role->name);
        $this->assertEquals('Senior Editor role with more permissions', $role->description);
        $this->assertCount(3, $role->permissions);
        $this->assertTrue($role->hasPermission('content.publish'));
    }

    /**
     * Test role deletion.
     */
    public function test_admin_can_delete_role(): void
    {
        $admin = $this->setupAdmin();

        $role = Role::create([
            'name' => 'Temporary',
            'slug' => 'temporary',
            'description' => 'Temporary role',
            'permissions' => ['content.view'],
        ]);

        $response = $this->actingAs($admin)
            ->delete(route('roles.destroy', $role->id));

        $response->assertRedirect(route('roles.index'));
        $response->assertSessionHas('success');

        $this->assertSoftDeleted($role);
    }

    /**
     * Test assigning users to a role.
     */
    public function test_admin_can_assign_users_to_role(): void
    {
        $admin = $this->setupAdmin();

        $role = Role::create([
            'name' => 'Tester',
            'slug' => 'tester',
            'description' => 'Role for testers',
            'permissions' => ['testing.run'],
        ]);

        $users = User::factory()->count(3)->create();
        $userIds = $users->pluck('id')->toArray();

        $response = $this->actingAs($admin)
            ->post(route('roles.assign-users', $role->id), [
                'user_ids' => $userIds,
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $role->refresh();
        $this->assertCount(3, $role->users);
        
        foreach ($users as $user) {
            $this->assertTrue($user->hasRole('tester'));
        }
    }

    /**
     * Test removing a user from a role.
     */
    public function test_admin_can_remove_user_from_role(): void
    {
        $admin = $this->setupAdmin();

        $role = Role::create([
            'name' => 'Contributor',
            'slug' => 'contributor',
            'description' => 'Role for contributors',
            'permissions' => ['content.create'],
        ]);

        $user = User::factory()->create();
        $user->assignRole($role);

        $this->assertTrue($user->hasRole('contributor'));

        $response = $this->actingAs($admin)
            ->delete(route('roles.remove-user', ['role' => $role->id, 'user' => $user->id]));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $user->refresh();
        $this->assertFalse($user->hasRole('contributor'));
    }

    /**
     * Test that system roles cannot be modified.
     */
    public function test_system_roles_cannot_be_modified(): void
    {
        $admin = $this->setupAdmin();

        $systemRole = Role::create([
            'name' => 'System Role',
            'slug' => 'system',
            'description' => 'System role cannot be modified',
            'permissions' => ['system.access'],
            'is_system' => true,
        ]);

        $updatedData = [
            'name' => 'Modified System Role',
            'description' => 'Trying to modify a system role',
            'permissions' => ['system.access', 'system.modify'],
        ];

        $response = $this->actingAs($admin)
            ->put(route('roles.update', $systemRole->id), $updatedData);

        $response->assertRedirect();
        $response->assertSessionHas('error');

        $systemRole->refresh();
        $this->assertEquals('System Role', $systemRole->name);
    }

    /**
     * Test that system roles cannot be deleted.
     */
    public function test_system_roles_cannot_be_deleted(): void
    {
        $admin = $this->setupAdmin();

        $systemRole = Role::create([
            'name' => 'System Role',
            'slug' => 'system',
            'description' => 'System role cannot be deleted',
            'permissions' => ['system.access'],
            'is_system' => true,
        ]);

        $response = $this->actingAs($admin)
            ->delete(route('roles.destroy', $systemRole->id));

        $response->assertRedirect();
        $response->assertSessionHas('error');

        $this->assertDatabaseHas('roles', [
            'id' => $systemRole->id,
            'name' => 'System Role',
        ]);
    }
} 