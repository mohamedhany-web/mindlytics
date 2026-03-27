<?php

namespace App\Models;

use App\Support\MontageVideoHelper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeTaskDeliverable extends Model
{
    protected $fillable = [
        'task_id',
        'title',
        'description',
        'received_from',
        'duration_before',
        'duration_after',
        'duration_before_minutes',
        'duration_after_minutes',
        'delivery_type',
        'link_url',
        'link_url_hash',
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
        'duration_before_minutes' => 'integer',
        'duration_after_minutes' => 'integer',
        'reviewed_at' => 'datetime',
        'submitted_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::saving(function (EmployeeTaskDeliverable $d) {
            if ($d->delivery_type === 'link' && $d->link_url) {
                $d->link_url_hash = MontageVideoHelper::linkUrlHash($d->link_url);
            } else {
                $d->link_url_hash = null;
            }
        });
    }

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
