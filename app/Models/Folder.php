<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Folder extends Model
{
    protected $fillable = [
        'user_id',
        'parent_id',
        'is_root',
        'name',
    ];

    protected static function booted(): void
    {
        static::creating(function (Folder $folder): void {
            if ($folder->user_id === null) {
                $folder->user_id = request()->input('viaResource') === 'users' && request()->input('viaResourceId')
                    ? (int) request()->input('viaResourceId')
                    : Auth::id();
            }

            if (($folder->is_root ?? false) === true) {
                return;
            }

            if ($folder->parent_id === null && $folder->user_id !== null) {
                $rootId = DB::table('folders')
                    ->where('user_id', $folder->user_id)
                    ->where('is_root', true)
                    ->value('id');

                if ($rootId) {
                    $folder->parent_id = (int) $rootId;
                }
            }
        });

        static::deleting(function (Folder $folder): void {
            if (($folder->is_root ?? false) === true) {
                abort(403, 'Root folder cannot be deleted.');
            }
        });
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
}
