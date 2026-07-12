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
        'visibility_scope',
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

    public function visibleStudents()
    {
        return $this->belongsToMany(User::class, 'curriculum_item_visible_students')
            ->withTimestamps();
    }

    public function visibleGroups()
    {
        return $this->belongsToMany(ScholarshipGroup::class, 'curriculum_item_visible_groups')
            ->withTimestamps();
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
