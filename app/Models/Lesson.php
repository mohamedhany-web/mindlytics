<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Lesson extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'content',
        'video_url',
        'thumbnail',
        'course_id',
        'order',
        'duration_minutes',
        'is_free',
        'status',
    ];

    protected $casts = [
        'is_free' => 'boolean',
    ];

    // العلاقات
    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
