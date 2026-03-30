<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OfflineCurriculumNote extends Model
{
    protected $fillable = [
        'offline_course_id',
        'title',
        'body',
    ];

    protected static function booted(): void
    {
        static::deleting(function (OfflineCurriculumNote $note) {
            OfflineCurriculumItem::query()
                ->where('item_type', self::class)
                ->where('item_id', $note->id)
                ->delete();
        });
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(OfflineCourse::class, 'offline_course_id');
    }

    public function offlineCurriculumItems()
    {
        return $this->morphMany(OfflineCurriculumItem::class, 'item');
    }
}
