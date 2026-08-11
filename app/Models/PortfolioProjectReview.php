<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PortfolioProjectReview extends Model
{
    public const DECISION_APPROVED = 'approved';

    public const DECISION_REJECTED = 'rejected';

    public const DECISION_CHANGES_REQUESTED = 'changes_requested';

    public const DECISION_PUBLISHED = 'published';

    protected $fillable = [
        'portfolio_project_id',
        'reviewed_by',
        'decision',
        'score_code_quality',
        'score_ui_ux',
        'score_functionality',
        'score_problem_solving',
        'score_documentation',
        'score_average',
        'instructor_notes',
        'rejected_reason',
    ];

    protected $casts = [
        'score_average' => 'float',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(PortfolioProject::class, 'portfolio_project_id');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
