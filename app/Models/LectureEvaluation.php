<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LectureEvaluation extends Model
{
    use HasFactory;

    protected $fillable = [
        'lecture_id',
        'student_id',
        'rating',
        'feedback',
        'evaluation_data',
    ];

    protected $casts = [
        'evaluation_data' => 'array',
    ];

    public function lecture()
    {
        return $this->belongsTo(Lecture::class);
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }
}
