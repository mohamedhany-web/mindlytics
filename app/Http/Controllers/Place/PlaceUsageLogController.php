<?php



namespace App\Http\Controllers\Place;



use App\Http\Controllers\Controller;

use App\Models\OfflineCourse;

use App\Models\PlaceDailyExpense;

use App\Models\PlaceMonthlySettlement;

use App\Models\PlaceUsageLog;

use App\Services\PlaceSettlementService;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;

use Illuminate\Validation\ValidationException;



class PlaceUsageLogController extends Controller

{

    public function __construct(protected PlaceSettlementService $settlementService)

    {

    }



    public function index(Request $request)

    {

        $location = view()->shared('resolvedPlaceLocation');



        $logs = PlaceUsageLog::query()

            ->where('offline_location_id', $location->id)

            ->with(['reviewer', 'offlineCourse', 'dailyExpenses'])

            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))

            ->when($request->filled('month'), function ($q) use ($request) {

                [$y, $m] = explode('-', $request->month);

                $q->whereYear('usage_date', $y)->whereMonth('usage_date', $m);

            })

            ->latest('usage_date')

            ->paginate(20)

            ->withQueryString();



        $standaloneExpenses = PlaceDailyExpense::query()

            ->where('offline_location_id', $location->id)

            ->whereNull('place_usage_log_id')

            ->with(['reviewer'])

            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))

            ->when($request->filled('month'), function ($q) use ($request) {

                [$y, $m] = explode('-', $request->month);

                $q->whereYear('expense_date', $y)->whereMonth('expense_date', $m);

            })

            ->latest('expense_date')

            ->paginate(20, ['*'], 'expenses_page')

            ->withQueryString();



        return view('place-office.usage-logs.index', compact('location', 'logs', 'standaloneExpenses'));

    }



    public function create()

    {

        $location = view()->shared('resolvedPlaceLocation');

        $period = now()->format('Y-m');



        $currentSettlement = PlaceMonthlySettlement::query()

            ->where('offline_location_id', $location->id)

            ->where('period_month', $period)

            ->first();



        $pendingLogs = PlaceUsageLog::query()

            ->where('offline_location_id', $location->id)

            ->pending()

            ->count();



        $pendingExpenses = PlaceDailyExpense::query()

            ->where('offline_location_id', $location->id)

            ->pending()

            ->count();



        $approvedHoursThisMonth = PlaceUsageLog::query()

            ->where('offline_location_id', $location->id)

            ->approved()

            ->whereYear('usage_date', now()->year)

            ->whereMonth('usage_date', now()->month)

            ->sum('hours');



        $courses = $location->courses()

            ->orderBy('title')

            ->get(['id', 'title']);



        $user = auth()->user();

        $expenseCategories = PlaceDailyExpense::categoryLabels();

        $usageTypes = PlaceUsageLog::usageTypeLabels();



        return view('place-office.usage-logs.create', compact(

            'location',

            'currentSettlement',

            'pendingLogs',

            'pendingExpenses',

            'approvedHoursThisMonth',

            'period',

            'user',

            'courses',

            'expenseCategories',

            'usageTypes'

        ));

    }



    public function store(Request $request)

    {

        $location = view()->shared('resolvedPlaceLocation');



        $validated = $request->validate([

            'usage_date' => 'required|date|before_or_equal:today',

            'usage_type' => 'nullable|in:course,other_activity',

            'offline_course_id' => 'nullable|exists:offline_courses,id',

            'hours' => 'nullable|numeric|min:0.25|max:24',

            'description' => 'nullable|string|max:500',

            'expenses' => 'nullable|array',

            'expenses.*.title' => 'nullable|string|max:255',

            'expenses.*.category' => 'nullable|in:food,drinks,supplies,transport,other',

            'expenses.*.amount' => 'nullable|numeric|min:0.01',

            'expenses.*.quantity' => 'nullable|integer|min:1|max:999',

        ], [

            'usage_date.required' => 'تاريخ اليوم مطلوب.',

            'hours.min' => 'الحد الأدنى ربع ساعة.',

        ]);



        $expenseLines = collect($validated['expenses'] ?? [])

            ->filter(fn ($row) => filled($row['title'] ?? null) && filled($row['amount'] ?? null))

            ->values();



        $hasHours = isset($validated['hours']) && (float) $validated['hours'] > 0;



        if (! $hasHours && $expenseLines->isEmpty()) {

            throw ValidationException::withMessages([

                'hours' => 'أدخل ساعات الاستخدام أو أضف بند مصروف واحد على الأقل.',

            ]);

        }



        if ($hasHours && empty($validated['usage_type'])) {
            $validated['usage_type'] = PlaceUsageLog::TYPE_COURSE;
        }

        if ($hasHours && ($validated['usage_type'] ?? '') === PlaceUsageLog::TYPE_COURSE) {

            if (empty($validated['offline_course_id'])) {

                throw ValidationException::withMessages([

                    'offline_course_id' => 'اختر الكورس المعطى في المكان.',

                ]);

            }



            $courseValid = OfflineCourse::query()

                ->where('id', $validated['offline_course_id'])

                ->where('location_id', $location->id)

                ->exists();



            if (! $courseValid) {

                throw ValidationException::withMessages([

                    'offline_course_id' => 'الكورس المختار غير مرتبط بهذا المكان.',

                ]);

            }

        }



        foreach ($expenseLines as $index => $line) {

            if (empty($line['category'])) {

                throw ValidationException::withMessages([

                    "expenses.{$index}.category" => 'فئة المصروف مطلوبة.',

                ]);

            }

        }



        $period = \Carbon\Carbon::parse($validated['usage_date'])->format('Y-m');



        try {

            $settlement = $this->settlementService->getOrCreateOpenSettlement($location, $period);

        } catch (ValidationException $e) {

            return back()->withErrors($e->errors())->withInput();

        }



        if ($settlement->status !== PlaceMonthlySettlement::STATUS_OPEN) {

            return back()->with('error', 'شهر ' . $period . ' غير مفتوح للتسجيل.')->withInput();

        }



        DB::transaction(function () use ($validated, $location, $request, $settlement, $hasHours, $expenseLines) {

            $usageLog = null;



            if ($hasHours) {

                $usageLog = PlaceUsageLog::create([

                    'offline_location_id' => $location->id,

                    'logged_by' => $request->user()->id,

                    'usage_date' => $validated['usage_date'],

                    'usage_type' => $validated['usage_type'],

                    'offline_course_id' => ($validated['usage_type'] ?? '') === PlaceUsageLog::TYPE_COURSE

                        ? $validated['offline_course_id']

                        : null,

                    'hours' => $validated['hours'],

                    'description' => $validated['description'] ?? null,

                    'status' => PlaceUsageLog::STATUS_PENDING,

                    'place_monthly_settlement_id' => $settlement->id,

                ]);

            }



            foreach ($expenseLines as $line) {

                PlaceDailyExpense::create([

                    'offline_location_id' => $location->id,

                    'logged_by' => $request->user()->id,

                    'expense_date' => $validated['usage_date'],

                    'title' => $line['title'],

                    'category' => $line['category'],

                    'amount' => $line['amount'],

                    'quantity' => $line['quantity'] ?? 1,

                    'status' => PlaceDailyExpense::STATUS_PENDING,

                    'place_monthly_settlement_id' => $settlement->id,

                    'place_usage_log_id' => $usageLog?->id,

                ]);

            }

        });



        $message = $hasHours && $expenseLines->isNotEmpty()

            ? 'تم تسجيل الساعات والمصاريف وإرسالها للمراجعة.'

            : ($hasHours ? 'تم تسجيل الساعات وإرسالها للمراجعة.' : 'تم تسجيل فاتورة المصاريف وإرسالها للمراجعة.');



        return redirect()->route('place.office.usage-logs.index')

            ->with('success', $message);

    }

}

