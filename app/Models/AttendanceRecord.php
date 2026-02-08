<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'lecture_id',
        'student_id',
        'joined_at',
        'left_at',
        'attendance_minutes',
        'total_minutes',
        'attendance_percentage',
        'status',
        'source',
        'teams_data',
        'teams_file_path',
    ];

    protected $casts = [
        'joined_at' => 'datetime',
        'left_at' => 'datetime',
        'attendance_percentage' => 'decimal:2',
        'teams_data' => 'array',
    ];

    public function lecture()
    {
        return $this->belongsTo(Lecture::class);
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    /**
     * علاقة مع المستخدم (alias للتوافق)
     */
    public function user()
    {
        return $this->student();
    }
}
