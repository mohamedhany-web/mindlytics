<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CurriculumItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_section_id',
        'item_type',
        'item_id',
        'order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function section()
    {
        return $this->belongsTo(CourseSection::class, 'course_section_id');
    }

    public function item()
    {
        return $this->morphTo();
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }
}
