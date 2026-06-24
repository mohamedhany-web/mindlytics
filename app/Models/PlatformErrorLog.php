<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlatformErrorLog extends Model
{
    public const STATUSES = [
        'open' => 'مفتوح',
        'acknowledged' => 'قيد المعالجة',
        'resolved' => 'تم الحل',
    ];

    public const LEVELS = [
        'critical' => 'حرج',
        'alert' => 'تنبيه',
        'error' => 'خطأ',
        'warning' => 'تحذير',
    ];

    protected $fillable = [
        'user_id',
        'level',
        'status',
        'fingerprint',
        'exception_class',
        'message',
        'file',
        'line',
        'trace',
        'url',
        'method',
        'ip_address',
        'user_agent',
        'context',
        'request_input',
        'admin_notes',
        'resolved_by',
        'resolved_at',
        'acknowledged_at',
    ];

    protected function casts(): array
    {
        return [
            'context' => 'array',
            'request_input' => 'array',
            'resolved_at' => 'datetime',
            'acknowledged_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withDefault(['name' => 'زائر / غير مسجّل']);
    }

    public function resolver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }

    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    public function scopeUnresolved($query)
    {
        return $query->whereIn('status', ['open', 'acknowledged']);
    }

    public static function statusLabel(string $status): string
    {
        return self::STATUSES[$status] ?? $status;
    }

    public static function levelLabel(string $level): string
    {
        return self::LEVELS[$level] ?? $level;
    }

    public function shortLocation(): ?string
    {
        if (! $this->file) {
            return null;
        }

        $base = base_path();
        $path = str_starts_with($this->file, $base)
            ? ltrim(substr($this->file, strlen($base)), '\\/')
            : $this->file;

        return $this->line ? "{$path}:{$this->line}" : $path;
    }
}
