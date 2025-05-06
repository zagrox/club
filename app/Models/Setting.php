<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    use HasFactory;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'key',
        'value',
        'group',
    ];
    
    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'key';
    
    /**
     * The "type" of the primary key.
     *
     * @var string
     */
    protected $keyType = 'string';
    
    /**
     * Get a setting value by key.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function get($key, $default = null)
    {
        $cacheKey = "settings:{$key}";
        
        return Cache::remember($cacheKey, 3600, function () use ($key, $default) {
            $setting = static::where('key', $key)->first();
            
            if (!$setting) {
                return $default;
            }
            
            return $setting->value;
        });
    }
    
    /**
     * Set a setting value.
     *
     * @param string $key
     * @param mixed $value
     * @param string $group
     * @return bool
     */
    public static function set($key, $value, $group = 'general')
    {
        $setting = static::firstOrNew(['key' => $key]);
        $setting->value = $value;
        $setting->group = $group;
        $result = $setting->save();
        
        // Clear the cache for this key
        Cache::forget("settings:{$key}");
        
        return $result;
    }
    
    /**
     * Get all settings in a group.
     *
     * @param string $group
     * @return array
     */
    public static function getGroup($group)
    {
        $cacheKey = "settings:group:{$group}";
        
        return Cache::remember($cacheKey, 3600, function () use ($group) {
            $settings = static::where('group', $group)->get();
            
            $result = [];
            foreach ($settings as $setting) {
                $result[$setting->key] = $setting->value;
            }
            
            return $result;
        });
    }
    
    /**
     * Clear cache for a group of settings.
     *
     * @param string $group
     * @return void
     */
    public static function clearGroupCache($group)
    {
        Cache::forget("settings:group:{$group}");
        
        // Also clear individual setting caches in this group
        $settings = static::where('group', $group)->get();
        foreach ($settings as $setting) {
            Cache::forget("settings:{$setting->key}");
        }
    }
} 