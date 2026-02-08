<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EmployeeJob extends Model
{
    protected $fillable = [
        'name',
        'code',
        'description',
        'responsibilities',
        'permissions',
        'min_salary',
        'max_salary',
        'is_active',
    ];

    protected $casts = [
        'permissions' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * علاقة مع الموظفين
     */
    public function employees(): HasMany
    {
        return $this->hasMany(User::class, 'employee_job_id');
    }

    /**
     * Scope للوظائف النشطة
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
