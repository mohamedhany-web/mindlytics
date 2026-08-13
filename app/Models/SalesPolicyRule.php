<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalesPolicyRule extends Model
{
    protected $fillable = [
        'section_id',
        'rule_number',
        'title',
        'title_en',
        'content',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(SalesPolicySection::class, 'section_id');
    }

    public function displayNumber(): string
    {
        return filled($this->rule_number) ? (string) $this->rule_number : '—';
    }
}
