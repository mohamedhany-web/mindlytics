<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkshopRegistration extends Model
{
    use HasFactory;

    protected $fillable = [
        'workshop_id',
        'name',
        'email',
        'phone',
        'attendance_mode',
        'notes',
        'status',
        'checkin_token',
        'checked_in_at',
    ];

    public function workshop(): BelongsTo
    {
        return $this->belongsTo(Workshop::class);
    }
}

