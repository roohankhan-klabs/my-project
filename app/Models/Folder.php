<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Folder extends Model
{
    //
    protected $fillable = [
        'user_id',
        'parent_id',
        'name',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function parent()
    {
        return $this->belongsTo(Folder::class);
    }

    public function files()
    {
        return $this->hasMany(File::class);
    }
}
