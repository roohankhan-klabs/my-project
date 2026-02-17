<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    //
    protected $fillable = [
        'user_id',
        'folder_id',
        'name',
        'mime_type',
        'size',
        'path',
    ];

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
