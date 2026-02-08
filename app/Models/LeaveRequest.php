<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeaveRequest extends Model
{
    protected $fillable = [
        'employee_id',
        'type',
        'start_date',
        'end_date',
        'days',
        'reason',
        'status',
        'reviewed_by',
        'admin_notes',
        'reviewed_at',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'days' => 'integer',
        'reviewed_at' => 'datetime',
    ];

    /**
     * علاقة مع الموظف
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'employee_id');
    }

    /**
     * علاقة مع المراجع
     */
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * Scope للطلبات المعلقة
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope للطلبات الموافق عليها
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope للطلبات المرفوضة
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    /**
     * الحصول على نص نوع الإجازة
     */
    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            'annual' => 'سنوية',
            'sick' => 'مرضية',
            'emergency' => 'طارئة',
            'unpaid' => 'بدون راتب',
            'other' => 'أخرى',
            default => $this->type,
        };
    }

    /**
     * الحصول على نص الحالة
     */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'pending' => 'قيد المراجعة',
            'approved' => 'موافق عليها',
            'rejected' => 'مرفوضة',
            'cancelled' => 'ملغاة',
            default => $this->status,
        };
    }

    /**
     * الحصول على لون الحالة
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'pending' => 'yellow',
            'approved' => 'green',
            'rejected' => 'red',
            'cancelled' => 'gray',
            default => 'gray',
        };
    }
}
