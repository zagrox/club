<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Permission extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'description',
    ];

    /**
     * The roles that belong to the permission.
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class);
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