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

        static::saving(function (File $file): void {
            // Auto-populate mime_type and size when path is set and they are null
            if ($file->isDirty('path') && $file->path && (! $file->mime_type || ! $file->size)) {
                $fullPath = storage_path('app/public/'.$file->path);
                if (file_exists($fullPath)) {
                    if (! $file->mime_type) {
                        $file->mime_type = mime_content_type($fullPath);
                    }
                    if (! $file->size) {
                        $file->size = filesize($fullPath);
                    }
                }
            }

            // Auto-populate name from path if name is not set
            if ($file->isDirty('path') && $file->path && ! $file->name) {
                $file->name = basename($file->path);
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

    protected $appends = ['size_in_kb'];

    public function getSizeInKbAttribute()
    {
        return $this->size ? number_format($this->size / 1024, 2).' KB' : '0 KB';
    }
}
