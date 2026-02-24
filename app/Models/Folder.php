<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Folder extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'parent_id',
        // 'is_root',
        'name',
    ];

    protected $appends = ['total_size'];

    protected static function booted(): void
    {
        static::creating(function (Folder $folder): void {
            if ($folder->user_id === null) {
                $folder->user_id = request()->input('viaResource') === 'users' && request()->input('viaResourceId')
                    ? (int) request()->input('viaResourceId')
                    : Auth::id();
            }

        });

        // static::deleting(function (Folder $folder): void {
        //     if (($folder->is_root ?? false) === true) {
        //         abort(403, 'Root folder cannot be deleted.');
        //     }
        // });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function parent()
    {
        return $this->belongsTo(Folder::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Folder::class, 'parent_id');
    }

    public function files()
    {
        return $this->hasMany(File::class);
    }

    public function getTotalSizeAttribute()
    {
        return $this->files()->sum('size');
    }

    public function getBreadcrumbPath(): array
    {
        $path = [];
        $current = $this;

        while ($current) {
            array_unshift($path, $current);
            $current = $current->parent;
        }

        return $path;
    }
}
