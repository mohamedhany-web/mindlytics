<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class HrJobApplication extends Model
{
    protected $table = 'hr_job_applications';

    public const STATUSES = [
        'applied' => 'تم التقديم',
        'under_review' => 'قيد المراجعة',
        'interview' => 'مقابلة',
        'accepted' => 'مقبول',
        'rejected' => 'مرفوض',
    ];

    protected $fillable = [
        'job_posting_id',
        'full_name',
        'email',
        'phone',
        'linkedin_url',
        'portfolio_url',
        'cover_letter',
        'parsed_skills',
        'parsed_education',
        'parsed_experience_years',
        'skills_score',
        'experience_score',
        'education_score',
        'auto_score',
        'scoring_notes',
        'scored_at',
        'status',
        'source',
        'submitted_at',
    ];

    protected $casts = [
        'parsed_skills' => 'array',
        'parsed_experience_years' => 'decimal:1',
        'skills_score' => 'decimal:2',
        'experience_score' => 'decimal:2',
        'education_score' => 'decimal:2',
        'auto_score' => 'decimal:2',
        'scoring_notes' => 'array',
        'scored_at' => 'datetime',
        'submitted_at' => 'datetime',
    ];

    public function job(): BelongsTo
    {
        return $this->belongsTo(HrJobPosting::class, 'job_posting_id');
    }

    public function files(): HasMany
    {
        return $this->hasMany(HrApplicationFile::class, 'job_application_id');
    }

    public function cvFile(): HasOne
    {
        return $this->hasOne(HrApplicationFile::class, 'job_application_id')->where('kind', 'cv');
    }

    public function score(): HasOne
    {
        return $this->hasOne(HrApplicationScore::class, 'job_application_id');
    }

    public function skills(): HasMany
    {
        return $this->hasMany(HrApplicationSkill::class, 'job_application_id');
    }

    /**
     * @return list<string>
     */
    public function normalizedParsedSkills(): array
    {
        $fromJson = is_array($this->parsed_skills) ? $this->parsed_skills : [];

        if ($fromJson !== []) {
            return array_values(array_unique(array_filter(array_map(
                fn ($s) => trim((string) $s),
                $fromJson
            ))));
        }

        return $this->skills()->pluck('skill_name')->map(fn ($s) => trim((string) $s))->filter()->unique()->values()->all();
    }

    public function displayScore(): ?float
    {
        return $this->auto_score !== null ? (float) $this->auto_score : null;
    }

    public function parsedEducationLabel(): string
    {
        if (! $this->parsed_education) {
            return '—';
        }

        return config('hr.education_levels.'.$this->parsed_education.'.label', $this->parsed_education);
    }
}
