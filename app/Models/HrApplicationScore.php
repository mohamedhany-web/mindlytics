<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HrApplicationScore extends Model
{
    protected $table = 'hr_application_scores';

    protected $fillable = [
        'job_application_id',
        'rubric_id',
        'scores_json',
        'total_score',
        'notes',
        'scored_by',
        'scored_at',
    ];

    protected $casts = [
        'scores_json' => 'array',
        'total_score' => 'decimal:2',
        'scored_at' => 'datetime',
    ];

    public function application(): BelongsTo
    {
        return $this->belongsTo(HrJobApplication::class, 'job_application_id');
    }

    public function rubric(): BelongsTo
    {
        return $this->belongsTo(HrRubric::class, 'rubric_id');
    }

    public function scorer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'scored_by');
    }
}

