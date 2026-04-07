<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalesKpiTarget extends Model
{
    protected $fillable = [
        'user_id',
        'year_month',
        'targets',
    ];

    protected function casts(): array
    {
        return [
            'targets' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function yearMonthKey(\DateTimeInterface $date): string
    {
        return $date->format('Y-m');
    }
}
