<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'advanced_course_id',
        'academic_year_id',
        'coupon_id',
        'original_amount',
        'discount_amount',
        'amount',
        'payment_method',
        'wallet_id',
        'payment_proof',
        'invoice_id',
        'payment_id',
        'status',
        'notes',
        'approved_at',
        'approved_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'original_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'approved_at' => 'datetime',
    ];

    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function course()
    {
        return $this->belongsTo(AdvancedCourse::class, 'advanced_course_id');
    }

    public function learningPath()
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    public function scopeRejected($query)
    {
        return $query->where('status', self::STATUS_REJECTED);
    }

    public function getStatusTextAttribute()
    {
        $statuses = [
            self::STATUS_PENDING => 'في الانتظار',
            self::STATUS_APPROVED => 'مقبول',
            self::STATUS_REJECTED => 'مرفوض',
        ];

        return $statuses[$this->status] ?? 'غير محدد';
    }

    public function getStatusColorAttribute()
    {
        $colors = [
            self::STATUS_PENDING => 'yellow',
            self::STATUS_APPROVED => 'green',
            self::STATUS_REJECTED => 'red',
        ];

        return $colors[$this->status] ?? 'gray';
    }
}