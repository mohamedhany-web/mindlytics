<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OfflineLocation extends Model
{
    protected $fillable = [
        'name',
        'address',
        'city',
        'phone',
        'description',
        'capacity',
        'facilities',
        'is_active',
    ];

    protected $casts = [
        'facilities' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * علاقة مع الكورسات الأوفلاين
     */
    public function courses(): HasMany
    {
        return $this->hasMany(OfflineCourse::class, 'location_id');
    }

    /**
     * Scope للأماكن النشطة
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
