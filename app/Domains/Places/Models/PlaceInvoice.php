<?php

namespace App\Domains\Places\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Domains\Offline\Models\OfflineLocation;
use App\Domains\Accounting\Support\DocumentNumber;

class PlaceInvoice extends Model
{
    protected $fillable = [
        'invoice_number',
        'offline_location_id',
        'place_monthly_settlement_id',
        'amount',
        'currency',
        'period_month',
        'status',
        'issued_at',
        'paid_at',
        'line_items',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'issued_at' => 'datetime',
        'paid_at' => 'datetime',
        'line_items' => 'array',
    ];

    public function location(): BelongsTo
    {
        return $this->belongsTo(OfflineLocation::class, 'offline_location_id');
    }

    public function settlement(): BelongsTo
    {
        return $this->belongsTo(PlaceMonthlySettlement::class, 'place_monthly_settlement_id');
    }

    public static function generateNumber(): string
    {
        return DocumentNumber::next(static::class, 'invoice_number', 'PLI-'.now()->format('Ym').'-', 4);
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'paid' => 'مدفوعة',
            'cancelled' => 'ملغاة',
            default => 'صادرة',
        };
    }
}
