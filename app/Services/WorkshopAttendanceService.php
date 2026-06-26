<?php

namespace App\Services;

use App\Models\Workshop;
use App\Models\WorkshopRegistration;
use Illuminate\Support\Str;

class WorkshopAttendanceService
{
    /**
     * @return array{status: 'success'|'already'|'created', message: string, registration: ?WorkshopRegistration}
     */
    public function confirmByNameAndPhone(Workshop $workshop, string $name, string $phone): array
    {
        $name = trim($name);
        $registration = $this->findRegistration($workshop, $name, $phone);

        if (! $registration) {
            $registration = $this->createWalkInRegistration($workshop, $name, $phone);

            return [
                'status' => 'created',
                'message' => 'تم تسجيل حضورك وتأكيده بنجاح، '.$registration->name.'! سيتم إصدار شهادة الورشة لك قريباً.',
                'registration' => $registration,
            ];
        }

        if ($registration->checked_in_at) {
            return [
                'status' => 'already',
                'message' => 'تم تأكيد حضورك مسبقاً في '.$registration->checked_in_at->format('Y-m-d H:i').'. ستُصدَر شهادتك وفق جدول الأكاديمية.',
                'registration' => $registration,
            ];
        }

        if ($this->normalizeName($registration->name) !== $this->normalizeName($name)) {
            $registration->name = $name;
        }

        $registration->checked_in_at = now();
        $registration->save();

        return [
            'status' => 'success',
            'message' => 'تم تأكيد حضورك بنجاح، '.($registration->name ?: 'عزيزنا').'! سيتم إصدار شهادة الورشة لك قريباً.',
            'registration' => $registration->fresh(),
        ];
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection<int, WorkshopRegistration>
     */
    public function confirmedAttendees(Workshop $workshop)
    {
        return $workshop->registrations()
            ->whereNotNull('checked_in_at')
            ->orderByDesc('checked_in_at')
            ->get(['id', 'name', 'checked_in_at', 'created_at']);
    }

    private function createWalkInRegistration(Workshop $workshop, string $name, string $phone): WorkshopRegistration
    {
        $attendanceMode = match ($workshop->mode) {
            'offline' => 'offline',
            'online' => 'online',
            default => 'online',
        };

        return WorkshopRegistration::create([
            'workshop_id' => $workshop->id,
            'name' => $name,
            'phone' => $phone,
            'attendance_mode' => $attendanceMode,
            'status' => 'confirmed',
            'notes' => 'تأكيد حضور مباشر — دون حجز مسبق',
            'checkin_token' => (string) Str::uuid(),
            'checked_in_at' => now(),
        ]);
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
