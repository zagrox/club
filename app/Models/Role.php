<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'is_default',
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
     */
    public function permissions()
    {
        return $this->belongsToMany(Permission::class);
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
     */
    public function givePermissionTo($permissions)
    {
        $permissions = collect($permissions)
            ->flatten()
            ->map(function ($permission) {
                if (is_string($permission)) {
                    return Permission::where('slug', $permission)->firstOrFail();
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
        return Permission::whereIn('slug', $permissions)->get();
    }

    /**
     * Revoke permissions from the role.
     */
    public function revokePermissionTo($permissions)
    {
        $permissions = collect($permissions)
            ->flatten()
            ->map(function ($permission) {
                if (is_string($permission)) {
                    return Permission::where('slug', $permission)->firstOrFail();
                }
                return $permission;
            })
            ->all();

        $this->permissions()->detach($permissions);

        return $this;
    }

    /**
     * Sync permissions for the role.
     */
    public function syncPermissions($permissions)
    {
        $permissions = collect($permissions)
            ->flatten()
            ->map(function ($permission) {
                if (is_string($permission)) {
                    return Permission::where('slug', $permission)->firstOrFail();
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
     * @return bool
     */
    public function hasPermissionTo($permission)
    {
        if (is_string($permission)) {
            $permission = Permission::where('slug', $permission)->first();
        }

        if (!$permission) {
            return false;
        }

        return $this->permissions->contains($permission);
    }
} 