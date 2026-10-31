<?php

namespace App\Domains\Sales\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CrmCase extends Model
{
    public const STATUSES = [
        'new' => 'جديدة',
        'assigned' => 'مُسندة',
        'pending' => 'معلّقة',
        'closed' => 'مغلقة',
    ];

    public const PRIORITIES = [
        'low' => 'منخفضة',
        'medium' => 'متوسطة',
        'high' => 'مرتفعة',
        'urgent' => 'عاجلة',
    ];

    protected $fillable = [
        'subject', 'status', 'priority', 'type', 'description', 'resolution',
        'crm_account_id', 'crm_contact_id', 'sales_lead_id',
        'assigned_to', 'created_by', 'closed_at',
    ];

    protected function casts(): array
    {
        return ['closed_at' => 'datetime'];
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(CrmAccount::class, 'crm_account_id');
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(CrmContact::class, 'crm_contact_id');
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(SalesLead::class, 'sales_lead_id');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to')->withDefault(['name' => '—']);
    }

    public static function statusLabel(?string $status): string
    {
        return self::STATUSES[$status] ?? ($status ?: '—');
    }

    public static function priorityLabel(?string $priority): string
    {
        return self::PRIORITIES[$priority] ?? ($priority ?: '—');
    }

    public function isOpen(): bool
    {
        return $this->status !== 'closed';
    }
}
