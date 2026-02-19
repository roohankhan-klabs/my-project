<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class File extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'folder_id',
        'name',
        'mime_type',
        'size',
        'path',
    ];

    protected static function booted(): void
    {
        static::creating(function (File $file): void {
            if ($file->user_id === null) {
                $file->user_id = request()->input('viaResource') === 'users' && request()->input('viaResourceId')
                    ? (int) request()->input('viaResourceId')
                    : Auth::id();
            }
            if ($file->folder_id === null && request()->input('viaResource') === 'folders' && request()->input('viaResourceId')) {
                $file->folder_id = (int) request()->input('viaResourceId');
            }
        });

        static::updating(function (File $file): void {
            if ($file->isDirty('folder_id') && $file->folder_id !== null) {
                $folder = \App\Models\Folder::find($file->folder_id);
                if ($folder && $folder->user_id !== $file->user_id) {
                    throw new \Illuminate\Database\Eloquent\ModelNotFoundException(
                        'You can only assign files to folders you own.'
                    );
                }
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function folder()
    {
        return $this->belongsTo(Folder::class);
    }

    public function getSizeInMB()
    {
        return $this->size / 1024 / 1024;
    }
}
