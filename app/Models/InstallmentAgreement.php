<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InstallmentAgreement extends Model
{
    use HasFactory;

    protected $fillable = [
        'installment_plan_id',
        'student_course_enrollment_id',
        'user_id',
        'advanced_course_id',
        'total_amount',
        'deposit_amount',
        'installments_count',
        'start_date',
        'status',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'deposit_amount' => 'decimal:2',
        'installments_count' => 'integer',
        'start_date' => 'date',
    ];

    public const STATUS_ACTIVE = 'active';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';
    public const STATUS_OVERDUE = 'overdue';

    public function plan(): BelongsTo
    {
        return $this->belongsTo(InstallmentPlan::class, 'installment_plan_id');
    }

    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(StudentCourseEnrollment::class, 'student_course_enrollment_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(AdvancedCourse::class, 'advanced_course_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(InstallmentPayment::class);
    }

    public function paidPayments()
    {
        return $this->payments()->where('status', InstallmentPayment::STATUS_PAID);
    }

    public function pendingPayments()
    {
        return $this->payments()->where('status', InstallmentPayment::STATUS_PENDING);
    }

    public function totalPaidAmount(): float
    {
        return (float) $this->paidPayments()->sum('amount');
    }

    public function totalDueAmount(): float
    {
        return (float) $this->pendingPayments()->sum('amount');
    }

    public function generateSchedule(?Carbon $startDate = null): void
    {
        $plan = $this->plan;
        if (!$plan) {
            return;
        }

        $start = $startDate ? $startDate->copy() : Carbon::parse($this->start_date);
        $amountPerInstallment = $this->calculateInstallmentAmount();

        $sequence = 1;
        if ($this->deposit_amount > 0) {
            $this->payments()->create([
                'sequence_number' => 0,
                'due_date' => $start->copy(),
                'amount' => $this->deposit_amount,
                'status' => InstallmentPayment::STATUS_PENDING,
            ]);
        }

        for ($i = 0; $i < $this->installments_count; $i++) {
            $dueDate = $start->copy();
            if ($i > 0 || $this->deposit_amount > 0) {
                $dueDate = $this->calculateDueDate($start, $plan, $i + ($this->deposit_amount > 0 ? 1 : 0));
            }

            $this->payments()->create([
                'sequence_number' => $sequence++,
                'due_date' => $dueDate,
                'amount' => $amountPerInstallment,
                'status' => InstallmentPayment::STATUS_PENDING,
            ]);
        }
    }

    protected function calculateInstallmentAmount(): float
    {
        $remaining = $this->total_amount - $this->deposit_amount;
        if ($this->installments_count <= 0) {
            return (float) $remaining;
        }

        return round($remaining / $this->installments_count, 2);
    }

    protected function calculateDueDate(Carbon $start, InstallmentPlan $plan, int $step): Carbon
    {
        $dueDate = $start->copy();
        $unit = $plan->frequency_unit;
        $interval = max(1, (int) $plan->frequency_interval);

        return match ($unit) {
            'week', 'weeks' => $dueDate->addWeeks($interval * $step),
            'day', 'days' => $dueDate->addDays($interval * $step),
            'year', 'years' => $dueDate->addYears($interval * $step),
            default => $dueDate->addMonths($interval * $step),
        };
    }

    public function refreshStatus(): void
    {
        $this->loadMissing('payments');

        if ($this->payments->every(fn (InstallmentPayment $payment) => $payment->status === InstallmentPayment::STATUS_PAID)) {
            $this->status = self::STATUS_COMPLETED;
        } elseif ($this->payments->contains(fn (InstallmentPayment $payment) => $payment->status === InstallmentPayment::STATUS_OVERDUE)) {
            $this->status = self::STATUS_OVERDUE;
        } elseif ($this->status !== self::STATUS_ACTIVE) {
            $this->status = self::STATUS_ACTIVE;
        }

        $this->save();
    }
}
