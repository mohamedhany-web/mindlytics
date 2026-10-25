<?php

namespace App\Domains\Community\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class DirectMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'thread_id',
        'sender_id',
        'body',
    ];

    public function thread()
    {
        return $this->belongsTo(DirectMessageThread::class, 'thread_id');
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }
}

