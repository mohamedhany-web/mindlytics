<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EmployeeTask extends Model
{
    protected $fillable = [
        'employee_id',
        'assigned_by',
        'title',
        'description',
        'priority',
        'status',
        'deadline',
        'started_at',
        'completed_at',
        'progress',
        'notes',
    ];

    protected $casts = [
        'deadline' => 'date',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'progress' => 'integer',
    ];

    /**
     * علاقة مع الموظف
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'employee_id');
    }

    /**
     * علاقة مع المكلف
     */
    public function assigner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    /**
     * علاقة مع التسليمات
     */
    public function deliverables(): HasMany
    {
        return $this->hasMany(EmployeeTaskDeliverable::class, 'task_id');
    }

    /**
     * Scope للمهام المعلقة
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope للمهام قيد التنفيذ
     */
    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    /**
     * Scope للمهام المكتملة
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope للمهام المتأخرة
     */
    public function scopeOverdue($query)
    {
        return $query->where('deadline', '<', now())
                    ->whereIn('status', ['pending', 'in_progress']);
    }
}
