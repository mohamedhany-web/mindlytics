<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalesDailyReportContact extends Model
{
    public const TYPE_CALL = 'call';

    public const TYPE_MEETING = 'meeting';

    protected $fillable = [
        'sales_daily_report_id',
        'sales_lead_id',
        'contact_name',
        'contact_phone',
        'interaction_type',
        'client_status',
        'client_problems',
    ];

    public function report(): BelongsTo
    {
        return $this->belongsTo(SalesDailyReport::class, 'sales_daily_report_id');
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(SalesLead::class, 'sales_lead_id');
    }

    public function interactionTypeLabel(): string
    {
        return match ($this->interaction_type) {
            self::TYPE_MEETING => 'اجتماع',
            default => 'مكالمة',
        };
    }
}
