<?php

namespace App\Domains\Sales\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** فلتر محفوظ لمستخدم في قايمة من قوايم الـ CRM (حالياً العملاء المحتملون). */
class CrmSavedFilter extends Model
{
    /** الوحدات المسموح فيها بالحفظ، والـ query params اللي بتتحفظ لكل وحدة. */
    public const MODULES = [
        'leads' => ['quick', 'q', 'stage', 'source', 'interest_type_id', 'assigned_to', 'sort', 'dir'],
    ];

    protected $fillable = ['user_id', 'module', 'name', 'params'];

    protected function casts(): array
    {
        return ['params' => 'array'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** @param array<string, mixed> $query */
    public static function cleanParams(string $module, array $query): array
    {
        $allowed = self::MODULES[$module] ?? [];

        return array_filter(
            array_intersect_key($query, array_flip($allowed)),
            fn ($v) => is_scalar($v) && trim((string) $v) !== ''
        );
    }
}
