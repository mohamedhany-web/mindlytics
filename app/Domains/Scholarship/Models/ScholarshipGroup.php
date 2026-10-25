<?php

namespace App\Domains\Scholarship\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\User;

class ScholarshipGroup extends Model
{
    protected $fillable = [
        'scholarship_program_id',
        'name',
        'description',
        'created_by',
    ];

    public function program(): BelongsTo
    {
        return $this->belongsTo(ScholarshipProgram::class, 'scholarship_program_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'scholarship_group_members')
            ->withTimestamps();
    }
}
