<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'name',
        'description',
        'leader_id',
        'max_members',
        'status',
    ];

    protected $casts = [
        'max_members' => 'integer',
    ];

    public function course()
    {
        return $this->belongsTo(AdvancedCourse::class, 'course_id');
    }

    public function leader()
    {
        return $this->belongsTo(User::class, 'leader_id');
    }

    public function members()
    {
        return $this->belongsToMany(User::class, 'group_members')
            ->withPivot('role', 'joined_at')
            ->withTimestamps();
    }

    public function messages()
    {
        return $this->hasMany(GroupMessage::class)->orderBy('created_at');
    }

    public function assignments()
    {
        return $this->hasMany(Assignment::class)->orderBy('due_date');
    }

    public function assignmentSubmissions()
    {
        return $this->hasMany(AssignmentSubmission::class);
    }

    public function isFull()
    {
        return $this->members()->count() >= $this->max_members;
    }
}
