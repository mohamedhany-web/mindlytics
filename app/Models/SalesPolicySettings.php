<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesPolicySettings extends Model
{
    protected $fillable = [
        'version',
        'effective_date',
        'document_title',
        'document_title_en',
        'intro_content',
        'acknowledgement_content',
    ];

    protected function casts(): array
    {
        return [
            'effective_date' => 'date',
        ];
    }

    public static function current(): self
    {
        return static::query()->firstOrCreate([], [
            'version' => '1.0',
            'document_title' => 'دليل قواعد وسياسات فريق المبيعات',
            'document_title_en' => 'Sales Team Policy, Attendance & Commission Manual',
        ]);
    }
}
