<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeTaskDeliverable extends Model
{
    protected $fillable = [
        'task_id',
        'title',
        'description',
        'delivery_type',
        'link_url',
        'file_path',
        'file_name',
        'file_type',
        'file_size',
        'status',
        'feedback',
        'reviewed_by',
        'reviewed_at',
        'submitted_at',
    ];

    protected $casts = [
        'file_size' => 'integer',
        'reviewed_at' => 'datetime',
        'submitted_at' => 'datetime',
    ];

    /**
     * علاقة مع المهمة
     */
    public function task(): BelongsTo
    {
        return $this->belongsTo(EmployeeTask::class, 'task_id');
    }

    /**
     * علاقة مع المراجع
     */
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * Scope للتسليمات المعلقة
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope للتسليمات المقدمة
     */
    public function scopeSubmitted($query)
    {
        return $query->where('status', 'submitted');
    }

    /**
     * Scope للتسليمات المعتمدة
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }
}
