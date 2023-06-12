<?php

namespace LaraFiler\Models;

use Illuminate\Database\Eloquent\Model;

class LarafmDocument extends Model
{
    protected $fillable = [
        'filename',
        'slug',
        'path',
        'size',
        'mimetype',
        'type',
        'extension',
        'created_by',
        'thumbs',
        'group_name'
    ];

    protected $hidden = [
        'id',
        'created_at',
        'updated_at',
        'path',
        'type',
        'created_by',
    ];
}