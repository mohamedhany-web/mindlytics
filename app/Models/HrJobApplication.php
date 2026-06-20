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
        'new' => 'جديد',
        'screening' => 'فرز',
        'interview' => 'مقابلة',
        'offer' => 'عرض',
        'hired' => 'توظيف',
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
        'status',
        'source',
        'submitted_at',
    ];

    protected $casts = [
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
}

