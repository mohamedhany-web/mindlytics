<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OfflineCourseSection extends Model
{
    protected $fillable = [
        'offline_course_id',
        'parent_id',
        'title',
        'description',
        'order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::deleting(function (OfflineCourseSection $section) {
            $section->children()->each(fn ($child) => $child->delete());
        });
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(OfflineCourse::class, 'offline_course_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(OfflineCourseSection::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(OfflineCourseSection::class, 'parent_id')->orderBy('order');
    }

    public function items(): HasMany
    {
        return $this->hasMany(OfflineCurriculumItem::class, 'offline_course_section_id')->orderBy('order');
    }

    public function activeItems(): HasMany
    {
        return $this->hasMany(OfflineCurriculumItem::class, 'offline_course_section_id')
            ->where('is_active', true)
            ->orderBy('order')
            ->with('item');
    }

    public function scopeRoots($query)
    {
        return $query->whereNull('parent_id');
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }
}
