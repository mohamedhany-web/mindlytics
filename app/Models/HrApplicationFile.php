<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HrApplicationFile extends Model
{
    protected $table = 'hr_application_files';

    protected $fillable = [
        'job_application_id',
        'kind',
        'disk',
        'path',
        'original_name',
        'mime',
        'size',
    ];

    public function application(): BelongsTo
    {
        return $this->belongsTo(HrJobApplication::class, 'job_application_id');
    }

    public function asStoredUploadArray(): array
    {
        return [
            'disk' => $this->disk,
            'path' => $this->path,
            'name' => $this->original_name ?: basename((string) $this->path),
        ];
    }
}

