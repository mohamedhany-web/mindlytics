<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MetaSocialConversation extends Model
{
    public const PLATFORM_MESSENGER = 'messenger';

    public const PLATFORM_INSTAGRAM = 'instagram';

    public const STATUS_OPEN = 'open';

    public const STATUS_CLOSED = 'closed';

    protected $fillable = [
        'meta_social_page_id',
        'platform',
        'participant_id',
        'participant_name',
        'participant_username',
        'participant_profile_pic',
        'phone',
        'email',
        'notes',
        'labels',
        'priority',
        'reminder_at',
        'lead_stage',
        'thread_id',
        'last_message_at',
        'last_message_preview',
        'unread_count',
        'status',
        'assigned_to',
        'sales_lead_id',
        'meta',
    ];

    public const PRIORITIES = [
        'low' => 'منخفض',
        'normal' => 'عادي',
        'high' => 'مرتفع',
        'urgent' => 'عاجل',
    ];

    /** مراحل Lead Center بأسلوب Business Suite (+ مراحل الأكاديمية) */
    public const LEAD_STAGES = [
        'intake' => 'Intake',
        'new_lead' => 'New Lead',
        'first_contact' => 'First Contact',
        'qualified' => 'Qualified',
        'follow_up' => 'Follow-up',
        'offer_sent' => 'Offer Sent',
        'converted' => 'Converted',
        'not_qualified' => 'Not qualified',
        'lost' => 'Lost',
    ];

    public const SUGGESTED_LABELS = [
        'High intent',
        'Hot',
        'Callback',
        'Price',
        'Course info',
        'WhatsApp prefer',
        'VIP',
    ];

    protected $casts = [
        'last_message_at' => 'datetime',
        'reminder_at' => 'datetime',
        'unread_count' => 'integer',
        'labels' => 'array',
        'meta' => 'array',
    ];

    public function page(): BelongsTo
    {
        return $this->belongsTo(MetaSocialPage::class, 'meta_social_page_id');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function salesLead(): BelongsTo
    {
        return $this->belongsTo(SalesLead::class, 'sales_lead_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(MetaSocialMessage::class)->orderBy('sent_at')->orderBy('id');
    }

    public function platformLabel(): string
    {
        return match ($this->platform) {
            self::PLATFORM_INSTAGRAM => 'Instagram',
            default => 'Messenger',
        };
    }

    public function displayName(): string
    {
        return $this->participant_name
            ?: $this->participant_username
            ?: $this->participant_id;
    }
}
