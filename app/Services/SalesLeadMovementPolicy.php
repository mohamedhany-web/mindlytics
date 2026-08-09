<?php

namespace App\Services;

use App\Models\SalesLead;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;

/**
 * قاعدة CRM: أي Lead مفتوح ممنوع يفضل سايب — لازم Status + Next Action + موعد متابعة.
 */
class SalesLeadMovementPolicy
{
    /**
     * المراحل اللي لسه محتاجة حركة يومية (مش مغلقة ومش Enrollment مكتمل).
     */
    public function requiresActiveMovement(?string $stage): bool
    {
        if ($stage === null || $stage === '') {
            return true;
        }

        $stage = SalesLead::normalizeStage($stage);

        return ! in_array($stage, [...SalesLead::CLOSED_STAGES, SalesLead::WON_STAGE], true);
    }

    /**
     * @param  array<string, mixed>  $data  الحقول الجديدة (من فورم/بايلود)
     * @param  SalesLead|null  $existing  السجل الحالي عند التحديث (لدمج القيم الناقصة)
     *
     * @throws ValidationException
     */
    public function assertOpenLeadHasMovement(array $data, ?SalesLead $existing = null): void
    {
        $stage = (string) ($data['stage'] ?? $existing?->stage ?? '');
        if ($stage === '' && $existing) {
            $stage = (string) $existing->stage;
        }
        $stage = SalesLead::normalizeStage($stage);

        if (! $this->requiresActiveMovement($stage)) {
            return;
        }

        $errors = [];

        if ($stage === '' || ! array_key_exists($stage, SalesLead::STAGES)) {
            $errors['stage'] = ['Status / المرحلة مطلوبة.'];
        }

        $nextFollow = $this->resolveField($data, $existing, 'next_follow_up_at');
        if ($nextFollow === null || $nextFollow === '') {
            $errors['next_follow_up_at'] = ['موعد المتابعة مطلوب — ممنوع Lead بدون حركة في الـ CRM.'];
        } else {
            try {
                $parsed = $nextFollow instanceof Carbon
                    ? $nextFollow
                    : Carbon::parse((string) $nextFollow);
                // هامش دقيقة لتجنب رفض datetime-local القريب من الآن
                if ($parsed->lt(now()->subMinute())) {
                    $errors['next_follow_up_at'] = ['موعد المتابعة يجب أن يكون الآن أو في المستقبل — حدّث الموعد قبل الحفظ.'];
                }
            } catch (\Throwable) {
                $errors['next_follow_up_at'] = ['موعد المتابعة غير صالح.'];
            }
        }

        $channel = $this->resolveField($data, $existing, 'follow_up_channel');
        if ($channel === null || $channel === '' || ! array_key_exists((string) $channel, SalesLead::FOLLOW_UP_CHANNELS)) {
            $errors['follow_up_channel'] = ['الإجراء التالي (Next Action) مطلوب: اتصال أو واتساب أو اجتماع.'];
        }

        if ($errors !== []) {
            throw ValidationException::withMessages($errors);
        }
    }

    /**
     * قيم افتراضية آمنة عند الإنشاء الآلي (استيراد / أنظمة).
     *
     * @param  array<string, mixed>  $attributes
     * @return array<string, mixed>
     */
    public function withCreateDefaults(array $attributes): array
    {
        $stage = SalesLead::normalizeStage((string) ($attributes['stage'] ?? 'new_lead'));
        $attributes['stage'] = $stage;

        if (! $this->requiresActiveMovement($stage)) {
            return $attributes;
        }

        if (empty($attributes['next_follow_up_at'])) {
            $attributes['next_follow_up_at'] = now()->addDay()->setTime(10, 0);
        }

        if (empty($attributes['follow_up_channel'])
            || ! array_key_exists((string) $attributes['follow_up_channel'], SalesLead::FOLLOW_UP_CHANNELS)) {
            $attributes['follow_up_channel'] = 'call';
        }

        return $attributes;
    }

    private function resolveField(array $data, ?SalesLead $existing, string $key): mixed
    {
        if (array_key_exists($key, $data)) {
            return $data[$key];
        }

        return $existing?->{$key};
    }
}
