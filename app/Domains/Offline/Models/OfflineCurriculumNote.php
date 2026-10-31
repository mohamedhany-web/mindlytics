<?php

namespace App\Domains\Offline\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Relation;

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
                ->where('item_type', Relation::getMorphAlias(self::class))
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
