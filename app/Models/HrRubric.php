<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HrRubric extends Model
{
    protected $table = 'hr_rubrics';

    protected $fillable = [
        'name',
        'criteria_json',
        'is_default',
        'created_by',
    ];

    protected $casts = [
        'criteria_json' => 'array',
        'is_default' => 'boolean',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scores(): HasMany
    {
        return $this->hasMany(HrApplicationScore::class, 'rubric_id');
    }

    /**
     * @return list<array{key: string, label: string, weight: float, max: float}>
     */
    public static function defaultCriteriaTemplate(): array
    {
        return [
            ['key' => 'experience', 'label' => 'الخبرة', 'weight' => 1, 'max' => 10],
            ['key' => 'skills', 'label' => 'المهارات', 'weight' => 1, 'max' => 10],
            ['key' => 'education', 'label' => 'التعليم', 'weight' => 1, 'max' => 10],
            ['key' => 'communication', 'label' => 'التواصل', 'weight' => 1, 'max' => 10],
        ];
    }

    /**
     * @return list<array{key: string, label: string, weight: float, max: float}>
     */
    public function normalizedCriteria(): array
    {
        $raw = $this->criteria_json;

        if (is_string($raw)) {
            $raw = json_decode($raw, true);
        }

        if (! is_array($raw) || $raw === []) {
            return [];
        }

        $out = [];

        foreach ($raw as $c) {
            if (! is_array($c)) {
                continue;
            }

            $key = trim((string) ($c['key'] ?? ''));
            if ($key === '') {
                continue;
            }

            $out[] = [
                'key' => $key,
                'label' => trim((string) ($c['label'] ?? $key)),
                'weight' => max(0.0, (float) ($c['weight'] ?? 1)),
                'max' => max(0.1, (float) ($c['max'] ?? 10)),
            ];
        }

        return $out;
    }

    public static function ensureDefaultExists(): HrRubric
    {
        $default = static::query()->where('is_default', true)->first();

        if (! $default) {
            return static::create([
                'name' => 'قالب التقييم الافتراضي',
                'criteria_json' => static::defaultCriteriaTemplate(),
                'is_default' => true,
                'created_by' => auth()->id(),
            ]);
        }

        if ($default->normalizedCriteria() === []) {
            $default->update(['criteria_json' => static::defaultCriteriaTemplate()]);
            $default->refresh();
        }

        return $default;
    }
}

