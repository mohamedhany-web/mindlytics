<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SalesPolicySection extends Model
{
    protected $fillable = [
        'title',
        'title_en',
        'rules_range',
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

    public function rules(): HasMany
    {
        return $this->hasMany(SalesPolicyRule::class, 'section_id')->orderBy('sort_order')->orderBy('id');
    }

    public function activeRules(): HasMany
    {
        return $this->rules()->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
