<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalesLeadTransfer extends Model
{
    protected $fillable = [
        'sales_lead_id',
        'from_user_id',
        'to_user_id',
        'transferred_by',
        'sales_team_id',
        'reason',
        'source',
        'interest_type_id',
    ];

    public function lead(): BelongsTo
    {
        return $this->belongsTo(SalesLead::class, 'sales_lead_id');
    }

    public function fromUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'from_user_id');
    }

    public function toUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'to_user_id');
    }

    public function transferredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'transferred_by');
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(SalesTeam::class, 'sales_team_id');
    }

    public function interestType(): BelongsTo
    {
        return $this->belongsTo(SalesInterestType::class, 'interest_type_id');
    }
}
