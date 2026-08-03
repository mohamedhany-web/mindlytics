<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalesUserSpecialty extends Model
{
    protected $fillable = [
        'user_id',
        'interest_type_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function interestType(): BelongsTo
    {
        return $this->belongsTo(SalesInterestType::class, 'interest_type_id');
    }
}
