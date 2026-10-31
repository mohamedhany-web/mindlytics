<?php

namespace App\Domains\Marketing\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class PopupAd extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'body',
        'image',
        'link_url',
        'cta_text',
        'starts_at',
        'ends_at',
        'max_views_per_visitor',
        'is_active',
        'order',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    /**
     * إعلان منبثق نشط يعرض الآن (ضمن الفترة و is_active)
     */
    public function scopeActiveNow($query)
    {
        $now = Carbon::now();
        return $query->where('is_active', true)
            ->where('starts_at', '<=', $now)
            ->where('ends_at', '>=', $now)
            ->orderBy('order');
    }
}
