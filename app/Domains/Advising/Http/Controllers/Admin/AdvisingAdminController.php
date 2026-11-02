<?php

namespace App\Domains\Advising\Http\Controllers\Admin;

use App\Domains\Advising\Models\AdvisingAvailability;
use App\Domains\Advising\Models\AdvisingBlockedDate;
use App\Domains\Advising\Models\AdvisingBooking;
use App\Domains\Advising\Services\AdvisingSettings;
use App\Domains\Advising\Services\AdvisingSlots;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/** إدارة الإرشاد المهني: الحجوزات ونتايجها، ومواعيد التوافر، والأيام المقفولة، وإعدادات الحجز. */
class AdvisingAdminController extends Controller
{
    public const RANGES = ['upcoming' => 'القادمة', 'today' => 'النهارده', 'past' => 'السابقة', 'all' => 'الكل'];

    public function bookings(Request $request): View
    {
        $range = array_key_exists((string) $request->query('range'), self::RANGES) ? (string) $request->query('range') : 'upcoming';
        $status = array_key_exists((string) $request->query('status'), AdvisingBooking::STATUSES) ? (string) $request->query('status') : null;

        $query = AdvisingBooking::query()->with('advisor:id,name');
        match ($range) {
            'upcoming' => $query->where('starts_at', '>=', now()->startOfDay())->orderBy('starts_at'),
            'today' => $query->whereDate('starts_at', today())->orderBy('starts_at'),
            'past' => $query->where('starts_at', '<', now()->startOfDay())->orderByDesc('starts_at'),
            default => $query->orderByDesc('starts_at'),
        };
        if ($status) {
            $query->where('status', $status);
        }
        if ($request->filled('q')) {
            $q = '%'.$request->string('q')->trim().'%';
            $query->where(fn ($w) => $w->where('name', 'like', $q)->orWhere('phone', 'like', $q)->orWhere('email', 'like', $q));
        }

        $counts = [
            'new' => AdvisingBooking::query()->where('status', 'pending')->count(),
            'today' => AdvisingBooking::query()->holdingSlot()->whereDate('starts_at', today())->count(),
            'week' => AdvisingBooking::query()->holdingSlot()->whereBetween('starts_at', [now()->startOfDay(), now()->addDays(7)->endOfDay()])->count(),
            'open_slots' => collect(app(AdvisingSlots::class)->days())->sum(fn ($d) => count($d['slots'])),
        ];

        return view('advising::admin.bookings', [
            'bookings' => $query->paginate(25)->withQueryString(),
            'range' => $range,
            'status' => $status,
            'counts' => $counts,
            'advisors' => $this->advisors(),
        ]);
    }

    public function updateBooking(Request $request, AdvisingBooking $booking): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(array_keys(AdvisingBooking::STATUSES))],
            'advisor_id' => ['nullable', 'integer', 'exists:users,id'],
            'outcome' => ['nullable', 'string', 'max:5000'],
            'recommended_program' => ['nullable', 'string', 'max:255'],
        ]);
        $booking->update($data);

        return back()->with('success', 'اتحفظ حجز '.$booking->name.'.');
    }

    public function availability(): View
    {
        return view('advising::admin.availability', [
            'rules' => AdvisingAvailability::query()->with('advisor:id,name')->orderByRaw('(weekday + 1) % 7')->orderBy('start_time')->get(),
            'blocked' => AdvisingBlockedDate::query()->where('date', '>=', today())->orderBy('date')->get(),
            'settings' => AdvisingSettings::get(),
            'advisors' => $this->advisors(),
            'preview' => array_slice(app(AdvisingSlots::class)->days(), 0, 5),
        ]);
    }

    public function storeRule(Request $request): RedirectResponse
    {
        $data = $this->validatedRule($request);
        foreach ($data['weekdays'] as $weekday) {
            AdvisingAvailability::query()->create(collect($data)->except('weekdays')->all() + ['weekday' => (int) $weekday, 'is_active' => true]);
        }

        return back()->with('success', 'اتضافت مواعيد التوافر.');
    }

    public function updateRule(Request $request, AdvisingAvailability $rule): RedirectResponse
    {
        $data = $this->validatedRule($request, single: true);
        $rule->update($data + ['is_active' => $request->boolean('is_active')]);

        return back()->with('success', 'اتحفظت فترة التوافر.');
    }

    public function destroyRule(AdvisingAvailability $rule): RedirectResponse
    {
        $rule->delete(); // الحجوزات القديمة بتفضل (availability_id بيبقى null)

        return back()->with('success', 'اتمسحت فترة التوافر. الحجوزات اللي عليها زي ما هي.');
    }

    public function storeBlocked(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'date' => ['required', 'date', 'after_or_equal:today', 'unique:advising_blocked_dates,date'],
            'reason' => ['nullable', 'string', 'max:255'],
        ], ['date.unique' => 'اليوم ده مقفول بالفعل.']);
        AdvisingBlockedDate::query()->create($data);

        return back()->with('success', 'اتقفل اليوم.');
    }

    public function destroyBlocked(AdvisingBlockedDate $blocked): RedirectResponse
    {
        $blocked->delete();

        return back()->with('success', 'اتفتح اليوم تاني.');
    }

    public function updateSettings(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'window_days' => ['required', 'integer', 'min:1', 'max:60'],
            'min_notice_hours' => ['required', 'integer', 'min:0', 'max:72'],
        ]);
        AdvisingSettings::save($data + ['enabled' => $request->boolean('enabled')]);

        return back()->with('success', 'اتحفظت إعدادات الحجز.');
    }

    /** @return array<string, mixed> */
    private function validatedRule(Request $request, bool $single = false): array
    {
        $rules = [
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'slot_minutes' => ['required', 'integer', Rule::in([15, 20, 30, 45, 60, 90])],
            'capacity' => ['required', 'integer', 'min:1', 'max:20'],
            'mode' => ['required', Rule::in(array_keys(AdvisingBooking::MODES))],
            'advisor_id' => ['nullable', 'integer', 'exists:users,id'],
        ];
        $rules += $single
            ? ['weekday' => ['required', 'integer', 'between:0,6']]
            : ['weekdays' => ['required', 'array', 'min:1'], 'weekdays.*' => ['integer', 'between:0,6']];

        return $request->validate($rules, ['end_time.after' => 'نهاية الفترة لازم تكون بعد بدايتها.', 'weekdays.required' => 'اختار يوم واحد على الأقل.']);
    }

    /** المرشدون: الإدارة والموظفين النشطين. @return Collection<int, User> */
    private function advisors(): Collection
    {
        return User::query()->where('is_active', true)
            ->where(fn ($q) => $q->whereIn('role', ['admin', 'super_admin'])->orWhere('is_employee', true))
            ->orderBy('name')->get(['id', 'name']);
    }
}
