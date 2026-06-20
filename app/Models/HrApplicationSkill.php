<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HrApplicationSkill extends Model
{
    protected $table = 'hr_application_skills';

    protected $fillable = [
        'job_application_id',
        'skill_name',
    ];

    public function application(): BelongsTo
    {
        return $this->belongsTo(HrJobApplication::class, 'job_application_id');
    }
}
