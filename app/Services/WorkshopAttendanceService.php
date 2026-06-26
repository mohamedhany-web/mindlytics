<?php

namespace App\Services;

use App\Models\Workshop;
use App\Models\WorkshopRegistration;

class WorkshopAttendanceService
{
    /**
     * @return array{status: 'success'|'already'|'not_found', message: string, registration: ?WorkshopRegistration}
     */
    public function confirmByNameAndPhone(Workshop $workshop, string $name, string $phone): array
    {
        $registration = $this->findRegistration($workshop, $name, $phone);

        if (! $registration) {
            return [
                'status' => 'not_found',
                'message' => 'لم نجد حجزاً بهذا الاسم ورقم الهاتف في هذه الورشة. تأكد من البيانات أو تواصل مع فريق الدعم.',
                'registration' => null,
            ];
        }

        if ($registration->checked_in_at) {
            return [
                'status' => 'already',
                'message' => 'تم تأكيد حضورك مسبقاً في '.$registration->checked_in_at->format('Y-m-d H:i').'. ستُصدَر شهادتك وفق جدول الأكاديمية.',
                'registration' => $registration,
            ];
        }

        $registration->checked_in_at = now();
        $registration->save();

        return [
            'status' => 'success',
            'message' => 'تم تأكيد حضورك بنجاح، '.($registration->name ?: 'عزيزنا').'! سيتم إصدار شهادة الورشة لك قريباً.',
            'registration' => $registration->fresh(),
        ];
    }

    public function findRegistration(Workshop $workshop, string $name, string $phone): ?WorkshopRegistration
    {
        $inputPhone = $this->normalizePhone($phone);
        if (! $inputPhone) {
            return null;
        }

        $candidates = $workshop->registrations()
            ->whereNotNull('phone')
            ->get()
            ->filter(fn (WorkshopRegistration $reg) => $this->normalizePhone($reg->phone) === $inputPhone);

        if ($candidates->isEmpty()) {
            $variants = $this->phoneMatchVariants($phone);
            if ($variants !== []) {
                $candidates = $workshop->registrations()
                    ->whereIn('phone', $variants)
                    ->get();
            }
        }

        if ($candidates->isEmpty()) {
            return null;
        }

        $nameMatches = $candidates->filter(fn (WorkshopRegistration $reg) => $this->namesMatch($reg->name, $name));

        if ($nameMatches->count() === 1) {
            return $nameMatches->first();
        }

        if ($nameMatches->count() > 1) {
            return $nameMatches->sortByDesc('id')->first();
        }

        if ($candidates->count() === 1) {
            return $candidates->first();
        }

        return null;
    }

    private function namesMatch(?string $registeredName, ?string $inputName): bool
    {
        $a = $this->normalizeName($registeredName);
        $b = $this->normalizeName($inputName);

        if ($a === '' || $b === '') {
            return false;
        }

        if ($a === $b) {
            return true;
        }

        if (mb_strlen($b) >= 3 && (str_contains($a, $b) || str_contains($b, $a))) {
            return true;
        }

        similar_text($a, $b, $percent);

        return $percent >= 85;
    }

    private function normalizeName(?string $name): string
    {
        if ($name === null) {
            return '';
        }

        $normalized = preg_replace('/\s+/u', ' ', trim($name)) ?? '';

        return mb_strtolower($normalized, 'UTF-8');
    }

    /**
     * @return list<string>
     */
    private function phoneMatchVariants(?string $phone): array
    {
        if ($phone === null || trim($phone) === '') {
            return [];
        }

        $normalized = $this->normalizePhone($phone);
        if (! $normalized) {
            return [];
        }

        return array_values(array_unique(array_filter([
            trim($phone),
            $normalized,
            str_starts_with($normalized, '20') ? '0'.substr($normalized, 2) : null,
            str_starts_with($normalized, '20') ? '+'. $normalized : null,
            ! str_starts_with($normalized, '20') ? '20'.$normalized : null,
        ])));
    }

    private function normalizePhone(?string $phone): ?string
    {
        if (! $phone) {
            return null;
        }

        $clean = preg_replace('/[^0-9]/', '', $phone);

        if (str_starts_with($clean, '01') && strlen($clean) === 11) {
            $clean = '2'.$clean;
        }

        return $clean ?: null;
    }
}
