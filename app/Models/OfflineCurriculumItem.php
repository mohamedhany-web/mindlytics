<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class OfflineCurriculumItem extends Model
{
    protected $fillable = [
        'offline_course_section_id',
        'item_type',
        'item_id',
        'order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function section(): BelongsTo
    {
        return $this->belongsTo(OfflineCourseSection::class, 'offline_course_section_id');
    }

    public function item(): MorphTo
    {
        return $this->morphTo();
    }
}
