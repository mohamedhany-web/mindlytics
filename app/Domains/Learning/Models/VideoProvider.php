<?php

namespace App\Domains\Learning\Models;

use Illuminate\Database\Eloquent\Model;

class VideoProvider extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'platform',
        'is_active',
        'library_id',
        'cdn_hostname',
        'api_key',
        'token_auth_key',
        'extra_config',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'extra_config' => 'array',
    ];
}

