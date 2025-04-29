<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Permission extends Model
{
    use HasFactory, SoftDeletes;
    
    // We'll keep the fillable attributes updated to match both systems
    protected $fillable = [
        'name',         // This is the slug in our old system
        'slug',
        'description',  // Description stays the same
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
     */
    public function users()
    {
        $users = collect();
        
        $this->roles->each(function ($role) use ($users) {
            $users = $users->merge($role->users);
        });
        
        return $users->unique('id');
    }
} 