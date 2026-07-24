<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    use HasFactory;

    protected $fillable = [
        'certificate_number',
        'serial_number',
        'user_id',
        'course_id',
        'course_name',
        'certificate_type',
        'issue_date',
        'expiry_date',
        'template',
        'pdf_path',
        'qr_code_path',
        'verification_code',
        'verification_url',
        'metadata',
        'is_verified',
        'is_public',
        'title',
        'description',
        'status',
        'issued_at',
        'certified_at',
        'certificate_hash',
        'academy_signature',
        'academy_signature_name',
        'academy_signature_title',
        'logo_path',
        'stamp_path',
        'instructor_id',
        'instructor_signature',
        'instructor_signature_name',
        'instructor_signature_title',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'issued_at' => 'date',
        'expiry_date' => 'date',
        'certified_at' => 'datetime',
        'metadata' => 'array',
        'is_verified' => 'boolean',
        'is_public' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function course()
    {
        return $this->belongsTo(AdvancedCourse::class, 'course_id');
    }

    public function instructor()
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    public function isExpired()
    {
        return $this->expiry_date && $this->expiry_date < now();
    }

    /**
     * Generate unique serial number
     */
    public static function generateSerialNumber()
    {
        $maxAttempts = 100;
        $attempt = 0;
        
        do {
            $serial = 'MIND-' . date('Y') . '-' . strtoupper(substr(uniqid(), -8)) . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
            $exists = self::whereNotNull('serial_number')->where('serial_number', $serial)->exists();
            $attempt++;
            
            if ($attempt >= $maxAttempts) {
                // Fallback: use timestamp if we can't generate unique serial
                $serial = 'MIND-' . date('Y') . '-' . time() . '-' . rand(1000, 9999);
                break;
            }
        } while ($exists);
        
        return $serial;
    }

    /**
     * Available certificate templates (achievement family only).
     */
    public static function availableTemplates(): array
    {
        return [
            'achievement' => [
                'name' => 'Certificate of Achievement',
                'description' => 'التصميم الأساسي — Navy / Teal / Gold',
                'theme' => 'navy',
            ],
            'achievement-teal' => [
                'name' => 'Achievement Teal',
                'description' => 'نفس التصميم بلمسة Teal أوضح',
                'theme' => 'teal',
            ],
            'achievement-navy' => [
                'name' => 'Achievement Navy',
                'description' => 'نفس التصميم بنسخة Navy أغمق',
                'theme' => 'deep',
            ],
        ];
    }

    /**
     * Resolve public URL for certificate asset path.
     */
    public function assetUrl(?string $path): ?string
    {
        if (! $path) {
            return null;
        }
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://') || str_starts_with($path, '/')) {
            return $path;
        }

        return asset('storage/' . ltrim($path, '/'));
    }

    public function logoUrl(?\App\Models\CertificateBranding $branding = null): ?string
    {
        return $this->assetUrl($this->logo_path)
            ?: ($branding?->logoUrl());
    }

    public function signatureImageUrl(?\App\Models\CertificateBranding $branding = null): ?string
    {
        return $this->assetUrl($this->academy_signature)
            ?: ($branding?->signatureUrl());
    }

    public function stampUrl(?\App\Models\CertificateBranding $branding = null): ?string
    {
        return $this->assetUrl($this->stamp_path)
            ?: ($branding?->stampUrl());
    }

    /**
     * Generate certificate hash for verification
     */
    public function generateHash()
    {
        $data = [
            'certificate_number' => $this->certificate_number,
            'serial_number' => $this->serial_number,
            'user_id' => $this->user_id,
            'course_id' => $this->course_id,
            'issue_date' => $this->issue_date?->format('Y-m-d'),
            'verification_code' => $this->verification_code,
        ];
        
        return hash('sha256', json_encode($data) . config('app.key'));
    }

    /**
     * Verify certificate hash
     */
    public function verifyHash()
    {
        return $this->certificate_hash === $this->generateHash();
    }

    /**
     * Get verification URL
     */
    public function getVerificationUrlAttribute()
    {
        if ($this->attributes['verification_url'] ?? null) {
            return $this->attributes['verification_url'];
        }
        
        return route('public.certificates.verify', ['code' => $this->verification_code]);
    }
}
