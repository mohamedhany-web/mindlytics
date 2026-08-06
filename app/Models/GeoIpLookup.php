<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GeoIpLookup extends Model
{
    protected $fillable = [
        'ip',
        'country_code',
        'country_name',
        'region_name',
        'city',
        'raw',
        'looked_up_at',
    ];

    protected $casts = [
        'raw' => 'array',
        'looked_up_at' => 'datetime',
    ];
}
