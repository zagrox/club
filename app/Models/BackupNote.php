<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BackupNote extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'filename',
        'disk',
        'path',
        'title',
        'note',
    ];

    /**
     * Find a backup note by filename
     *
     * @param string $filename
     * @param string $disk
     * @return static|null
     */
    public static function findByFilename($filename, $disk = 'backup')
    {
        return static::where('filename', $filename)
            ->where('disk', $disk)
            ->first();
    }
}
