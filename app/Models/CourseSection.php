<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseSection extends Model
{
    use HasFactory;

    protected $fillable = [
        'advanced_course_id',
        'title',
        'description',
        'order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function course()
    {
        return $this->belongsTo(AdvancedCourse::class, 'advanced_course_id');
    }

    public function items()
    {
        return $this->hasMany(CurriculumItem::class)->orderBy('order');
    }

    public function activeItems()
    {
        return $this->hasMany(CurriculumItem::class)
            ->where('is_active', true)
            ->orderBy('order')
            ->with('item');
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
