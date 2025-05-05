<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Permission\Models\Permission as SpatiePermission;

class Permission extends SpatiePermission
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
        'slug',
    ];
    
    /**
     * The roles that belong to the permission.
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'permission_role');
    }
    
    /**
     * Get all users that have this permission via their roles.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users(): BelongsToMany
    {
        return $this->morphedByMany(
            User::class,
            'model',
            'model_has_permissions',
            'permission_id',
            'model_id'
        );
    }

    /**
     * Get the slug attribute or fallback to name
     * This ensures backward compatibility with old code
     */
    public function getSlugAttribute($value)
    {
        return $value ?: $this->name;
    }
} 