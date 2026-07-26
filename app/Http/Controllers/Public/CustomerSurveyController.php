<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\AdvancedCourse;
use App\Models\MarketingCustomerSurvey;
use App\Models\Order;
use App\Models\StudentCourseEnrollment;
use App\Models\User;
use App\Services\CustomerSurveyRewardService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

/**
 * استبيان عملاء التسويق: متاح فقط لمن اشترى كورساً من الأكاديمية،
 * ويمنحه كوبون خصم 20% على أي كورس بعد التعبئة.
 */
class CustomerSurveyController extends Controller
{
    public function __construct(private readonly CustomerSurveyRewardService $rewards)
    {
    }

    public function show()
    {
        $prefill = null;

        if (Auth::check()) {
            $prefill = $this->customerPayload(Auth::user());
        }

        return view('public.customer-survey', [
            'prefill' => $prefill,
            'governorates' => MarketingCustomerSurvey::governorates(),
            'jobs' => MarketingCustomerSurvey::jobs(),
            'heardFromOptions' => MarketingCustomerSurvey::heardFromOptions(),
            'discountPercentage' => CustomerSurveyRewardService::DISCOUNT_PERCENTAGE,
            'validDays' => CustomerSurveyRewardService::VALID_DAYS,
        ]);
    }

    /**
     * التحقق من البريد المسجل واستحضار بيانات العميل وكورساته المشتراة.
     */
    public function lookup(Request $request): JsonResponse
    {
        $request->merge(['email' => trim((string) $request->input('email'))]);

        $data = $request->validate([
            'email' => ['required', 'string', 'email', 'max:255'],
        ], [
            'email.required' => 'من فضلك اكتب البريد الإلكتروني.',
            'email.email' => 'صيغة البريد الإلكتروني غير صحيحة.',
        ]);

        $user = $this->findUserByEmail($data['email']);

        if (! $user) {
            return response()->json([
                'found' => false,
                'eligible' => false,
                'message' => 'لم نجد هذا البريد في المنصة. تأكد من كتابة نفس البريد المسجل عند شراء الكورس.',
            ], 200);
        }

        if ($this->purchasedCourses($user)->isEmpty()) {
            return response()->json([
                'found' => true,
                'eligible' => false,
                'message' => 'هذا الاستبيان مخصص لعملاء الأكاديمية الذين اشتروا كورساً بالفعل. لم نجد أي كورس مشترى على هذا البريد.',
            ], 200);
        }

        return response()->json([
            'found' => true,
            'eligible' => true,
            'message' => 'تم التعرف عليك ✔ أكمل باقي البيانات لتحصل على الخصم.',
            'customer' => $this->customerPayload($user),
        ], 200);
    }

