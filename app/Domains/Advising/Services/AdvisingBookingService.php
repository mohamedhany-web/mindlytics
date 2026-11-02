<?php

namespace App\Domains\Advising\Services;

use App\Domains\Advising\Models\AdvisingAvailability;
use App\Domains\Advising\Models\AdvisingBooking;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/** إنشاء حجز إرشاد: بيتأكد إن الميعاد لسه فاضي جوه transaction عشان حجزين ما ياخدوش آخر مكان في نفس اللحظة. */
class AdvisingBookingService
{
    public function __construct(private AdvisingSlots $slots) {}

    /** @param  array<string, mixed>  $data  بعد الـ validation، ومعاها slot = "availability_id|Y-m-d H:i" */
    public function book(array $data): AdvisingBooking
    {
        [$availabilityId, $startsAt] = array_pad(explode('|', (string) $data['slot'], 2), 2, '');
        $availabilityId = (int) $availabilityId;

        return DB::transaction(function () use ($data, $availabilityId, $startsAt) {
            $rule = AdvisingAvailability::query()->whereKey($availabilityId)->lockForUpdate()->first();
            if (! $rule || ! $this->slots->find($availabilityId, $startsAt)) {
                throw ValidationException::withMessages([
                    'slot' => app()->getLocale() === 'ar' ? 'الميعاد ده اتحجز أو مبقاش متاح. اختار ميعاد تاني.' : 'This time is no longer available. Please pick another one.',
                ]);
            }

            return AdvisingBooking::query()->create([
                'name' => $data['name'],
                'phone' => $data['phone'],
                'email' => $data['email'] ?? null,
                'goal' => $data['goal'],
                'level' => $data['level'] ?? null,
                'interest' => $data['interest'] ?? null,
                'notes' => $data['notes'] ?? null,
                'starts_at' => Carbon::parse($startsAt),
                'duration_minutes' => $rule->slot_minutes,
                'mode' => $rule->mode,
                'availability_id' => $rule->id,
                'advisor_id' => $rule->advisor_id,
                'user_id' => $data['user_id'] ?? null,
                'status' => 'pending',
                'source_url' => isset($data['source_url']) ? mb_substr((string) $data['source_url'], 0, 500) : null,
                'ip' => $data['ip'] ?? null,
            ]);
        });
    }
}
