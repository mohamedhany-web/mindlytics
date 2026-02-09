<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InstructorRequest extends Model
{
    protected $fillable = [
        'instructor_id',
        'subject',
        'message',
        'status',
        'admin_reply',
        'replied_at',
        'replied_by',
    ];

    protected $casts = [
        'replied_at' => 'datetime',
    ];

    public function instructor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    public function repliedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'replied_by');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
}
