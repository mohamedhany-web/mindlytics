<?php

namespace App\Domains\Learning\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LectureMaterial extends Model
{
    protected $fillable = [
        'lecture_id',
        'file_name',
        'file_path',
        'title',
        'is_visible_to_student',
        'sort_order',
    ];

    protected $casts = [
        'is_visible_to_student' => 'boolean',
    ];

    public function lecture(): BelongsTo
    {
        return $this->belongsTo(Lecture::class);
    }
}
