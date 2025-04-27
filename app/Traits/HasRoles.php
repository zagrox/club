<?php

namespace App\Traits;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

trait HasRoles
{
    /**
     * Get all roles assigned to the model.
     */
    public function roles(): MorphToMany
    {
        return $this->morphToMany(Role::class, 'model', 'model_has_roles');
    }

    /**
     * Assign the given role to the model.
     *
     * @param string|array|Role|\Illuminate\Support\Collection $roles
     * @return $this
     */
    public function assignRole($roles)
    {
        $roles = collect($roles)
            ->flatten()
            ->map(function ($role) {
                if (is_string($role)) {
                    return Role::where('slug', $role)->firstOrFail();
                }
                return $role;
            })
            ->all();

        $this->roles()->syncWithoutDetaching($roles);

        return $this;
    }

    /**
     * Revoke the given role from the model.
     *
     * @param string|array|Role|\Illuminate\Support\Collection $roles
     * @return $this
     */
    public function removeRole($roles)
    {
        $roles = collect($roles)
            ->flatten()
            ->map(function ($role) {
                if (is_string($role)) {
                    return Role::where('slug', $role)->firstOrFail();
                }
                return $role;
            })
            ->all();

        $this->roles()->detach($roles);

        return $this;
    }

    /**
     * Sync the given roles to the model.
     *
     * @param string|array|Role|\Illuminate\Support\Collection $roles
     * @return $this
     */
    public function syncRoles($roles)
    {
        $roles = collect($roles)
            ->flatten()
            ->map(function ($role) {
                if (is_string($role)) {
                    return Role::where('slug', $role)->firstOrFail();
                }
                return $role;
            })
            ->pluck('id')
            ->all();

        $this->roles()->sync($roles);

        return $this;
    }

    /**
     * Determine if the model has the given role.
     *
     * @param string|Role $role
     * @return bool
     */
    public function hasRole($role)
    {
        if (is_string($role)) {
            return $this->roles->contains('slug', $role);
        }

        return $role->intersect($this->roles)->isNotEmpty();
    }

    /**
     * Determine if the model has any of the given roles.
     *
     * @param string|array|Role|\Illuminate\Support\Collection $roles
     * @return bool
     */
    public function hasAnyRole($roles)
    {
        if (is_string($roles)) {
            return $this->hasRole($roles);
        }

        $roles = collect($roles)->flatten();
        
        foreach ($roles as $role) {
            if ($this->hasRole($role)) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Determine if the model has all of the given roles.
     *
     * @param string|array|Role|\Illuminate\Support\Collection $roles
     * @return bool
     */
    public function hasAllRoles($roles)
    {
        if (is_string($roles)) {
            return $this->hasRole($roles);
        }
        
        $roles = collect($roles)->flatten();
        
        foreach ($roles as $role) {
            if (!$this->hasRole($role)) {
                return false;
            }
        }
        
        return true;
    }

    /**
     * Get all permissions the model has through roles.
     */
    public function getPermissionsViaRoles()
    {
        return Permission::whereHas('roles', function ($query) {
            $query->whereIn('id', $this->roles->pluck('id'));
        })->get();
    }

    /**
     * Determine if the model has the given permission through roles.
     *
     * @param string|Permission $permission
     * @return bool
     */
    public function hasPermissionTo($permission)
    {
        if (is_string($permission)) {
            $permissionSlug = $permission;
            
            foreach ($this->roles as $role) {
                if ($role->hasPermission($permissionSlug)) {
                    return true;
                }
            }
            
            return false;
        }
        
        foreach ($this->roles as $role) {
            if ($role->permissions->contains($permission)) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Determine if the model has any of the given permissions.
     *
     * @param string|array|Permission|\Illuminate\Support\Collection $permissions
     * @return bool
     */
    public function hasAnyPermission($permissions)
    {
        if (is_string($permissions)) {
            return $this->hasPermissionTo($permissions);
        }
        
        $permissions = collect($permissions)->flatten();
        
        foreach ($permissions as $permission) {
            if ($this->hasPermissionTo($permission)) {
                return true;
            }
        }
        
        return false;
    }
} 