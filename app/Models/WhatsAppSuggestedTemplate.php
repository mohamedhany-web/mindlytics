<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WhatsAppSuggestedTemplate extends Model
{
    protected $table = 'whatsapp_suggested_templates';

    protected $fillable = [
        'key',
        'title',
        'category',
        'language',
        'body',
        'help',
        'variables',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'variables' => 'array',
        'is_active' => 'boolean',
    ];
}

