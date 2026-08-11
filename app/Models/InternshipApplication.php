<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InternshipApplication extends Model
{
    public const STATUS_PENDING = 'pending';

    public const STATUS_REVIEWED = 'reviewed';

    public const STATUS_ACCEPTED = 'accepted';

    public const STATUS_REJECTED = 'rejected';

    protected $fillable = [
        'internship_id',
        'name',
        'email',
        'phone',
        'university',
        'major',
        'year_of_study',
        'cv_path',
        'portfolio_url',
        'github_url',
        'linkedin_url',
        'cover_letter',
        'status',
        'admin_notes',
        'reviewed_at',
        'reviewed_by',
        'source',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
    ];

    public static function statuses(): array
    {
        return [
            self::STATUS_PENDING => 'قيد الانتظار',
            self::STATUS_REVIEWED => 'تمت المراجعة',
            self::STATUS_ACCEPTED => 'مقبول',
            self::STATUS_REJECTED => 'مرفوض',
        ];
    }

    public function internship(): BelongsTo
    {
        return $this->belongsTo(Internship::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function statusLabel(): string
    {
        return self::statuses()[$this->status] ?? $this->status;
    }

    public function cvUrl(): ?string
    {
        return $this->cv_path ? asset($this->cv_path) : null;
    }
}
