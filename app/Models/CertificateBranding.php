<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class CertificateBranding extends Model
{
    protected $fillable = [
        'academy_name',
        'academy_tagline',
        'tax_number',
        'logo_path',
        'signature_path',
        'stamp_path',
        'stamp_enabled',
        'signature_name',
        'signature_title',
        'seal_label',
        'seal_since',
        'default_template',
    ];

    protected function casts(): array
    {
        return [
            'stamp_enabled' => 'boolean',
        ];
    }

    /**
     * Singleton branding settings used on all certificates.
     */
    public static function current(): self
    {
        $row = static::query()->first();
        if ($row) {
            if (! $row->tax_number) {
                $row->tax_number = '774-128-949';
                $row->save();
            }

            return $row;
        }

        return static::create([
            'academy_name' => config('app.name', 'Mindlytics Academy'),
            'academy_tagline' => 'أكاديمية البرمجة',
            'tax_number' => '774-128-949',
            'signature_name' => 'المدير العام',
            'signature_title' => 'Mindlytics Academy',
            'seal_label' => 'CERTIFICATION',
            'seal_since' => '2020',
            'stamp_enabled' => true,
            'default_template' => 'emerald-classic',
        ]);
    }

    public function logoUrl(): ?string
    {
        return $this->publicUrl($this->logo_path);
    }

    public function signatureUrl(): ?string
    {
        return $this->publicUrl($this->signature_path);
    }

    public function stampUrl(): ?string
    {
        return $this->publicUrl($this->stamp_path);
    }

    private function publicUrl(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://') || str_starts_with($path, '/')) {
            return $path;
        }

        return asset('storage/' . ltrim($path, '/'));
    }

    public function deleteAsset(string $field): void
    {
        $path = $this->{$field} ?? null;
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
        $this->{$field} = null;
        $this->save();
    }
}
