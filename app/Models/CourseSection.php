<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseSection extends Model
{
    use HasFactory;

    protected $fillable = [
        'advanced_course_id',
        'parent_id',
        'title',
        'description',
        'order',
        'is_active',
        'unlock_rule',
        'unlock_percent',
        'visibility_scope',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected static function booted()
    {
        static::deleting(function (CourseSection $section) {
            $section->children()->each(fn ($child) => $child->delete());
        });
    }

    public function visibleStudents()
    {
        return $this->belongsToMany(User::class, 'course_section_visible_students')
            ->withTimestamps();
    }

    public function visibleGroups()
    {
        return $this->belongsToMany(ScholarshipGroup::class, 'course_section_visible_groups')
            ->withTimestamps();
    }

    public function parent()
    {
        return $this->belongsTo(CourseSection::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(CourseSection::class, 'parent_id')->orderBy('order');
    }

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

    public function scopeRoots($query)
    {
        return $query->whereNull('parent_id');
    }
}
