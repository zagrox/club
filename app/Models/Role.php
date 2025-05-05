<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'guard_name',
        'description',
        'is_default',
        'slug',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'is_default' => 'boolean',
    ];

    /**
     * Get the users assigned to this role.
     */
    public function users(): BelongsToMany
    {
        return $this->morphedByMany(User::class, 'model', 'model_has_roles');
    }

    /**
     * The permissions that belong to the role.
     * We override Spatie's default to use our custom table.
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(
            Permission::class,
            'permission_role',
            'role_id',
            'permission_id'
        );
    }

    /**
     * Check if the role has the given permission.
     */
    public function hasPermission($permission)
    {
        if (is_string($permission)) {
            return $this->permissions->contains('slug', $permission);
        }

        return $permission->intersect($this->permissions)->count() > 0;
    }

    /**
     * Give permissions to the role.
     *
     * @param string|array|\Spatie\Permission\Contracts\Permission|\Illuminate\Support\Collection $permissions
     * @return $this
     */
    public function givePermissionTo(...$permissions): self
    {
        $permissions = collect($permissions)
            ->flatten()
            ->map(function ($permission) {
                if (is_string($permission)) {
                    return Permission::where('name', $permission)->firstOrFail();
                }
                return $permission;
            })
            ->all();

        $this->permissions()->syncWithoutDetaching($permissions);

        return $this;
    }

    /**
     * Get the specified permissions.
     */
    protected function getPermissions(array $permissions)
    {
        return Permission::whereIn('name', $permissions)->get();
    }

    /**
     * Revoke permissions from the role.
     *
     * @param string|array|\Spatie\Permission\Contracts\Permission|\Illuminate\Support\Collection $permissions
     * @return $this
     */
    public function revokePermissionTo(...$permissions): self
    {
        $permissions = collect($permissions)
            ->flatten()
            ->map(function ($permission) {
                if (is_string($permission)) {
                    return Permission::where('name', $permission)->firstOrFail();
                }
                return $permission;
            })
            ->all();

        $this->permissions()->detach($permissions);

        return $this;
    }

    /**
     * Sync permissions for the role.
     *
     * @param string|array|\Spatie\Permission\Contracts\Permission|\Illuminate\Support\Collection $permissions
     * @return $this
     */
    public function syncPermissions(...$permissions): self
    {
        $permissions = collect($permissions)
            ->flatten()
            ->map(function ($permission) {
                if (is_string($permission)) {
                    return Permission::where('name', $permission)->firstOrFail();
                }
                return $permission;
            })
            ->pluck('id')
            ->all();

        $this->permissions()->sync($permissions);

        return $this;
    }

    /**
     * Assign a permission to the role.
     *
     * @param  \App\Models\Permission|int  $permission
     * @return void
     */
    public function assignPermission($permission)
    {
        $this->permissions()->sync($permission, false);
    }

    /**
     * Remove a permission from the role.
     *
     * @param  \App\Models\Permission|int  $permission
     * @return void
     */
    public function removePermission($permission)
    {
        $this->permissions()->detach($permission);
    }

    /**
     * Determine if the role has the given permission.
     *
     * @param string|Permission $permission
     * @param string|null $guardName
     * @return bool
     */
    public function hasPermissionTo($permission, ?string $guardName = null): bool
    {
        if (is_string($permission)) {
            $permission = Permission::where('slug', $permission)->first();
        }

        if (!$permission) {
            return false;
        }

        return $this->permissions->contains($permission);
    }

    /**
     * Custom function to check if the role is a system role.
     *
     * @return bool
     */
    public function isSystem()
    {
        return $this->name === 'admin' || $this->name === 'super-admin';
    }
} 