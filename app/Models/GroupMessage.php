<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'group_id',
        'user_id',
        'body',
        'reply_to_id',
        'kind',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function replyTo()
    {
        return $this->belongsTo(GroupMessage::class, 'reply_to_id');
    }
}
