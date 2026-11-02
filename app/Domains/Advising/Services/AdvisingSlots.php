<?php

namespace App\Domains\Advising\Services;

use App\Domains\Advising\Models\AdvisingAvailability;
use App\Domains\Advising\Models\AdvisingBlockedDate;
use App\Domains\Advising\Models\AdvisingBooking;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * المواعيد المتاحة للإرشاد: من التوافر الأسبوعي، ناقص الأيام المقفولة، والمواعيد اللي اتحجزت لآخرها،
 * والمواعيد اللي فاضل عليها أقل من وقت الحجز المسموح.
 */
class AdvisingSlots
{
    /**
     * @return list<array{date: string, weekday: int, slots: list<array{key: string, time: string, starts_at: string, availability_id: int, mode: string, minutes: int}>}>
     */
    public function days(?Carbon $now = null): array
    {
        $settings = AdvisingSettings::get();
        if (! $settings['enabled']) {
            return [];
        }

        $now = ($now ?? now())->copy();
        $earliest = $now->copy()->addHours($settings['min_notice_hours']);
        $from = $now->copy()->startOfDay();
        $to = $from->copy()->addDays($settings['window_days']);

        $rules = AdvisingAvailability::query()->active()->orderBy('start_time')->get()->groupBy('weekday');
        if ($rules->isEmpty()) {
            return [];
        }

        $blocked = AdvisingBlockedDate::query()->whereBetween('date', [$from->toDateString(), $to->toDateString()])
            ->pluck('date')->map(fn ($d) => Carbon::parse($d)->toDateString())->flip();

        $taken = AdvisingBooking::query()->holdingSlot()->whereBetween('starts_at', [$from, $to->copy()->endOfDay()])
            ->selectRaw('availability_id, starts_at, COUNT(*) as c')->groupBy('availability_id', 'starts_at')->get()
            ->mapWithKeys(fn ($r) => [$r->availability_id.'|'.Carbon::parse($r->starts_at)->format('Y-m-d H:i') => (int) $r->c]);

        $days = [];
        for ($day = $from->copy(); $day->lt($to); $day->addDay()) {
            if (isset($blocked[$day->toDateString()])) {
                continue;
            }
            $slots = [];
            foreach ($rules->get($day->dayOfWeek, collect()) as $rule) {
                foreach ($this->ruleTimes($rule, $day) as $start) {
                    $key = $rule->id.'|'.$start->format('Y-m-d H:i');
                    if ($start->lt($earliest) || ($taken[$key] ?? 0) >= max(1, $rule->capacity)) {
                        continue;
                    }
                    $slots[$start->format('H:i').'|'.$rule->id] = [
                        'key' => $key,
                        'time' => $start->format('H:i'),
                        'starts_at' => $start->format('Y-m-d H:i'),
                        'availability_id' => $rule->id,
                        'mode' => $rule->mode,
                        'minutes' => $rule->slot_minutes,
                    ];
                }
            }
            if ($slots !== []) {
                ksort($slots);
                $days[] = ['date' => $day->toDateString(), 'weekday' => $day->dayOfWeek, 'slots' => array_values($slots)];
            }
        }

        return $days;
    }

    /** الميعاد ده لسه متاح؟ بيرجّع فترة التوافر بتاعته، وإلا null. */
    public function find(int $availabilityId, string $startsAt): ?AdvisingAvailability
    {
        foreach ($this->days() as $day) {
            foreach ($day['slots'] as $slot) {
                if ($slot['availability_id'] === $availabilityId && $slot['starts_at'] === $startsAt) {
                    return AdvisingAvailability::query()->find($availabilityId);
                }
            }
        }

        return null;
    }

    /** @return Collection<int, Carbon> */
    private function ruleTimes(AdvisingAvailability $rule, Carbon $day): Collection
    {
        $minutes = max(10, (int) $rule->slot_minutes);
        $start = Carbon::parse($day->toDateString().' '.$rule->start_time);
        $end = Carbon::parse($day->toDateString().' '.$rule->end_time);
        $out = collect();
        for ($t = $start->copy(); $t->copy()->addMinutes($minutes)->lte($end); $t->addMinutes($minutes)) {
            $out->push($t->copy());
        }

        return $out;
    }
}
