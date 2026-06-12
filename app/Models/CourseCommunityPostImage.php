<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseCommunityPostImage extends Model
{
    protected $fillable = [
        'post_id',
        'path',
        'disk',
        'sort_order',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    public function post(): BelongsTo
    {
        return $this->belongsTo(CourseCommunityPost::class, 'post_id');
    }

    /**
     * رابط عرض الصورة (قرص public محليًا أو رابط موقّع لـ R2/S3 الخاص).
     */
    public function getUrlAttribute(): string
    {
        $disk = $this->disk ?: 'public';

        return storage_inline_media_url($disk, (string) $this->path);
    }
}
