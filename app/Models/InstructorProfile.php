<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InstructorProfile extends Model
{
    const STATUS_DRAFT = 'draft';
    const STATUS_PENDING_REVIEW = 'pending_review';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';

    protected $fillable = [
        'user_id',
        'headline',
        'bio',
        'photo_path',
        'experience',
        'skills',
        'social_links',
        'status',
        'rejection_reason',
        'reviewed_at',
        'reviewed_by',
        'submitted_at',
    ];

    protected $casts = [
        'social_links' => 'array',
        'reviewed_at' => 'datetime',
        'submitted_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reviewedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING_REVIEW);
    }

    /**
     * رابط صورة الملف التعريفي — باستخدام asset('storage/...') لضمان ظهور الصورة في كل الصفحات.
     */
    public function getPhotoUrlAttribute(): ?string
    {
        if (empty($this->photo_path)) {
            return null;
        }
        $path = str_replace('\\', '/', trim($this->photo_path));
        $path = ltrim($path, '/');
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }
        return asset('storage/' . $path);
    }

    public static function statusLabel(string $status): string
    {
        return match ($status) {
            self::STATUS_DRAFT => 'مسودة',
            self::STATUS_PENDING_REVIEW => 'قيد المراجعة',
            self::STATUS_APPROVED => 'معتمد',
            self::STATUS_REJECTED => 'مرفوض',
            default => $status,
        };
    }

    /**
     * المهارات كقائمة مرتبة (سطر لكل مهارة أو مفصولة بفاصلة)
     */
    public function getSkillsListAttribute(): array
    {
        if (empty($this->skills)) {
            return [];
        }
        $raw = preg_split('/[\r\n,،]+/u', $this->skills, -1, PREG_SPLIT_NO_EMPTY);
        $list = array_map('trim', $raw);
        return array_values(array_filter($list));
    }
}
