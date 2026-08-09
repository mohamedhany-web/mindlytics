<?php

namespace App\Services;

use App\Models\AdvancedCourse;
use App\Models\Certificate;
use App\Models\CertificateBranding;
use App\Models\StudentCourseEnrollment;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CertificateIssueService
{
    /**
     * @return list<array{key:string,name:string,description:string,preview:string,rtl:bool}>
     */
    public static function designCatalog(): array
    {
        return [
            'emerald-classic' => [
                'key' => 'emerald-classic',
                'name' => 'Emerald Classic',
                'description' => 'أخضر زمردي مع شريط ذهبي كلاسيكي',
                'preview' => asset('certification/01-emerald-classic.html'),
                'rtl' => false,
            ],
            'navy-deco' => [
                'key' => 'navy-deco',
                'name' => 'Royal Navy Deco',
                'description' => 'كحلي آرت ديكو فاخر',
                'preview' => asset('certification/02-navy-deco.html'),
                'rtl' => false,
            ],
            'burgundy-academy' => [
                'key' => 'burgundy-academy',
                'name' => 'Burgundy Academy',
                'description' => 'أكاديمي بورجوندي تقليدي',
                'preview' => asset('certification/03-burgundy-academy.html'),
                'rtl' => false,
            ],
            'ivory-minimal' => [
                'key' => 'ivory-minimal',
                'name' => 'Ivory Minimal',
                'description' => 'بسيط وأنيق على خلفية عاجية',
                'preview' => asset('certification/04-ivory-minimal.html'),
                'rtl' => false,
            ],
            'midnight-champagne' => [
                'key' => 'midnight-champagne',
                'name' => 'Midnight Champagne',
                'description' => 'داكن مع لمسات شامبانيا',
                'preview' => asset('certification/05-midnight-champagne.html'),
                'rtl' => false,
            ],
            'tech-gradient' => [
                'key' => 'tech-gradient',
                'name' => 'Tech Gradient',
                'description' => 'تقني بألوان سيان–بنفسجي',
                'preview' => asset('certification/06-tech-gradient.html'),
                'rtl' => false,
            ],
            'platinum-noir' => [
                'key' => 'platinum-noir',
                'name' => 'Platinum Noir',
                'description' => 'أسود وفضي فاخر',
                'preview' => asset('certification/07-platinum-noir.html'),
                'rtl' => false,
            ],
            'imperial-purple' => [
                'key' => 'imperial-purple',
                'name' => 'Imperial Purple',
                'description' => 'بنفسجي إمبراطوري مزخرف',
                'preview' => asset('certification/08-imperial-purple.html'),
                'rtl' => false,
            ],
            'cairo-gold-arabic' => [
                'key' => 'cairo-gold-arabic',
                'name' => 'Cairo Gold — عربي',
                'description' => 'تصميم عربي RTL بذهبي',
                'preview' => asset('certification/09-cairo-gold-arabic.html'),
                'rtl' => true,
            ],
            'mindlytics-modern' => [
                'key' => 'mindlytics-modern',
                'name' => 'Mindlytics Modern',
                'description' => 'حديث بهوية Mindlytics',
                'preview' => asset('certification/10-mindlytics-modern.html'),
                'rtl' => false,
            ],
        ];
    }

    public function eligibleEnrollments(User $student)
    {
        return StudentCourseEnrollment::query()
            ->with(['course.instructor:id,name'])
            ->where('user_id', $student->id)
            ->whereIn('status', ['active', 'completed'])
            ->finishedCurriculum()
            ->orderByDesc('curriculum_completed_at')
            ->orderByDesc('progress')
            ->get()
            ->filter(function (StudentCourseEnrollment $enrollment) use ($student) {
                return ! Certificate::query()
                    ->where('user_id', $student->id)
                    ->where('course_id', $enrollment->advanced_course_id)
                    ->where(function ($q) {
                        $q->where('status', 'issued')->orWhere('is_verified', true);
                    })
                    ->exists();
            })
            ->values();
    }

    public function issueForCompletedCourse(
        User $student,
        AdvancedCourse $course,
        string $templateKey,
        ?string $displayName = null,
    ): Certificate {
        $designs = self::designCatalog();
        if (! isset($designs[$templateKey])) {
            throw ValidationException::withMessages([
                'template' => 'تصميم الشهادة غير صالح.',
            ]);
        }

        $enrollment = StudentCourseEnrollment::query()
            ->where('user_id', $student->id)
            ->where('advanced_course_id', $course->id)
            ->whereIn('status', ['active', 'completed'])
            ->first();

        if (! $enrollment || ! $enrollment->hasFinishedCurriculum()) {
            throw ValidationException::withMessages([
                'course' => 'يجب إكمال الكورس بنسبة 100٪ قبل إصدار الشهادة.',
            ]);
        }

        $existing = Certificate::query()
            ->where('user_id', $student->id)
            ->where('course_id', $course->id)
            ->where(function ($q) {
                $q->where('status', 'issued')->orWhere('is_verified', true);
            })
            ->first();

        if ($existing) {
            return $existing;
        }

        $course->loadMissing('instructor');
        $branding = CertificateBranding::current();
        $instructor = $course->instructor;
        $name = trim((string) ($displayName ?: $student->name));

        return DB::transaction(function () use ($student, $course, $templateKey, $branding, $instructor, $name, $enrollment) {
            $serial = Certificate::generateSerialNumber();
            $certNumber = $this->uniqueCertificateNumber();
            $verificationCode = strtoupper(Str::random(12));

            $certificate = Certificate::create([
                'certificate_number' => $certNumber,
                'serial_number' => $serial,
                'user_id' => $student->id,
                'course_id' => $course->id,
                'course_name' => $course->title,
                'title' => 'شهادة إتمام — '.$course->title,
                'description' => 'شهادة إتمام كورس بعد الوصول لنسبة إنجاز 100٪',
                'certificate_type' => 'completion',
                'template' => $templateKey,
                'status' => 'issued',
                'is_verified' => true,
                'is_public' => true,
                'issue_date' => now()->toDateString(),
                'issued_at' => now(),
                'certified_at' => now(),
                'verification_code' => $verificationCode,
                'logo_path' => $branding?->logo_path,
                'academy_signature' => $branding?->signature_path,
                'academy_signature_name' => $serial,
                'academy_signature_title' => 'Serial Number',
                'stamp_path' => $branding?->stamp_path,
                'instructor_id' => $instructor?->id,
                'instructor_signature_name' => $instructor?->name ?? ($branding?->signature_name ?: 'Instructor'),
                'instructor_signature_title' => 'Instructor',
                'metadata' => [
                    'enrollment_id' => $enrollment->id,
                    'progress' => (float) $enrollment->progress,
                    'curriculum_completed_at' => optional($enrollment->curriculum_completed_at)?->toIso8601String(),
                    'display_name' => $name,
                    'design' => $templateKey,
                    'issued_via' => 'student_claim',
                    'tax_number' => $branding?->tax_number ?: '774-128-949',
                    'academy_name' => $branding?->academy_name,
                ],
            ]);

            $certificate->certificate_hash = $certificate->generateHash();
            $certificate->verification_url = route('public.certificates.verify.code', ['code' => $serial]);
            $certificate->save();

            return $certificate->fresh(['user', 'course', 'instructor']);
        });
    }

    private function uniqueCertificateNumber(): string
    {
        do {
            $number = 'CERT-'.strtoupper(Str::random(10));
        } while (Certificate::where('certificate_number', $number)->exists());

        return $number;
    }
}
