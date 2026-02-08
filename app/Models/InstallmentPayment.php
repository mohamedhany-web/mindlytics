<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Payment;

class InstallmentPayment extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';
    public const STATUS_PAID = 'paid';
    public const STATUS_OVERDUE = 'overdue';
    public const STATUS_SKIPPED = 'skipped';

    protected $fillable = [
        'installment_agreement_id',
        'sequence_number',
        'due_date',
        'amount',
        'status',
        'paid_at',
        'reminder_sent_at',
        'overdue_notified_at',
        'payment_id',
        'notes',
    ];

    protected $casts = [
        'due_date' => 'date',
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
        'reminder_sent_at' => 'datetime',
        'overdue_notified_at' => 'datetime',
    ];

    public function agreement(): BelongsTo
    {
        return $this->belongsTo(InstallmentAgreement::class, 'installment_agreement_id');
    }

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class, 'payment_id');
    }

    public function markAsPaid(?Payment $payment = null): void
    {
        $this->status = self::STATUS_PAID;
        $this->paid_at = now();
        if ($payment) {
            $this->payment()->associate($payment);
        }
        $this->reminder_sent_at = null;
        $this->overdue_notified_at = null;
        $this->save();
    }

    public function markAsOverdue(): void
    {
        $this->status = self::STATUS_OVERDUE;
        $this->save();
    }

    public function markReminderSent(): void
    {
        $this->reminder_sent_at = now();
        $this->save();
    }

    public function markOverdueNotified(): void
    {
        $this->overdue_notified_at = now();
        $this->save();
    }
}
