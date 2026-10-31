<?php

namespace App\Domains\Sales\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CrmFeatureAccess extends Model
{
    protected $table = 'crm_feature_access';

    protected $fillable = [
        'user_id',
        'feature_key',
        'allowed',
    ];

    protected function casts(): array
    {
        return [
            'allowed' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
