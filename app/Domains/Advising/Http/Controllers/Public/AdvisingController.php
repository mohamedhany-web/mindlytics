<?php

namespace App\Domains\Advising\Http\Controllers\Public;

use App\Domains\Advising\Models\AdvisingAvailability;
use App\Domains\Advising\Models\AdvisingBooking;
use App\Domains\Advising\Services\AdvisingBookingService;
use App\Domains\Advising\Services\AdvisingCrmSync;
use App\Domains\Advising\Services\AdvisingSlots;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * حجز جلسة الإرشاد المهني من الموقع: البوب أب في كل الصفحات (JSON)، وصفحة /advising لو الـ JS مقفول أو للرابط المباشر.
 */
class AdvisingController extends Controller
{
    public function show(AdvisingSlots $slots): View
    {
        return view('advising::public.book', ['days' => $this->presentDays($slots->days())]);
    }

    public function slots(AdvisingSlots $slots): JsonResponse
    {
        return response()->json(['days' => $this->presentDays($slots->days())]);
    }

    public function store(Request $request, AdvisingBookingService $service, AdvisingCrmSync $crm): JsonResponse|RedirectResponse
    {
        // Honeypot: البوتات بتملاه
        if ($request->filled('website')) {
            return $request->expectsJson() ? response()->json(['ok' => true]) : redirect()->route('advising.show');
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'phone' => ['required', 'string', 'max:40', 'regex:/^[0-9+\s\-()]{7,40}$/'],
            'email' => ['nullable', 'email', 'max:190'],
            'goal' => ['required', Rule::in(array_keys(AdvisingBooking::GOALS))],
            'level' => ['nullable', Rule::in(array_keys(AdvisingBooking::LEVELS))],
            'interest' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'slot' => ['required', 'string', 'max:40'],
        ], [], $this->attributeNames());

        $booking = $service->book($data + [
            'user_id' => $request->user()?->id,
            'source_url' => (string) $request->headers->get('referer', ''),
            'ip' => $request->ip(),
        ]);
        $crm->onBooked($booking); // عميل محتمل + اجتماع في كاليندر الموظف (docs/ADVISING.md)

        $summary = $this->summary($booking);
        if ($request->expectsJson()) {
            return response()->json(['ok' => true, 'summary' => $summary]);
        }

        return redirect()->route('advising.show')->with('advising_booked', $summary);
    }

    /** @return list<array<string, mixed>> */
    private function presentDays(array $days): array
    {
        $ar = app()->getLocale() === 'ar';
        $names = $ar ? AdvisingAvailability::WEEKDAYS : AdvisingAvailability::WEEKDAYS_EN;

        return array_map(function (array $day) use ($ar, $names) {
            $date = Carbon::parse($day['date']);

            return [
                'date' => $day['date'],
                'weekday' => $names[$day['weekday']] ?? '',
                'label' => $date->isToday() ? ($ar ? 'النهارده' : 'Today') : ($date->isTomorrow() ? ($ar ? 'بكرة' : 'Tomorrow') : $names[$day['weekday']] ?? ''),
                'short' => $date->format('d/m'),
                'slots' => array_map(fn ($s) => $s + [
                    'time_label' => $this->timeLabel($s['time'], $ar),
                    'mode_label' => AdvisingBooking::label(AdvisingBooking::MODES, $s['mode'], $ar),
                ], $day['slots']),
            ];
        }, $days);
    }

    private function timeLabel(string $hm, bool $ar): string
    {
        $t = Carbon::createFromFormat('H:i', $hm);

        return $ar ? $t->format('g:i').' '.($t->hour < 12 ? 'ص' : 'م') : $t->format('g:i A');
    }

    /** @return array{date: string, time: string, mode: string, minutes: int} */
    private function summary(AdvisingBooking $b): array
    {
        $ar = app()->getLocale() === 'ar';
        $names = $ar ? AdvisingAvailability::WEEKDAYS : AdvisingAvailability::WEEKDAYS_EN;

        return [
            'date' => ($names[$b->starts_at->dayOfWeek] ?? '').' '.$b->starts_at->format('d/m/Y'),
            'time' => $this->timeLabel($b->starts_at->format('H:i'), $ar),
            'mode' => AdvisingBooking::label(AdvisingBooking::MODES, $b->mode, $ar),
            'minutes' => $b->duration_minutes,
        ];
    }

    /** @return array<string, string> */
    private function attributeNames(): array
    {
        return app()->getLocale() === 'ar'
            ? ['name' => 'الاسم', 'phone' => 'رقم الموبايل', 'email' => 'البريد الإلكتروني', 'goal' => 'هدفك', 'level' => 'مستواك', 'slot' => 'الميعاد']
            : ['name' => 'name', 'phone' => 'mobile', 'email' => 'email', 'goal' => 'goal', 'level' => 'level', 'slot' => 'time'];
    }
}
