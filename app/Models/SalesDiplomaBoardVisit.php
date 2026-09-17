<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SalesDiplomaBoardVisit extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'sales_diploma_board_entry_id',
        'user_id',
        'ip_address',
        'user_agent',
        'referer',
        'accept_language',
        'session_id',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }

    public function entry(): BelongsTo
    {
        return $this->belongsTo(SalesDiplomaBoardEntry::class, 'sales_diploma_board_entry_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function capture(SalesDiplomaBoardEntry $entry, Request $request): self
    {
        $ua = $request->userAgent();
        $referer = $request->headers->get('referer');
        $lang = $request->headers->get('accept-language');

        return static::query()->create([
            'sales_diploma_board_entry_id' => $entry->id,
            'user_id' => $request->user()?->id,
            'ip_address' => $request->ip(),
            'user_agent' => $ua ? Str::limit($ua, 2000, '') : null,
            'referer' => $referer ? Str::limit($referer, 2048, '') : null,
            'accept_language' => $lang ? Str::limit($lang, 255, '') : null,
            'session_id' => $request->hasSession() ? (string) $request->session()->getId() : null,
            'created_at' => now(),
        ]);
    }
}
