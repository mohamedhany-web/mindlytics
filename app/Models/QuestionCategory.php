<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestionCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'academic_year_id',
        'academic_subject_id',
        'parent_id',
        'order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * علاقة مع السنة الدراسية
     */
    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    /**
     * علاقة مع المادة الدراسية
     */
    public function academicSubject()
    {
        return $this->belongsTo(AcademicSubject::class);
    }

    /**
     * علاقة مع التصنيف الأب
     */
    public function parent()
    {
        return $this->belongsTo(QuestionCategory::class, 'parent_id');
    }

    /**
     * علاقة مع التصنيفات الفرعية
     */
    public function children()
    {
        return $this->hasMany(QuestionCategory::class, 'parent_id')->orderBy('order');
    }

    /**
     * علاقة مع الأسئلة
     */
    public function questions()
    {
        return $this->hasMany(Question::class, 'category_id');
    }

    /**
     * scope للتصنيفات النشطة
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * scope للتصنيفات الرئيسية
     */
    public function scopeMain($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * الحصول على المسار الكامل للتصنيف
     */
    public function getFullPathAttribute()
    {
        $path = [$this->name];
        $parent = $this->parent;
        
        while ($parent) {
            array_unshift($path, $parent->name);
            $parent = $parent->parent;
        }
        
        return implode(' > ', $path);
    }

    /**
     * عدد الأسئلة في التصنيف وفروعه
     */
    public function getTotalQuestionsCountAttribute()
    {
        $count = $this->questions()->count();
        
        foreach ($this->children as $child) {
            $count += $child->total_questions_count;
        }
        
        return $count;
    }
}