<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MarketingRegionDailyStat extends Model
{
    protected $fillable = [
        'stat_date',
        'country_code',
        'visits',
    ];

    protected $casts = [
        'stat_date' => 'date',
        'visits' => 'integer',
    ];
}
