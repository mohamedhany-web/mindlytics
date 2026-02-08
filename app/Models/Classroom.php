<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Classroom extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'school_id',
        'teacher_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // العلاقات
    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function students()
    {
        return $this->belongsToMany(User::class, 'classroom_students', 'classroom_id', 'student_id')
            ->withPivot('enrolled_at', 'is_active')
            ->withTimestamps();
    }

    public function courses()
    {
        return $this->hasMany(Course::class);
    }
}

