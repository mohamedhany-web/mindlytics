<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WhatsAppConversation extends Model
{
    protected $table = 'whatsapp_conversations';

    public const STATUS_OPEN = 'open';

    public const STATUS_PENDING = 'pending';

    public const STATUS_WAITING_CUSTOMER = 'waiting_customer';

    public const STATUS_RESOLVED = 'resolved';

    public const STATUS_CLOSED = 'closed';

    public const STATUSES = [
        self::STATUS_OPEN => 'مفتوحة',
        self::STATUS_PENDING => 'قيد المعالجة',
        self::STATUS_WAITING_CUSTOMER => 'بانتظار العميل',
        self::STATUS_RESOLVED => 'تم الحل',
        self::STATUS_CLOSED => 'مغلقة',
    ];

    public const DEPARTMENTS = [
        'sales' => 'المبيعات',
        'support' => 'الدعم',
        'finance' => 'المالية',
        'general' => 'عام',
    ];

    protected $fillable = [
        'phone_number',
        'contact_name',
        'user_id',
        'contact_id',
        'assigned_to',
        'sales_lead_id',
        'status',
        'department',
        'priority',
        'closed_at',
        'last_message_at',
        'last_message_preview',
        'last_message_direction',
        'unread_count',
        'meta',
    ];

    protected $casts = [
        'last_message_at' => 'datetime',
        'closed_at' => 'datetime',
        'meta' => 'array',
        'unread_count' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(WhatsAppContact::class, 'contact_id');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function salesLead(): BelongsTo
    {
        return $this->belongsTo(SalesLead::class, 'sales_lead_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(WhatsAppConversationMessage::class, 'conversation_id');
    }

    public function notes(): HasMany
    {
        return $this->hasMany(WhatsAppConversationNote::class, 'conversation_id');
    }

    public function events(): HasMany
    {
        return $this->hasMany(WhatsAppConversationEvent::class, 'conversation_id');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(WhatsAppTag::class, 'whatsapp_conversation_tag', 'conversation_id', 'tag_id')
            ->withPivot('tagged_by', 'created_at');
    }

    public function scopeOwnedBySalesAgent(Builder $query, User|int $user): Builder
    {
        $userId = $user instanceof User ? (int) $user->id : (int) $user;

        return $query->where(function (Builder $q) use ($userId) {
            $q->where('assigned_to', $userId)
                ->orWhereHas('salesLead', fn (Builder $lq) => $lq->where('assigned_to', $userId));
        });
    }

    public function isOwnedBySalesAgent(int $userId): bool
    {
        if ((int) $this->assigned_to === $userId) {
            return true;
        }

        $this->loadMissing('salesLead');

        return $this->sales_lead_id && (int) $this->salesLead?->assigned_to === $userId;
    }

    public function scopeVisibleTo(Builder $query, ?User $user): Builder
    {
        if (! $user) {
            return $query;
        }

        if (in_array($user->role, ['admin', 'super_admin'], true)) {
            return $query;
        }

        if ($user->isSalesEmployee()) {
            return $query->ownedBySalesAgent($user);
        }

        if ($user->isSalesManager()) {
            $memberIds = app(\App\Services\SalesTeamService::class)->visibleAssigneeIds($user);
            $memberIds[] = (int) $user->id;

            return $query->where(function (Builder $q) use ($memberIds) {
                $q->whereIn('assigned_to', $memberIds)
                    ->orWhereHas('salesLead', fn (Builder $lq) => $lq->whereIn('assigned_to', $memberIds));
            });
        }

        return $query->where(function (Builder $q) use ($user) {
            $q->where('assigned_to', $user->id)
                ->orWhereNull('assigned_to');
        });
    }

    public function scopeInSalesQueue(Builder $query): Builder
    {
        return $query
            ->whereNull('assigned_to')
            ->where(function (Builder $q) {
                $q->whereNull('sales_lead_id')
                    ->orWhereHas('salesLead', fn (Builder $lq) => $lq->whereNull('assigned_to'));
            })
            ->where(function (Builder $q) {
                $q->where('department', 'sales')->orWhereNull('department');
            })
            ->where(function (Builder $q) {
                $q->whereNull('status')
                    ->orWhereIn('status', [self::STATUS_OPEN, self::STATUS_PENDING]);
            });
    }

    public function scopeFilterCrm(Builder $query, array $filters): Builder
    {
        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (! empty($filters['department'])) {
            $query->where('department', $filters['department']);
        }

        if (! empty($filters['assigned_to'])) {
            if ($filters['assigned_to'] === 'unassigned') {
                $query->whereNull('assigned_to');
            } else {
                $query->where('assigned_to', (int) $filters['assigned_to']);
            }
        }

        if (! empty($filters['sales_owned']) && auth()->check()) {
            $query->ownedBySalesAgent(auth()->user());
        }

        if (! empty($filters['mine']) && auth()->check()) {
            $query->where('assigned_to', auth()->id());
        }

        if (! empty($filters['tag_id'])) {
            $query->whereHas('tags', fn (Builder $q) => $q->where('whatsapp_tags.id', (int) $filters['tag_id']));
        }

        return $query;
    }

    public function displayName(): string
    {
        if ($this->contact_name) {
            return $this->contact_name;
        }

        if ($this->contact?->displayName()) {
            return $this->contact->displayName();
        }

        if ($this->salesLead?->name) {
            return $this->salesLead->name;
        }

        if ($this->user?->name) {
            return $this->user->name;
        }

        return '+' . $this->phone_number;
    }

    public function formattedPhone(): string
    {
        return '+' . $this->phone_number;
    }

    public static function statusLabel(?string $status): string
    {
        return self::STATUSES[$status ?? ''] ?? ($status ?? '—');
    }

    public static function departmentLabel(?string $department): string
    {
        return self::DEPARTMENTS[$department ?? ''] ?? ($department ?? '—');
    }
}
