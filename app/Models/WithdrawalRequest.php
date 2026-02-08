<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WithdrawalRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'instructor_id',
        'request_number',
        'amount',
        'status',
        'payment_method',
        'bank_name',
        'account_number',
        'account_holder_name',
        'iban',
        'notes',
        'admin_notes',
        'processed_at',
        'processed_by',
        'payment_id',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'processed_at' => 'datetime',
    ];

    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_PROCESSING = 'processing';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';

    public const PAYMENT_METHOD_BANK_TRANSFER = 'bank_transfer';
    public const PAYMENT_METHOD_WALLET = 'wallet';
    public const PAYMENT_METHOD_CASH = 'cash';
    public const PAYMENT_METHOD_OTHER = 'other';

    protected static function booted(): void
    {
        static::creating(function (WithdrawalRequest $request) {
            if (empty($request->request_number)) {
                $request->request_number = 'WDR-' . date('Y') . '-' . str_pad(WithdrawalRequest::count() + 1, 6, '0', STR_PAD_LEFT);
            }
        });
    }

    public function instructor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class, 'payment_id');
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            self::STATUS_PENDING => 'قيد المراجعة',
            self::STATUS_APPROVED => 'موافق عليه',
            self::STATUS_REJECTED => 'مرفوض',
            self::STATUS_PROCESSING => 'قيد المعالجة',
            self::STATUS_COMPLETED => 'مكتمل',
            self::STATUS_CANCELLED => 'ملغي',
            default => 'غير محدد',
        };
    }

    public function getPaymentMethodLabelAttribute(): string
    {
        return match($this->payment_method) {
            self::PAYMENT_METHOD_BANK_TRANSFER => 'تحويل بنكي',
            self::PAYMENT_METHOD_WALLET => 'محفظة',
            self::PAYMENT_METHOD_CASH => 'نقدي',
            self::PAYMENT_METHOD_OTHER => 'أخرى',
            default => 'غير محدد',
        };
    }

    public function scopeForInstructor($query, $instructorId)
    {
        return $query->where('instructor_id', $instructorId);
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }
}
