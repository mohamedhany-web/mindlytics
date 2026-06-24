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
        'acceptance_email_sent_at',
        'whatsapp_link_sent_at',
        'converted_to_lead_at',
        'sales_lead_id',
    ];

    protected $casts = [
        'checked_in_at' => 'datetime',
        'acceptance_email_sent_at' => 'datetime',
        'whatsapp_link_sent_at' => 'datetime',
        'converted_to_lead_at' => 'datetime',
    ];

    public function workshop(): BelongsTo
    {
        return $this->belongsTo(Workshop::class);
    }

    public function salesLead(): BelongsTo
    {
        return $this->belongsTo(SalesLead::class, 'sales_lead_id');
    }

    public function isConvertedToLead(): bool
    {
        return $this->converted_to_lead_at !== null || $this->sales_lead_id !== null;
    }
}

