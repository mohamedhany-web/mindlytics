<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CampaignDailyReport extends Model
{
    protected $fillable = [
        'advertising_campaign_id',
        'user_id',
        'sales_daily_report_id',
        'report_date',
        'new_messages',
        'whatsapp_messages',
        'messenger_messages',
        'instagram_messages',
        'qualified',
        'unqualified',
        'converted',
        'notes',
    ];

    protected $casts = [
        'report_date' => 'date',
        'new_messages' => 'integer',
        'whatsapp_messages' => 'integer',
        'messenger_messages' => 'integer',
        'instagram_messages' => 'integer',
        'qualified' => 'integer',
        'unqualified' => 'integer',
        'converted' => 'integer',
    ];

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(AdvertisingCampaign::class, 'advertising_campaign_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function salesDailyReport(): BelongsTo
    {
        return $this->belongsTo(SalesDailyReport::class);
    }

    /**
     * إجمالي الرسائل عبر كل القنوات (احتياطي لو new_messages صفر).
     */
    public function totalMessages(): int
    {
        return max(
            (int) $this->new_messages,
            (int) $this->whatsapp_messages + (int) $this->messenger_messages + (int) $this->instagram_messages
        );
    }
}
