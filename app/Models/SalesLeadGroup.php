<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SalesLeadGroup extends Model
{
    protected $fillable = [
        'name',
        'description',
        'assigned_to',
        'created_by',
        'is_admin_managed',
    ];

    protected function casts(): array
    {
        return [
            'is_admin_managed' => 'boolean',
        ];
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function leads(): HasMany
    {
        return $this->hasMany(SalesLead::class, 'sales_lead_group_id');
    }

    public function scopeForAssignee($query, int $userId)
    {
        return $query->where('assigned_to', $userId);
    }
}