    public function store(Request $request)
    {
        $email = (string) $request->input('email', '');
        $user = $this->findUserByEmail($email);

        if (! $user) {
            throw ValidationException::withMessages([
                'email' => 'لم نجد هذا البريد في المنصة. اكتب نفس البريد المسجل عند شراء الكورس.',
            ]);
        }

        $purchasedCourses = $this->purchasedCourses($user);

        if ($purchasedCourses->isEmpty()) {
            throw ValidationException::withMessages([
                'email' => 'هذا الاستبيان مخصص لعملاء اشتروا كورساً من الأكاديمية.',
            ]);
        }

        if (MarketingCustomerSurvey::where('user_id', $user->id)->exists()) {
            return redirect()->route('public.customer-survey.show')
                ->with('info', 'سجّلنا رأيك قبل كذلك، والخصم متاح لك في محفظتك. شكراً لك!');
        }

        $validated = $request->validate([
            'advanced_course_id' => ['required', Rule::in($purchasedCourses->pluck('id')->all())],
            'name' => ['required', 'string', 'max:150'],
            'phone' => ['nullable', 'string', 'max:30'],
            'governorate' => ['required', Rule::in(array_keys(MarketingCustomerSurvey::governorates()))],
            'job' => ['required', Rule::in(array_keys(MarketingCustomerSurvey::jobs()))],
            'job_other' => ['nullable', 'string', 'max:150', 'required_if:job,'.MarketingCustomerSurvey::OTHER],
            'heard_from' => ['required', Rule::in(array_keys(MarketingCustomerSurvey::heardFromOptions()))],
            'heard_from_other' => ['nullable', 'string', 'max:150', 'required_if:heard_from,'.MarketingCustomerSurvey::OTHER],
            'interested_in' => ['required', 'string', 'max:2000'],
            'opinion' => ['required', 'string', 'max:3000'],
            'needed_courses' => ['nullable', 'string', 'max:2000'],
            'recommendations' => ['nullable', 'string', 'max:2000'],
        ], [
            'advanced_course_id.required' => 'اختر الكورس الذي درسته معنا.',
            'advanced_course_id.in' => 'الكورس المختار غير مرتبط بحسابك.',
            'name.required' => 'الاسم مطلوب.',
            'governorate.required' => 'اختر المحافظة.',
            'job.required' => 'اختر الوظيفة أو المجال.',
            'job_other.required_if' => 'اكتب وظيفتك بما أنك اخترت «أخرى».',
            'heard_from.required' => 'اختر كيف عرفتنا.',
            'heard_from_other.required_if' => 'اكتب كيف عرفتنا بما أنك اخترت «أخرى».',
            'interested_in.required' => 'قل لنا ما يهمك في الفترة القادمة.',
            'opinion.required' => 'رأيك في الكورس والأكاديمية مطلوب.',
        ]);

        try {
            $result = DB::transaction(function () use ($validated, $user, $request) {
                $survey = MarketingCustomerSurvey::create([
                    'user_id' => $user->id,
                    'advanced_course_id' => $validated['advanced_course_id'],
                    'name' => $validated['name'],
                    'email' => $user->email,
                    'phone' => $validated['phone'] ?? $user->phone,
                    'governorate' => $validated['governorate'],
                    'job' => $validated['job'],
                    'job_other' => $validated['job'] === MarketingCustomerSurvey::OTHER ? ($validated['job_other'] ?? null) : null,
                    'heard_from' => $validated['heard_from'],
                    'heard_from_other' => $validated['heard_from'] === MarketingCustomerSurvey::OTHER ? ($validated['heard_from_other'] ?? null) : null,
                    'interested_in' => $validated['interested_in'],
                    'opinion' => $validated['opinion'],
                    'needed_courses' => $validated['needed_courses'] ?? null,
                    'recommendations' => $validated['recommendations'] ?? null,
                    'reward_percentage' => CustomerSurveyRewardService::DISCOUNT_PERCENTAGE,
                    'ip_address' => $request->ip(),
                    'user_agent' => substr((string) $request->userAgent(), 0, 1000),
                ]);

                $coupon = $this->rewards->grant($survey);

                return ['survey' => $survey, 'coupon' => $coupon];
            });
        } catch (\Throwable $e) {
            Log::error('Customer survey submission failed', [
                'user_id' => $user->id,
                'message' => $e->getMessage(),
            ]);

            throw ValidationException::withMessages([
                'opinion' => 'تعذر حفظ الاستبيان. من فضلك حاول مرة أخرى، وإن استمرت المشكلة تواصل معنا.',
            ]);
        }

        return redirect()->route('public.customer-survey.show')
            ->with('survey_reward', [
                'code' => $result['coupon']?->code,
                'percentage' => $result['survey']->reward_percentage,
                'expires_at' => $result['coupon']?->expires_at?->format('Y-m-d'),
                'name' => $result['survey']->name,
            ]);
    }

    private function findUserByEmail(string $email): ?User
    {
        $email = mb_strtolower(trim($email));

        if ($email === '' || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return null;
        }

        return User::whereRaw('LOWER(email) = ?', [$email])->first();
    }

    /**
     * الكورسات التي اشتراها العميل فعلاً (تسجيل نشط/مكتمل أو طلب معتمد).
     *
     * @return Collection<int, AdvancedCourse>
     */
    private function purchasedCourses(User $user): Collection
    {
        $enrolledIds = StudentCourseEnrollment::where('user_id', $user->id)
            ->whereIn('status', ['active', 'completed'])
            ->pluck('advanced_course_id');

        $orderedIds = Order::where('user_id', $user->id)
            ->where('status', Order::STATUS_APPROVED)
            ->whereNotNull('advanced_course_id')
            ->pluck('advanced_course_id');

        $ids = $enrolledIds->merge($orderedIds)->filter()->unique()->values();

        if ($ids->isEmpty()) {
            return collect();
        }

        return AdvancedCourse::whereIn('id', $ids)
            ->orderBy('title')
            ->get(['id', 'title']);
    }

    /**
     * @return array{name: string, email: string, phone: ?string, courses: array<int, array{id: int, title: string}>, already_submitted: bool, coupon_code: ?string}
     */
    private function customerPayload(User $user): array
    {
        $survey = MarketingCustomerSurvey::with('rewardCoupon')
            ->where('user_id', $user->id)
            ->first();

        return [
            'name' => (string) $user->name,
            'email' => (string) $user->email,
            'phone' => $user->phone,
            'courses' => $this->purchasedCourses($user)
                ->map(fn (AdvancedCourse $course) => [
                    'id' => $course->id,
                    'title' => (string) $course->title,
                ])->all(),
            'already_submitted' => (bool) $survey,
            'coupon_code' => $survey?->rewardCoupon?->code,
        ];
    }
}
