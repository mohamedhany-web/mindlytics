<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class WhatsAppMetaTemplate extends Model
{
    protected $table = 'whatsapp_meta_templates';

    public const STATUS_DRAFT = 'draft';

    public const STATUS_PENDING = 'pending';

    public const STATUS_APPROVED = 'approved';

    public const STATUS_REJECTED = 'rejected';

    public const STATUS_PAUSED = 'paused';

    public const STATUS_DISABLED = 'disabled';

    protected $fillable = [
        'meta_template_id',
        'name',
        'language',
        'category',
        'status',
        'body_text',
        'header_type',
        'header_content',
        'footer_text',
        'buttons',
        'body_variable_count',
        'components',
        'rejection_reason',
        'quality_score',
        'submitted_at',
        'meta_synced_at',
        'created_by',
    ];

    protected $casts = [
        'buttons' => 'array',
        'components' => 'array',
        'submitted_at' => 'datetime',
        'meta_synced_at' => 'datetime',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function assignedUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'whatsapp_meta_template_user')
            ->withTimestamps();
    }

    public function statusLabel(): string
    {
        return self::statusLabels()[$this->status] ?? $this->status;
    }

    public function categoryLabel(): string
    {
        return self::categoryLabels()[$this->category] ?? $this->category;
    }

    public function isEditable(): bool
    {
        return in_array($this->status, [self::STATUS_DRAFT, self::STATUS_REJECTED], true);
    }

    public function isSendable(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    public function displayLabel(): string
    {
        return $this->name . ' · ' . $this->language;
    }

    /**
     * @return array<string, string>
     */
    public static function statusLabels(): array
    {
        return [
            self::STATUS_DRAFT => 'مسودة',
            self::STATUS_PENDING => 'قيد المراجعة',
            self::STATUS_APPROVED => 'معتمد',
            self::STATUS_REJECTED => 'مرفوض',
            self::STATUS_PAUSED => 'موقوف',
            self::STATUS_DISABLED => 'معطّل',
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function categoryLabels(): array
    {
        return [
            'AUTHENTICATION' => 'مصادقة (Authentication)',
            'UTILITY' => 'خدمي (Utility)',
            'MARKETING' => 'تسويقي (Marketing)',
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function languageOptions(): array
    {
        return [
            'ar' => 'العربية (ar)',
            'en' => 'English (en)',
            'en_US' => 'English US (en_US)',
            'en_GB' => 'English UK (en_GB)',
            'fr' => 'Français (fr)',
            'de' => 'Deutsch (de)',
            'es' => 'Español (es)',
            'tr' => 'Türkçe (tr)',
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function headerTypeLabels(): array
    {
        return [
            '' => 'بدون Header',
            'text' => 'نص (Text)',
            'image' => 'صورة (Image)',
            'video' => 'فيديو (Video)',
            'document' => 'مستند (Document)',
        ];
    }

    public static function normalizeMetaStatus(?string $status): string
    {
        return match (strtoupper((string) $status)) {
            'APPROVED' => self::STATUS_APPROVED,
            'PENDING' => self::STATUS_PENDING,
            'REJECTED' => self::STATUS_REJECTED,
            'PAUSED' => self::STATUS_PAUSED,
            'DISABLED' => self::STATUS_DISABLED,
            default => self::STATUS_PENDING,
        };
    }
}
