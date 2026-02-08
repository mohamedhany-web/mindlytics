<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeamsAttendanceFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'lecture_id',
        'file_name',
        'file_path',
        'file_type',
        'total_records',
        'processed_records',
        'status',
        'error_message',
        'uploaded_by',
    ];

    public function lecture()
    {
        return $this->belongsTo(Lecture::class);
    }

    public function uploadedBy()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
