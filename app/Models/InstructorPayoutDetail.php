<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InstructorPayoutDetail extends Model
{
    protected $fillable = [
        'user_id',
        'bank_name',
        'account_holder_name',
        'account_number',
        'iban',
        'branch_name',
        'swift_code',
        'notes',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function hasAnyDetails(): bool
    {
        return !empty($this->bank_name) || !empty($this->account_number) || !empty($this->iban);
    }
}
