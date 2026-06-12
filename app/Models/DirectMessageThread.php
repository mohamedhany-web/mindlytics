<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DirectMessageThread extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'instructor_id',
        'student_id',
        'last_message_at',
    ];

    protected $casts = [
        'last_message_at' => 'datetime',
    ];

    public function course()
    {
        return $this->belongsTo(AdvancedCourse::class, 'course_id');
    }

    public function instructor()
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function messages()
    {
        return $this->hasMany(DirectMessage::class, 'thread_id');
    }
}

