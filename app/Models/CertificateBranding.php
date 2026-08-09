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
            try {
                if (\Illuminate\Support\Facades\Schema::hasColumn('certificate_brandings', 'tax_number')
                    && blank($row->tax_number)) {
                    $row->forceFill(['tax_number' => '774-128-949'])->save();
                }
            } catch (\Throwable) {
                // ignore until migration is applied
            }

            return $row;
        }

        $payload = [
            'academy_name' => config('app.name', 'Mindlytics Academy'),
            'academy_tagline' => 'أكاديمية البرمجة',
            'signature_name' => 'المدير العام',
            'signature_title' => 'Mindlytics Academy',
            'seal_label' => 'CERTIFICATION',
            'seal_since' => '2020',
            'default_template' => 'emerald-classic',
        ];

        try {
            if (\Illuminate\Support\Facades\Schema::hasColumn('certificate_brandings', 'tax_number')) {
                $payload['tax_number'] = '774-128-949';
            }
            if (\Illuminate\Support\Facades\Schema::hasColumn('certificate_brandings', 'stamp_enabled')) {
                $payload['stamp_enabled'] = true;
            }
        } catch (\Throwable) {
            // ignore
        }

        return static::create($payload);
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
