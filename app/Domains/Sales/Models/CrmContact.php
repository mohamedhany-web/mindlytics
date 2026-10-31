<?php

namespace App\Domains\Sales\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CrmContact extends Model
{
    protected $fillable = [
        'first_name', 'last_name', 'name', 'title', 'phone', 'email',
        'crm_account_id', 'student_user_id', 'sales_lead_id', 'description',
        'assigned_to', 'created_by',
    ];

    protected static function booted(): void
    {
        static::saving(function (CrmContact $contact) {
            if (! filled($contact->name)) {
                $contact->name = trim(($contact->first_name ?? '').' '.($contact->last_name ?? '')) ?: 'جهة اتصال';
            }
        });
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(CrmAccount::class, 'crm_account_id');
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(SalesLead::class, 'sales_lead_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_user_id');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to')->withDefault(['name' => '—']);
    }

    public function cases(): HasMany
    {
        return $this->hasMany(CrmCase::class);
    }
}
