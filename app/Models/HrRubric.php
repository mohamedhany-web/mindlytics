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
}

