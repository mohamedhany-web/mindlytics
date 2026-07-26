<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\AdvancedCourse;
use App\Models\AcademicYear;
use App\Models\Coupon;
use App\Models\StudentCourseEnrollment;
use App\Models\LearningPathEnrollment;
use App\Models\User;
use App\Services\CustomerDiscountService;
use App\Services\InstructorCoursePercentageService;
use App\Models\Order;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Transaction;
use App\Services\FawaterakService;
use App\Services\GatewayFeeCalculator;
use App\Services\KashierService;
use App\Services\PaymentGatewaySettings;
use App\Support\BranchContext;
use App\Support\PlatformSettings;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;

class CheckoutController extends Controller
{
    /**
     * تفصيل السعر بعد تطبيق خصم العميل الشخصي (كوبون الاستبيان/الإحالة/الورشة) تلقائياً.
     *
     * @return array{coupon: ?Coupon, pricing: array{original_amount: float, course_discount: float, coupon_discount: float, discount_amount: float, amount: float, coupon: ?Coupon}}
     */
    private function personalPricing(AdvancedCourse $course): array
    {
        $service = app(CustomerDiscountService::class);
        $user = Auth::user();
        $coupon = $user ? $service->bestCouponForCourse($user, $course) : null;
        $pricing = $service->breakdown($course, $coupon);

        // لا نستهلك كوبوناً يجعل المبلغ صفراً في مسار دفع مأجور.
        if ($coupon && $pricing['amount'] <= 0) {
            $coupon = null;
            $pricing = $service->breakdown($course, null);
        }

        return ['coupon' => $pricing['coupon'], 'pricing' => $pricing];
    }

    /**
     * تسجيل استهلاك كوبون الطلب بعد اعتماده (آمن للتكرار).
     */
    private function consumeOrderCoupon(Order $order, ?int $invoiceId = null): void
    {
        if (! $order->coupon_id || (float) $order->discount_amount <= 0) {
            return;
        }

        $coupon = Coupon::find($order->coupon_id);
        $user = $order->user ?: User::find($order->user_id);

        if (! $coupon || ! $user) {
            return;
        }

        app(CustomerDiscountService::class)->markUsed(
            $coupon,
            $user,
            (float) $order->original_amount,
            (float) $order->discount_amount,
            $invoiceId
        );
    }

    /**
     * عرض صفحة إتمام الطلب
     */
    public function show($courseId)
    {
        $course = AdvancedCourse::where('id', $courseId)
            ->where('is_active', true)
            ->publicCatalog()
            ->with(['academicSubject', 'academicYear'])
            ->firstOrFail();

        if (Auth::check()) {
            $isEnrolled = StudentCourseEnrollment::where('user_id', Auth::id())
                ->where('advanced_course_id', $course->id)
                ->where('status', 'active')
                ->exists();

            if ($isEnrolled) {
                return redirect()->route('public.course.show', $course->id)
                    ->with('info', 'أنت مسجل بالفعل في هذا الكورس');
            }

            $existingOrder = Order::where('user_id', Auth::id())
                ->where('advanced_course_id', $course->id)
                ->where('status', Order::STATUS_PENDING)
                ->first();

            if ($existingOrder) {
                $payMode = PlatformSettings::paymentMode();
                $canOnlineRetry = $existingOrder->payment_method === 'online'
                    && $existingOrder->payment_proof === null
                    && in_array($payMode, ['kashier', 'fawaterak'], true);
                if (! $canOnlineRetry) {
                    return redirect()->route('public.course.show', $course->id)
                        ->with('info', 'لديك طلب قيد الانتظار لهذا الكورس');
                }
            }
        }

        $wallets = \App\Models\Wallet::academyWallets()
            ->where('is_active', true)
            ->whereNotNull('type')
            ->whereIn('type', ['vodafone_cash', 'instapay', 'bank_transfer'])
            ->where(function ($query) {
                $query->whereNotNull('account_number')
                    ->orWhereNotNull('name');
            })
            ->orderBy('type')
            ->orderBy('name')
            ->get();

        $platformPaymentMode = PlatformSettings::paymentMode();
        $fawaterakCheckoutReady = PaymentGatewaySettings::isFawaterakEnabled();
        $phoneCountries = config('phone_countries.countries', []);
        $defaultCountry = collect($phoneCountries)->firstWhere('code', config('phone_countries.default_country', 'SA'));

        $personal = $this->personalPricing($course);
        $personalCoupon = $personal['coupon'];
        $checkoutPricing = $personal['pricing'];

        return view('public.checkout', compact(
            'course',
            'wallets',
            'platformPaymentMode',
            'fawaterakCheckoutReady',
            'phoneCountries',
            'defaultCountry',
            'personalCoupon',
            'checkoutPricing'
        ));
    }

    /**
     * تسجيل سريع أثناء الدفع (JSON) — ينشئ حساب طالب ويسجّل الدخول فوراً.
     */
    public function quickRegister(Request $request, $courseId): JsonResponse
    {
        if (Auth::check()) {
            return response()->json(['success' => true, 'already_authenticated' => true]);
        }

        AdvancedCourse::where('id', $courseId)
            ->where('is_active', true)
            ->publicCatalog()
            ->firstOrFail();

        if (! $request->filled('password_confirmation') && $request->filled('password')) {
            $request->merge(['password_confirmation' => $request->input('password')]);
        }

        $countries = config('phone_countries.countries', []);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'country_code' => 'required|string|max:10',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|unique:users',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ], [
            'name.required' => 'الاسم مطلوب',
            'country_code.required' => 'كود الدولة مطلوب',
            'phone.required' => 'رقم الهاتف مطلوب',
            'email.required' => 'البريد الإلكتروني مطلوب',
            'email.email' => 'البريد الإلكتروني غير صحيح',
            'email.unique' => 'البريد الإلكتروني مسجل مسبقاً',
            'password.required' => 'كلمة المرور مطلوبة',
            'password.confirmed' => 'تأكيد كلمة المرور غير متطابق',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors(),
            ], 422);
        }

        $country = collect($countries)->firstWhere('dial_code', $request->country_code);
        if (! $country || ! isset($country['validation']['regex'])) {
            return response()->json([
                'success' => false,
                'message' => 'كود الدولة غير مدعوم.',
                'errors' => ['country_code' => ['كود الدولة غير مدعوم.']],
            ], 422);
        }

        $nationalNumber = preg_replace('/\D/', '', (string) $request->phone);
        $nationalNumber = ltrim($nationalNumber, '0');
        if (! preg_match($country['validation']['regex'], $nationalNumber)) {
            $example = $country['example'] ?? $country['placeholder'] ?? '';

            return response()->json([
                'success' => false,
                'message' => 'رقم الهاتف غير صحيح لهذه الدولة. مثال: '.$example,
                'errors' => ['phone' => ['رقم الهاتف غير صحيح لهذه الدولة. مثال: '.$example]],
            ], 422);
        }

        $dial = $country['dial_code'] ?? '';
        $fullPhone = ($dial === '' || $dial === 'OTHER') ? ('OTHER_'.$nationalNumber) : ($dial.$nationalNumber);
        if (User::where('phone', $fullPhone)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'رقم الهاتف مسجل مسبقاً',
                'errors' => ['phone' => ['رقم الهاتف مسجل مسبقاً']],
            ], 422);
        }

        $branchId = app(BranchContext::class)->id();

        $user = User::create([
            'name' => $request->name,
            'phone' => $fullPhone,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'student',
            'is_active' => true,
            'branch_id' => $branchId,
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return response()->json([
            'success' => true,
            'csrf_token' => csrf_token(),
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
        ]);
    }

    /**
     * التوجيه لبوابة الدفع كاشير (كورس).
     * طلبات JSON (صفحة checkout مع iframe): تُرجع session_url و session_id.
     */
    public function redirectToKashier(Request $request, $courseId)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'يجب تسجيل الدخول أولاً');
        }

        $course = AdvancedCourse::where('id', $courseId)
            ->where('is_active', true)
            ->publicCatalog()
            ->firstOrFail();

        $isEnrolled = StudentCourseEnrollment::where('user_id', Auth::id())
            ->where('advanced_course_id', $course->id)
            ->where('status', 'active')
            ->exists();
        if ($isEnrolled) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'أنت مسجل بالفعل في هذا الكورس'], 422);
            }

            return redirect()->route('public.course.show', $course->id)
                ->with('info', 'أنت مسجل بالفعل في هذا الكورس');
        }

        if ($course->effectivePrice() <= 0) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'هذا الكورس مجاني يمكنك التسجيل مباشرة.'], 422);
            }

            return redirect()->route('public.course.show', $course->id)
                ->with('info', 'هذا الكورس مجاني يمكنك التسجيل مباشرة.');
        }

        $personal = $this->personalPricing($course);
        $amount = (float) $personal['pricing']['amount'];

        $payMode = PlatformSettings::paymentMode();
        if ($payMode === 'manual') {
            $msg = 'الدفع الإلكتروني غير مفعّل. يرجى استخدام التحويل البنكي ورفع إيصال الدفع من صفحة إتمام الطلب.';
            if ($request->expectsJson()) {
                return response()->json(['message' => $msg], 422);
            }

            return redirect()->route('public.course.checkout', $course->id)->with('error', $msg);
        }
        if ($payMode === 'fawaterak') {
            $msg = PaymentGatewaySettings::isFawaterakEnabled()
                ? 'الدفع الإلكتروني لهذا الكورس يتم عبر بوابة فواتيرك من نفس الصفحة.'
                : 'وضع فواتيرك مفعّل لكن إعدادات الإطار غير مكتملة. راجع لوحة الإدارة أو ملف البيئة.';
            if ($request->expectsJson()) {
                return response()->json(['message' => $msg], 422);
            }

            return redirect()->route('public.course.checkout', $course->id)->with('error', $msg);
        }

        $existingOrder = Order::where('user_id', Auth::id())
            ->where('advanced_course_id', $course->id)
            ->where('status', Order::STATUS_PENDING)
            ->first();
        if ($existingOrder && ($existingOrder->payment_method !== 'online' || $existingOrder->payment_proof !== null)) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'لديك طلب قيد الانتظار لهذا الكورس'], 422);
            }

            return redirect()->route('public.course.show', $course->id)
                ->with('info', 'لديك طلب قيد الانتظار لهذا الكورس');
        }

        $orderCreatedThisRequest = false;
        $pricing = $personal['pricing'];
        DB::beginTransaction();
        try {
            if ($existingOrder && $existingOrder->payment_method === 'online' && $existingOrder->payment_proof === null) {
                $order = $existingOrder;
                if ((float) $order->amount !== $amount) {
                    $order->update([
                        'original_amount' => $pricing['original_amount'],
                        'discount_amount' => $pricing['discount_amount'],
                        'amount' => $pricing['amount'],
                        'coupon_id' => $personal['coupon']?->id,
                    ]);
                }
            } else {
                $order = Order::create([
                    'user_id' => Auth::id(),
                    'advanced_course_id' => $course->id,
                    'coupon_id' => $personal['coupon']?->id,
                    'original_amount' => $pricing['original_amount'],
                    'discount_amount' => $pricing['discount_amount'],
                    'amount' => $pricing['amount'],
                    'payment_method' => 'online',
                    'payment_proof' => null,
                    'wallet_id' => null,
                    'notes' => 'دفع عبر بوابة كاشير',
                    'status' => Order::STATUS_PENDING,
                ]);
                $orderCreatedThisRequest = true;
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Kashier redirect: order create failed', ['course_id' => $courseId, 'message' => $e->getMessage()]);
            if ($request->expectsJson()) {
                return response()->json(['message' => 'حدث خطأ أثناء إنشاء الطلب. يرجى المحاولة مرة أخرى.'], 500);
            }

            return redirect()->route('public.course.show', $course->id)
                ->with('error', 'حدث خطأ أثناء إنشاء الطلب. يرجى المحاولة مرة أخرى.');
        }

        $kashier = app(KashierService::class);
        $callbackUrl = $this->getKashierCallbackUrl();
        try {
            $session = $kashier->createPaymentSession(
                (string) $order->id,
                $amount,
                $callbackUrl,
                Auth::user()->email,
                (string) Auth::id(),
                'Course order #' . $order->id
            );
        } catch (\RuntimeException $e) {
            if ($orderCreatedThisRequest) {
                $order->delete();
            }
            if ($request->expectsJson()) {
                return response()->json(['message' => $e->getMessage()], 422);
            }

            return redirect()->route('public.course.checkout', $course->id)
                ->with('error', $e->getMessage());
        } catch (\Exception $e) {
            Log::error('Kashier createPaymentSession failed (course)', ['course_id' => $courseId, 'message' => $e->getMessage()]);
            if ($orderCreatedThisRequest) {
                $order->delete();
            }
            if ($request->expectsJson()) {
                return response()->json(['message' => 'حدث خطأ أثناء إنشاء جلسة الدفع. يرجى المحاولة مرة أخرى.'], 500);
            }

            return redirect()->route('public.course.checkout', $course->id)
                ->with('error', 'حدث خطأ أثناء التوجيه للدفع. يرجى المحاولة مرة أخرى.');
        }

        if ($request->expectsJson()) {
            return response()->json([
                'session_url' => $session['sessionUrl'] ?? null,
                'session_id' => $session['sessionId'] ?? null,
                'order_id' => $order->id,
                'amount' => $amount,
            ]);
        }

        return redirect()->away($session['sessionUrl'] ?? '');
    }

    /**
     * إنشاء جلسة دفع عبر كاشير لمسار تعليمي.
     * - إذا كان الطلب عادي (HTML): نعيد توجيه المستخدم لرابط الدفع (السلوك القديم).
     * - إذا كان الطلب JSON/AJAX: نرجع sessionUrl و sessionId لاستخدامه داخل iframe في نفس الصفحة.
     */
    public function redirectToKashierLearningPath(Request $request, $slug)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'يجب تسجيل الدخول أولاً');
        }

        $learningPath = AcademicYear::active()
            ->visibleOnCurrentHost()
            ->get()
            ->first(fn ($year) => Str::slug($year->name) === $slug);
        if (!$learningPath) {
            abort(404, 'المسار التعليمي غير موجود');
        }

        $isEnrolled = LearningPathEnrollment::where('user_id', Auth::id())
            ->where('academic_year_id', $learningPath->id)
            ->where('status', 'active')
            ->exists();
        if ($isEnrolled) {
            return redirect()->route('public.learning-path.show', $slug)
                ->with('info', 'أنت مسجل بالفعل في هذا المسار');
        }

        $amount = (float) ($learningPath->price ?? 0);
        if ($amount <= 0) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'هذا المسار مجاني يمكنك التسجيل مباشرة.'], 422);
            }
            return redirect()->route('public.learning-path.show', $slug)
                ->with('info', 'هذا المسار مجاني يمكنك التسجيل مباشرة.');
        }

        $payMode = PlatformSettings::paymentMode();
        if ($payMode === 'manual') {
            $msg = 'الدفع الإلكتروني غير مفعّل. يرجى استخدام التحويل البنكي ورفع إيصال الدفع من صفحة إتمام الطلب.';
            if ($request->expectsJson()) {
                return response()->json(['message' => $msg], 422);
            }

            return redirect()->route('public.learning-path.checkout', $slug)->with('error', $msg);
        }
        if ($payMode === 'fawaterak') {
            $msg = 'الدفع الإلكتروني عبر كاشير غير مستخدم. المسارات التعليمية: استخدم الدفع اليدوي من إعدادات المنصة أو تواصل مع الإدارة.';
            if ($request->expectsJson()) {
                return response()->json(['message' => $msg], 422);
            }

            return redirect()->route('public.learning-path.checkout', $slug)->with('error', $msg);
        }

        $existingPathOrder = Order::where('user_id', Auth::id())
            ->where('academic_year_id', $learningPath->id)
            ->where('status', Order::STATUS_PENDING)
            ->first();

        if ($existingPathOrder && ($existingPathOrder->payment_method !== 'online' || $existingPathOrder->payment_proof !== null)) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'لديك طلب قيد الانتظار لهذا المسار'], 422);
            }

            return redirect()->route('public.learning-path.show', $slug)
                ->with('info', 'لديك طلب قيد الانتظار لهذا المسار');
        }

        $orderCreatedThisRequest = false;
        DB::beginTransaction();
        try {
            if ($existingPathOrder && $existingPathOrder->payment_method === 'online' && $existingPathOrder->payment_proof === null) {
                $order = $existingPathOrder;
                if ((float) $order->amount !== $amount) {
                    $order->update([
                        'original_amount' => $amount,
                        'discount_amount' => 0,
                        'amount' => $amount,
                    ]);
                }
            } else {
                $order = Order::create([
                    'user_id' => Auth::id(),
                    'academic_year_id' => $learningPath->id,
                    'original_amount' => $amount,
                    'discount_amount' => 0,
                    'amount' => $amount,
                    'payment_method' => 'online',
                    'payment_proof' => null,
                    'wallet_id' => null,
                    'notes' => 'دفع عبر بوابة كاشير',
                    'status' => Order::STATUS_PENDING,
                ]);
                $orderCreatedThisRequest = true;
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Kashier redirect: order create failed (path)', ['slug' => $slug, 'message' => $e->getMessage()]);
            if ($request->expectsJson()) {
                return response()->json(['message' => 'حدث خطأ أثناء إنشاء الطلب. يرجى المحاولة مرة أخرى.'], 500);
            }

            return redirect()->route('public.learning-path.show', $slug)
                ->with('error', 'حدث خطأ أثناء إنشاء الطلب. يرجى المحاولة مرة أخرى.');
        }

        $kashier = app(KashierService::class);
        $callbackUrl = $this->getKashierCallbackUrl();
        try {
            // إنشاء جلسة دفع جديدة عبر API v3
            $session = $kashier->createPaymentSession(
                (string) $order->id,
                $amount,
                $callbackUrl,
                Auth::user()->email,
                (string) Auth::id(),
                'Learning path order #' . $order->id
            );
        } catch (\RuntimeException $e) {
            if ($orderCreatedThisRequest) {
                $order->delete();
            }
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => $e->getMessage(),
                ], 422);
            }
            return redirect()->route('public.learning-path.checkout', $slug)
                ->with('error', $e->getMessage());
        } catch (\Exception $e) {
            Log::error('Kashier createPaymentSession failed (path)', ['slug' => $slug, 'message' => $e->getMessage()]);
            if ($orderCreatedThisRequest) {
                $order->delete();
            }
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'حدث خطأ أثناء إنشاء جلسة الدفع. يرجى المحاولة مرة أخرى.',
                ], 500);
            }
            return redirect()->route('public.learning-path.checkout', $slug)
                ->with('error', 'حدث خطأ أثناء التوجيه للدفع. يرجى المحاولة مرة أخرى.');
        }

        // طلب AJAX من صفحة البلاتفورم: نرجع بيانات الجلسة لاستخدامها في iframe داخل نفس الصفحة
        if ($request->expectsJson()) {
            return response()->json([
                'session_url' => $session['sessionUrl'] ?? null,
                'session_id' => $session['sessionId'] ?? null,
                'order_id' => $order->id,
                'amount' => $amount,
            ]);
        }

        // السلوك القديم: إعادة توجيه كاملة لرابط الدفع
        return redirect()->away($session['sessionUrl'] ?? '');
    }

    /**
     * رابط العودة من كاشير: نفس منطق KashierService::merchantRedirectForKashier() (APP_URL أو KASHIER_MERCHANT_REDIRECT_URL).
     */
    private function getKashierCallbackUrl(): string
    {
        return app(KashierService::class)->merchantRedirectForKashier();
    }

    /**
     * استقبال callback من بوابة كاشير بعد إتمام الدفع
     */
    public function kashierCallback(Request $request)
    {
        if (PlatformSettings::paymentMode() === 'fawaterak') {
            return redirect()->route('public.courses')
                ->with('error', 'الدفع عبر كاشير غير مستخدم. الدفع الإلكتروني للكورس يتم عبر فواتيرك.');
        }

        $kashier = app(KashierService::class);
        $query = $request->query();

        if (!$kashier->validateCallback($query)) {
            Log::warning('Kashier callback: invalid signature', ['query_keys' => array_keys($query)]);
            return redirect()->route('public.courses')->with('error', 'فشل التحقق من الدفع. يرجى التواصل مع الدعم.');
        }

        $merchantOrderId = $query['merchantOrderId'] ?? null;
        if (!$merchantOrderId || !ctype_digit((string) $merchantOrderId)) {
            Log::warning('Kashier callback: invalid merchantOrderId', ['merchantOrderId' => $merchantOrderId]);
            return redirect()->route('public.courses')->with('error', 'بيانات الطلب غير صحيحة.');
        }

        $order = Order::with(['course', 'learningPath'])->find($merchantOrderId);
        if (!$order || $order->status !== Order::STATUS_PENDING) {
            Log::warning('Kashier callback: order not found or not pending', ['order_id' => $merchantOrderId]);
            return redirect()->route('public.courses')->with('error', 'الطلب غير موجود أو تم معالجته مسبقاً.');
        }

        if (!$kashier->isPaymentSuccess($query)) {
            if ($order->academic_year_id) {
                $slug = Str::slug($order->learningPath->name ?? '');
                return redirect()->route('public.learning-path.show', $slug)
                    ->with('error', 'لم يتم إتمام الدفع. يمكنك المحاولة مرة أخرى.');
            }
            return redirect()->route('public.course.show', $order->advanced_course_id)
                ->with('error', 'لم يتم إتمام الدفع. يمكنك المحاولة مرة أخرى.');
        }

        DB::beginTransaction();
        try {
            $this->approveOrderAfterOnlinePayment(
                $order,
                'kashier',
                $query['transactionId'] ?? null,
                $query,
                'كاشير'
            );
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Kashier callback: approval failed', [
                'order_id' => $order->id,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return redirect()->route('public.courses')->with('error', 'حدث خطأ أثناء تفعيل الطلب. يرجى التواصل مع الدعم.');
        }

        if ($order->academic_year_id) {
            $slug = Str::slug($order->learningPath->name ?? '');
            return redirect()->route('public.learning-path.show', $slug)
                ->with('success', 'تم الدفع بنجاح! تم تفعيل المسار التعليمي على حسابك.');
        }

        return $this->redirectAfterPaidCourseEnrollment(
            (int) $order->advanced_course_id,
            'تم الدفع بنجاح! تم تفعيل الكورس على حسابك.'
        );
    }

    /**
     * بعد شراء كورس بنجاح: الطالب → صفحة التعلم مع نافذة نجاح وعدّاد؛ غير الطالب → صفحة الكورس العامة ثم توجيه للتعلم بعد العدّاد.
     */
    private function redirectAfterPaidCourseEnrollment(int $advancedCourseId, string $successMessage): RedirectResponse
    {
        $user = Auth::user();
        if ($user && $user->isStudent()) {
            return redirect()
                ->route('my-courses.learn', $advancedCourseId)
                ->with('success', $successMessage)
                ->with('payment_success_modal', true);
        }

        return redirect()
            ->route('public.course.show', $advancedCourseId)
            ->with('success', $successMessage)
            ->with('payment_success_modal', true)
            ->with('payment_success_redirect_url', route('my-courses.learn', $advancedCourseId));
    }

    /**
     * تجهيز طلب كورس + إعدادات إضافة فواتيرك (iframe).
     */
    public function fawaterakPrepare(Request $request, $courseId)
    {
        if (! Auth::check()) {
            return response()->json(['message' => 'يجب تسجيل الدخول'], 401);
        }

        if (! PaymentGatewaySettings::isFawaterakEnabled()) {
            return response()->json(['message' => 'بوابة فواتيرك غير مفعّلة أو غير مهيأة.'], 503);
        }

        $fawaterak = app(FawaterakService::class);
        if (! $fawaterak->isConfigured()) {
            return response()->json(['message' => 'إعداد فواتيرك غير مكتمل (مفاتيح Vendor/Provider).'], 503);
        }

        $course = AdvancedCourse::where('id', $courseId)
            ->where('is_active', true)
            ->publicCatalog()
            ->firstOrFail();

        $isEnrolled = StudentCourseEnrollment::where('user_id', Auth::id())
            ->where('advanced_course_id', $course->id)
            ->where('status', 'active')
            ->exists();
        if ($isEnrolled) {
            return response()->json(['message' => 'أنت مسجل بالفعل في هذا الكورس'], 422);
        }

        if ($course->effectivePrice() <= 0) {
            return response()->json(['message' => 'هذا الكورس مجاني يمكنك التسجيل مباشرة.'], 422);
        }

        $personal = $this->personalPricing($course);
        $pricing = $personal['pricing'];
        $amount = (float) $pricing['amount'];
        $user = Auth::user();
        $email = trim((string) ($user->email ?? ''));
        if ($email === '' || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return response()->json(['message' => 'يرجى إضافة بريد إلكتروني صالح في الملف الشخصي قبل الدفع.'], 422);
        }

        $browserHostname = $request->input('browser_hostname');
        $browserHostname = is_string($browserHostname) ? $browserHostname : null;
        $browserScheme = $request->input('browser_protocol');
        $browserScheme = is_string($browserScheme) ? $browserScheme : null;
        if ($fawaterak->normalizeBrowserScheme($browserScheme) === null) {
            $browserScheme = $request->getScheme().':';
        }

        $fawaterakWarning = null;
        if (config('fawaterak.iframe_preflight', true)) {
            try {
                $pf = $fawaterak->preflightGetPaymentMethods($browserHostname, $browserScheme);
                if ($pf->status() === 400) {
                    $domainUsed = $fawaterak->hashDomain($browserHostname, $browserScheme);
                    $overrideRaw = trim((string) config('fawaterak.iframe_domain', ''));
                    $overrideNorm = $overrideRaw !== '' ? rtrim($overrideRaw, '/') : '';

                    $message = 'رفض فواتيرك التحقق من توقيع الـ iframe (HMAC). النطاق المستخدم في التوقيع: '.$domainUsed.'. ';
                    if ($overrideNorm !== '' && $overrideNorm !== $domainUsed) {
                        $message .= 'تعارض: لديك FAWATERAK_IFRAME_DOMAIN='.$overrideNorm.' بينما التوقيع يتبع المتصفح ('.$domainUsed.'). احذف السطر من .env أو افتح الموقع بنفس البروتوكول المسجّل في لوحة فواتيرك (غالباً HTTPS). ';
                    }
                    $message .= '(1) تفعيل التاجر وطريقة دفع واحدة على الأقل. (2) تطابق FAWATERAK_ENV مع نوع المفاتيح (test/live). (3) إن وُجد في اللوحة سر توقيع منفصل عن API Key ضعه في FAWATERAK_HMAC_SECRET — إن نجحت طلبات بدون HASH ورفضت مع HASH فالسبب غالباً السر وليس Bearer. (4) أضف نفس الأصل في «IFrame domains» بدون / أخيرة وبدون منفذ؛ وثائق فواتيرك تشترط غالباً HTTPS فقط، فجرّب تشغيل الموقع محلياً بـ https إن بقيت على http.';

                    return response()->json([
                        'message' => $message,
                        'code' => 'fawaterak_hmac_rejected',
                        'hash_domain' => $domainUsed,
                        'iframe_domain_env' => $overrideNorm !== '' ? $overrideNorm : null,
                    ], 422);
                }
                if ($pf->successful()) {
                    $payload = $pf->json();
                    if (($payload['status'] ?? '') === 'success'
                        && is_array($payload['data'] ?? null)
                        && count($payload['data']) === 0) {
                        $fawaterakWarning = 'لا توجد طرق دفع مفعّلة في لوحة فواتيرك؛ أضف طريقة دفع واحدة على الأقل.';
                    }
                }
            } catch (\Throwable $e) {
                Log::warning('Fawaterak iframe preflight failed', ['message' => $e->getMessage()]);
            }
        }

        $existingOrder = Order::where('user_id', Auth::id())
            ->where('advanced_course_id', $course->id)
            ->where('status', Order::STATUS_PENDING)
            ->first();
        if ($existingOrder && ($existingOrder->payment_method !== 'online' || $existingOrder->payment_proof !== null)) {
            return response()->json(['message' => 'لديك طلب قيد الانتظار لهذا الكورس'], 422);
        }

        DB::beginTransaction();
        try {
            if ($existingOrder && $existingOrder->payment_method === 'online' && $existingOrder->payment_proof === null) {
                $order = $existingOrder;
                if ((float) $order->amount !== $amount) {
                    $order->update([
                        'original_amount' => $pricing['original_amount'],
                        'discount_amount' => $pricing['discount_amount'],
                        'amount' => $pricing['amount'],
                        'coupon_id' => $personal['coupon']?->id,
                        'notes' => 'دفع عبر فواتيرك (iframe)',
                    ]);
                }
            } else {
                $order = Order::create([
                    'user_id' => Auth::id(),
                    'advanced_course_id' => $course->id,
                    'coupon_id' => $personal['coupon']?->id,
                    'original_amount' => $pricing['original_amount'],
                    'discount_amount' => $pricing['discount_amount'],
                    'amount' => $pricing['amount'],
                    'payment_method' => 'online',
                    'payment_proof' => null,
                    'wallet_id' => null,
                    'notes' => 'دفع عبر فواتيرك (iframe)',
                    'status' => Order::STATUS_PENDING,
                ]);
            }
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Fawaterak prepare: order failed', ['course_id' => $courseId, 'message' => $e->getMessage()]);

            return response()->json(['message' => 'حدث خطأ أثناء إنشاء الطلب.'], 500);
        }

        session(['fawaterak_order_id' => $order->id]);

        $name = trim((string) ($user->name ?? ''));
        $nameParts = preg_split('/\s+/', $name, 2, PREG_SPLIT_NO_EMPTY) ?: [];
        $firstName = $nameParts[0] ?? 'Student';
        $lastName = $nameParts[1] ?? $firstName;
        $phone = trim((string) ($user->phone ?? ''));
        if ($phone === '') {
            $phone = '01000000000';
        }

        $courseTitle = $course->localized('title') ?: ($course->title ?? 'كورس');
        $cartTotal = (string) round($amount, 2);
        $currency = config('fawaterak.currency', 'EGP');

        $pluginConfig = [
            'envType' => $fawaterak->envType(),
            'hashKey' => $fawaterak->generateHashKey($browserHostname, $browserScheme),
            // الإضافة الحالية تستخدم token لطلبات API (قد لا يظهر في مثال الوثائق القصير)
            'token' => $fawaterak->checkoutPluginBearerToken(),
            'version' => (string) config('fawaterak.version', '0'),
            'redirectOutIframe' => true,
            'style' => [
                'listing' => 'horizontal',
            ],
            'requestBody' => [
                'cartTotal' => $cartTotal,
                'currency' => $currency,
                'customer' => [
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'email' => $email,
                    'phone' => $phone,
                    'address' => '-',
                    'customer_unique_id' => (string) $user->id,
                ],
                'redirectionUrls' => [
                    'successUrl' => route('public.checkout.fawaterak.return', ['status' => 'success'], true),
                    'failUrl' => route('public.checkout.fawaterak.return', ['status' => 'fail'], true),
                    'pendingUrl' => route('public.checkout.fawaterak.return', ['status' => 'pending'], true),
                ],
                'cartItems' => [
                    [
                        'name' => $courseTitle,
                        'price' => $cartTotal,
                        'quantity' => '1',
                    ],
                ],
                'payLoad' => [
                    'order_id' => (string) $order->id,
                    'user_id' => (string) $user->id,
                    'course_id' => (string) $course->id,
                ],
            ],
        ];

        $json = [
            'mode' => 'iframe',
            'pluginScriptUrl' => $fawaterak->proxiedPluginScriptUrl(),
            'pluginConfig' => $pluginConfig,
            'order_id' => $order->id,
        ];
        if ($fawaterakWarning !== null) {
            $json['fawaterak_warning'] = $fawaterakWarning;
        }

        return response()->json($json);
    }

    /**
     * عودة المستخدم بعد الدفع (فواتيرك iframe).
     */
    public function fawaterakReturn(Request $request, string $status)
    {
        if (! Auth::check()) {
            return redirect()->guest(route('login'))->with('info', 'يرجى تسجيل الدخول لمتابعة نتيجة الدفع');
        }

        if (! in_array($status, ['success', 'fail', 'pending'], true)) {
            abort(404);
        }

        $orderId = session()->pull('fawaterak_order_id');

        if ($status !== 'success') {
            $msg = $status === 'pending'
                ? 'حالة الدفع معلّقة. سيتم تحديث حالة طلبك عند اكتمال المعالجة.'
                : 'لم يتم إتمام الدفع. يمكنك المحاولة مرة أخرى من صفحة إتمام الطلب.';

            if ($orderId) {
                $ord = Order::find($orderId);
                if ($ord && $ord->advanced_course_id) {
                    return redirect()->route('public.course.checkout', $ord->advanced_course_id)->with('error', $msg);
                }
            }

            return redirect()->route('public.courses')->with('error', $msg);
        }

        if (! $orderId) {
            return redirect()->route('public.courses')
                ->with('error', 'انتهت الجلسة أو فُقد رقم الطلب. إذا تم خصم المبلغ، تواصل مع الدعم مع بريدك المسجّل.');
        }

        $order = Order::with(['course', 'learningPath'])->find($orderId);
        if (! $order || (int) $order->user_id !== (int) Auth::id()) {
            return redirect()->route('public.courses')->with('error', 'الطلب غير صالح.');
        }

        if (! $order->advanced_course_id) {
            return redirect()->route('public.courses')->with('error', 'هذا المسار غير مدعوم لفواتيرك من الواجهة الحالية.');
        }

        if ($order->status === Order::STATUS_APPROVED) {
            $u = Auth::user();
            if ($u && $u->isStudent()) {
                return redirect()->route('my-courses.learn', $order->advanced_course_id)
                    ->with('info', 'تمت معالجة هذا الطلب مسبقاً.');
            }

            return redirect()->route('public.course.show', $order->advanced_course_id)
                ->with('info', 'تمت معالجة هذا الطلب مسبقاً.');
        }

        if ($order->status !== Order::STATUS_PENDING) {
            return redirect()->route('public.course.show', $order->advanced_course_id)
                ->with('error', 'لا يمكن إتمام الدفع لهذا الطلب.');
        }

        $invoice = null;
        DB::beginTransaction();
        try {
            $order = Order::whereKey($orderId)->lockForUpdate()->first();
            if (! $order || (int) $order->user_id !== (int) Auth::id()) {
                DB::rollBack();

                return redirect()->route('public.courses')->with('error', 'الطلب غير صالح.');
            }
            if ($order->status === Order::STATUS_APPROVED) {
                DB::commit();
                $invoice = $order->invoice_id ? Invoice::find($order->invoice_id) : null;
            } else {
                $invoice = $this->approveOrderAfterOnlinePayment(
                    $order,
                    'fawaterak',
                    null,
                    ['source' => 'fawaterak_iframe', 'query' => $request->query()],
                    'فواتيرك'
                );
                DB::commit();
            }
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Fawaterak return: approval failed', [
                'order_id' => $orderId,
                'message' => $e->getMessage(),
            ]);

            $failedOrder = Order::find($orderId);
            if ($failedOrder && $failedOrder->advanced_course_id) {
                return redirect()->route('public.course.checkout', $failedOrder->advanced_course_id)
                    ->with('error', 'حدث خطأ أثناء تفعيل الطلب. يرجى التواصل مع الدعم.');
            }

            return redirect()->route('public.courses')->with('error', 'حدث خطأ أثناء تفعيل الطلب. يرجى التواصل مع الدعم.');
        }

        $invNo = $invoice ? $invoice->invoice_number : '';

        return $this->redirectAfterPaidCourseEnrollment(
            (int) $order->advanced_course_id,
            'تم الدفع بنجاح! تم تفعيل الكورس. رقم الفاتورة المحلية: '.$invNo
        );
    }

    /**
     * قبول الطلب بعد دفع أونلاين (كاشير / فواتيرك).
     *
     * @param  array<string, mixed>  $gatewayResponse
     */
    private function approveOrderAfterOnlinePayment(
        Order $order,
        string $paymentGateway,
        ?string $transactionId,
        array $gatewayResponse,
        string $gatewayLabel,
    ): Invoice {
        $order->loadMissing(['course', 'learningPath']);

        if ($order->status !== Order::STATUS_PENDING) {
            throw new \RuntimeException('Order is not pending');
        }

        $isLearningPath = ! empty($order->academic_year_id);
        $orderTitle = $isLearningPath
            ? ($order->learningPath->name ?? 'مسار تعليمي')
            : ($order->course ? ($order->course->localized('title') ?: $order->course->title) : 'كورس');

        $noteLine = 'دفع عبر '.$gatewayLabel.' - طلب #'.$order->id;

        $invoiceNumber = 'INV-' . str_pad(Invoice::count() + 1, 8, '0', STR_PAD_LEFT);
        $invoice = Invoice::create([
            'invoice_number' => $invoiceNumber,
            'user_id' => $order->user_id,
            'type' => $isLearningPath ? 'learning_path' : 'course',
            'description' => $isLearningPath ? 'تسجيل في المسار: '.$orderTitle : 'تسجيل في الكورس: '.$orderTitle,
            'subtotal' => $order->amount,
            'tax_amount' => 0,
            'discount_amount' => 0,
            'total_amount' => $order->amount,
            'status' => 'paid',
            'due_date' => now(),
            'paid_at' => now(),
            'notes' => $noteLine,
            'items' => [
                [
                    'description' => $isLearningPath ? 'المسار: '.$orderTitle : 'الكورس: '.$orderTitle,
                    'quantity' => 1,
                    'price' => $order->amount,
                    'total' => $order->amount,
                ],
            ],
        ]);

        $gross = (float) $order->amount;
        $feeCalc = GatewayFeeCalculator::calculate($gross);
        $applyFee = GatewayFeeCalculator::appliesToGateway($paymentGateway);
        $feeAmount = $applyFee ? $feeCalc['fee'] : 0.0;
        $feeDetail = $applyFee ? $feeCalc['detail'] : null;

        $paymentNumber = 'PAY-' . str_pad(Payment::count() + 1, 8, '0', STR_PAD_LEFT);
        $payment = Payment::create([
            'payment_number' => $paymentNumber,
            'invoice_id' => $invoice->id,
            'order_id' => $order->id,
            'user_id' => $order->user_id,
            'payment_method' => 'online',
            'payment_gateway' => $paymentGateway,
            'amount' => $order->amount,
            'gateway_fee_amount' => $feeAmount > 0 ? $feeAmount : null,
            'gateway_fee_detail' => $feeDetail,
            'currency' => 'EGP',
            'status' => 'completed',
            'transaction_id' => $transactionId,
            'gateway_response' => $gatewayResponse,
            'paid_at' => now(),
            'notes' => $noteLine,
        ]);

        $transactionNumber = 'TXN-' . str_pad(Transaction::count() + 1, 8, '0', STR_PAD_LEFT);
        Transaction::create([
            'transaction_number' => $transactionNumber,
            'user_id' => $order->user_id,
            'payment_id' => $payment->id,
            'invoice_id' => $invoice->id,
            'expense_id' => null,
            'subscription_id' => null,
            'type' => 'credit',
            'category' => 'course_payment',
            'amount' => $order->amount,
            'currency' => 'EGP',
            'description' => ($isLearningPath ? 'دفع مسار: ' : 'دفع كورس: ').$orderTitle.' - طلب #'.$order->id,
            'status' => 'completed',
            'metadata' => [
                'order_id' => $order->id,
                'invoice_id' => $invoice->id,
                'payment_id' => $payment->id,
                'course_id' => $order->advanced_course_id,
                'academic_year_id' => $order->academic_year_id,
                'payment_gateway' => $paymentGateway,
            ],
        ]);

        if ($feeAmount > 0) {
            $feeTxnNumber = 'TXN-' . str_pad(Transaction::count() + 1, 8, '0', STR_PAD_LEFT);
            Transaction::create([
                'transaction_number' => $feeTxnNumber,
                'user_id' => $order->user_id,
                'payment_id' => $payment->id,
                'invoice_id' => $invoice->id,
                'expense_id' => null,
                'subscription_id' => null,
                'type' => 'debit',
                'category' => 'fee',
                'amount' => $feeAmount,
                'currency' => 'EGP',
                'description' => 'عمولة بوابة الدفع ('.$gatewayLabel.') — '.$payment->payment_number,
                'status' => 'completed',
                'metadata' => [
                    'kind' => 'payment_gateway_fee',
                    'order_id' => $order->id,
                    'invoice_id' => $invoice->id,
                    'payment_id' => $payment->id,
                    'related_transaction_number' => $transactionNumber,
                    'gateway' => $paymentGateway,
                ],
            ]);
        }

        $order->update([
            'status' => Order::STATUS_APPROVED,
            'approved_at' => now(),
            'approved_by' => null,
            'invoice_id' => $invoice->id,
            'payment_id' => $payment->id,
        ]);

        $this->consumeOrderCoupon($order, $invoice->id);

        if ($order->academic_year_id) {
            $existingPath = LearningPathEnrollment::where('user_id', $order->user_id)
                ->where('academic_year_id', $order->academic_year_id)
                ->first();
            if (! $existingPath) {
                $pathEnrollment = LearningPathEnrollment::create([
                    'user_id' => $order->user_id,
                    'academic_year_id' => $order->academic_year_id,
                    'status' => 'active',
                    'enrolled_at' => now(),
                    'activated_at' => now(),
                    'activated_by' => $order->user_id,
                    'progress' => 0,
                ]);
                $this->enrollInPathCourses($pathEnrollment);
            } else {
                if ($existingPath->status !== 'active') {
                    $existingPath->update([
                        'status' => 'active',
                        'activated_at' => now(),
                        'activated_by' => $order->user_id,
                    ]);
                    $this->enrollInPathCourses($existingPath);
                }
            }
        }

        if ($order->advanced_course_id) {
            $existingEnrollment = StudentCourseEnrollment::where('user_id', $order->user_id)
                ->where('advanced_course_id', $order->advanced_course_id)
                ->first();
            if (! $existingEnrollment) {
                StudentCourseEnrollment::create([
                    'user_id' => $order->user_id,
                    'advanced_course_id' => $order->advanced_course_id,
                    'enrolled_at' => now(),
                    'activated_at' => now(),
                    'activated_by' => $order->user_id,
                    'status' => 'active',
                    'progress' => 0,
                    'invoice_id' => $invoice->id,
                    'payment_id' => $payment->id,
                    'payment_method' => 'online',
                    'final_price' => $order->amount,
                ]);
            } else {
                $existingEnrollment->update([
                    'status' => 'active',
                    'activated_at' => now(),
                    'activated_by' => $order->user_id,
                    'invoice_id' => $invoice->id,
                    'payment_id' => $payment->id,
                    'payment_method' => 'online',
                    'final_price' => $order->amount,
                ]);
            }
            $enrollment = StudentCourseEnrollment::where('user_id', $order->user_id)
                ->where('advanced_course_id', $order->advanced_course_id)
                ->first();
            if ($enrollment) {
                InstructorCoursePercentageService::processEnrollmentActivation($enrollment);
            }
        }

        return $invoice;
    }

    /**
     * تسجيل الطالب في كورسات المسار (للاستخدام من callback كاشير)
     */
    private function enrollInPathCourses(LearningPathEnrollment $enrollment): void
    {
        $learningPath = $enrollment->learningPath()->with(['academicSubjects'])->first();
        if (!$learningPath) {
            return;
        }
        $courses = collect();
        if (\Illuminate\Support\Facades\Schema::hasTable('academic_year_courses')) {
            try {
                $learningPath->load('linkedCourses');
                $linked = $learningPath->linkedCourses()->where('advanced_courses.is_active', true)->get();
                $courses = $courses->merge($linked);
            } catch (\Throwable $e) {
                Log::warning('enrollInPathCourses: linkedCourses failed', ['message' => $e->getMessage()]);
            }
        }
        $subjects = $learningPath->academicSubjects ?? collect();
        foreach ($subjects as $subject) {
            try {
                $subjectCourses = $subject->advancedCourses()->where('is_active', true)->get();
                $courses = $courses->merge($subjectCourses);
            } catch (\Throwable $e) {
                Log::warning('enrollInPathCourses: subject courses failed', ['subject_id' => $subject->id ?? null]);
            }
        }
        $courses = $courses->unique('id');
        foreach ($courses as $course) {
            try {
                $courseEnrollment = StudentCourseEnrollment::firstOrCreate(
                    [
                        'user_id' => $enrollment->user_id,
                        'advanced_course_id' => $course->id,
                    ],
                    [
                        'status' => 'active',
                        'enrolled_at' => now(),
                        'activated_at' => now(),
                        'activated_by' => $enrollment->user_id,
                        'progress' => 0,
                    ]
                );
                if ($courseEnrollment->status === 'active') {
                    InstructorCoursePercentageService::processEnrollmentActivation($courseEnrollment->fresh());
                }
            } catch (\Throwable $e) {
                Log::warning('enrollInPathCourses: firstOrCreate failed', ['course_id' => $course->id ?? null]);
            }
        }
    }

    /**
     * إتمام الطلب
     */
    public function complete(Request $request, $courseId)
    {
        // التحقق من تسجيل الدخول
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'يجب تسجيل الدخول أولاً');
        }

        if (PlatformSettings::paymentMode() !== 'manual') {
            $msg = PlatformSettings::paymentMode() === 'fawaterak'
                ? 'لا يمكن رفع إيصال يدوي أثناء تفعيل فواتيرك. أكمل الدفع الإلكتروني من صفحة إتمام الطلب.'
                : 'لا يمكن إرسال إيصال يدوي بينما مفعّل الدفع الإلكتروني. استخدم صفحة إتمام الطلب.';

            return redirect()->route('public.course.show', $courseId)->with('error', $msg);
        }

        $course = AdvancedCourse::where('id', $courseId)
            ->where('is_active', true)
            ->publicCatalog()
            ->firstOrFail();

        // التحقق من التسجيل السابق
        $isEnrolled = StudentCourseEnrollment::where('user_id', Auth::id())
            ->where('advanced_course_id', $course->id)
            ->where('status', 'active')
            ->exists();

        if ($isEnrolled) {
            return redirect()->route('public.course.show', $course->id)
                ->with('info', 'أنت مسجل بالفعل في هذا الكورس');
        }

        // منع طلب مكرر: إذا كان هناك طلب قيد الانتظار لنفس الكورس
        $existingPending = Order::where('user_id', Auth::id())
            ->where('advanced_course_id', $course->id)
            ->where('status', Order::STATUS_PENDING)
            ->first();
        if ($existingPending) {
            return redirect()->route('public.course.show', $course->id)
                ->with('info', 'لديك طلب قيد الانتظار لهذا الكورس. يرجى انتظار المراجعة.');
        }

        // التحقق من صحة البيانات (wallet_id: فقط محافظ نشطة ومعروضة في الصفحة)
        $validated = $request->validate([
            'payment_method' => 'required|in:bank_transfer,wallet,online',
            'wallet_id' => [
                'nullable',
                'required_if:payment_method,wallet',
                Rule::exists('wallets', 'id')->where('is_active', true)->whereIn('type', ['vodafone_cash', 'instapay', 'bank_transfer']),
            ],
            'payment_proof' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'notes' => 'nullable|string|max:1000',
        ], [
            'payment_method.required' => 'طريقة الدفع مطلوبة',
            'payment_method.in' => 'طريقة الدفع غير صحيحة',
            'wallet_id.required_if' => 'يجب اختيار محفظة للدفع',
            'wallet_id.exists' => 'المحفظة المختارة غير صالحة أو غير متاحة. يرجى اختيار محفظة من القائمة.',
            'payment_proof.required' => 'صورة إيصال الدفع مطلوبة',
            'payment_proof.image' => 'يجب أن يكون الملف صورة',
            'payment_proof.mimes' => 'يجب أن تكون الصورة بصيغة jpeg, png أو jpg',
            'payment_proof.max' => 'حجم الصورة يجب ألا يتجاوز 2 ميجابايت',
            'notes.max' => 'الملاحظات يجب ألا تتجاوز 1000 حرف',
        ]);

        DB::beginTransaction();
        try {
            // حساب السعر النهائي بعد خصم الكورس + خصم العميل الشخصي
            $personal = $this->personalPricing($course);
            $pricing = $personal['pricing'];
            $originalAmount = $pricing['original_amount'];
            $finalAmount = $pricing['amount'];
            $discountAmount = $pricing['discount_amount'];

            // رفع صورة الإيصال
            $paymentProofPath = $request->file('payment_proof')->store('payment-proofs', 'public');

            // إنشاء الطلب
            $order = Order::create([
                'user_id' => Auth::id(),
                'advanced_course_id' => $course->id,
                'coupon_id' => $personal['coupon']?->id,
                'original_amount' => $originalAmount,
                'discount_amount' => $discountAmount,
                'amount' => $finalAmount,
                'payment_method' => $request->payment_method === 'wallet' ? 'bank_transfer' : $request->payment_method,
                'payment_proof' => $paymentProofPath,
                'wallet_id' => in_array($request->payment_method, ['wallet', 'bank_transfer']) ? ($request->wallet_id ?: null) : null,
                'notes' => $request->notes ?? '',
                'status' => Order::STATUS_PENDING,
            ]);

            DB::commit();

            return redirect()->route('public.course.show', $course->id)
                ->with('success', 'تم استلام طلبك بنجاح. طلبك قيد المراجعة لهذا الكورس وسيتم تفعيله بعد الموافقة.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Checkout complete error: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'course_id' => $courseId,
                'trace' => $e->getTraceAsString(),
            ]);
            return back()->with('error', 'حدث خطأ أثناء إتمام الطلب. يرجى المحاولة مرة أخرى.')
                ->withInput();
        }
    }

    /**
     * تسجيل مجاني للكورسات المجانية
     */
    public function enrollFree($courseId)
    {
        // التحقق من تسجيل الدخول
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'يجب تسجيل الدخول أولاً');
        }

        $course = AdvancedCourse::where('id', $courseId)
            ->where('is_active', true)
            ->publicCatalog()
            ->firstOrFail();

        // التحقق من أن الكورس مجاني
        if ($course->effectivePrice() > 0 && !($course->is_free ?? false)) {
            return redirect()->route('public.course.show', $course->id)
                ->with('error', 'هذا الكورس ليس مجانياً');
        }

        // التحقق من التسجيل السابق
        $existingEnrollment = StudentCourseEnrollment::where('user_id', Auth::id())
            ->where('advanced_course_id', $course->id)
            ->first();

        if ($existingEnrollment && $existingEnrollment->status === 'active') {
            return redirect()->route('public.course.show', $course->id)
                ->with('info', 'أنت مسجل بالفعل في هذا الكورس');
        }

        DB::beginTransaction();
        try {
            // إذا كان هناك تسجيل غير نشط، تفعيله
            $enrollment = null;
            if ($existingEnrollment) {
                $existingEnrollment->update([
                    'status' => 'active',
                    'activated_at' => now(),
                    'activated_by' => Auth::id(),
                ]);
                $enrollment = $existingEnrollment->fresh();
            } else {
                $enrollment = StudentCourseEnrollment::create([
                    'user_id' => Auth::id(),
                    'advanced_course_id' => $course->id,
                    'enrolled_at' => now(),
                    'activated_at' => now(),
                    'activated_by' => Auth::id(),
                    'status' => 'active',
                    'progress' => 0,
                ]);
            }
            if ($enrollment) {
                InstructorCoursePercentageService::processEnrollmentActivation($enrollment);
            }

            DB::commit();

            return redirect()->route('public.course.show', $course->id)
                ->with('success', 'تم تسجيلك في الكورس بنجاح! يمكنك الآن البدء بالتعلم.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('public.course.show', $course->id)
                ->with('error', 'حدث خطأ أثناء التسجيل. يرجى المحاولة مرة أخرى.');
        }
    }

    /**
     * عرض صفحة إتمام الطلب للمسار التعليمي
     */
    public function showLearningPath($slug)
    {
        // التحقق من تسجيل الدخول
        if (!Auth::check()) {
            return redirect()->guest(route('login'))->with('info', 'يرجى تسجيل الدخول أولاً لإتمام عملية الشراء');
        }

        // البحث عن AcademicYear بالاسم (slug)
        $learningPath = AcademicYear::active()
            ->visibleOnCurrentHost()
            ->get()
            ->first(function($year) use ($slug) {
                return Str::slug($year->name) === $slug;
            });
        
        if (!$learningPath) {
            abort(404, 'المسار التعليمي غير موجود');
        }

        // التحقق من التسجيل السابق
        $isEnrolled = LearningPathEnrollment::where('user_id', Auth::id())
            ->where('academic_year_id', $learningPath->id)
            ->where('status', 'active')
            ->exists();

        if ($isEnrolled) {
            return redirect()->route('public.learning-path.show', $slug)
                ->with('info', 'أنت مسجل بالفعل في هذا المسار التعليمي');
        }

        // جلب المحافظ الإلكترونية النشطة
        $wallets = \App\Models\Wallet::academyWallets()
            ->where('is_active', true)
            ->whereNotNull('type')
            ->whereIn('type', ['vodafone_cash', 'instapay', 'bank_transfer'])
            ->where(function($query) {
                $query->whereNotNull('account_number')
                      ->orWhereNotNull('name');
            })
            ->orderBy('type')
            ->orderBy('name')
            ->get();

        $platformPaymentMode = PlatformSettings::paymentMode();
        $fawaterakCheckoutReady = PaymentGatewaySettings::isFawaterakEnabled();

        return view('public.checkout', compact('learningPath', 'wallets', 'platformPaymentMode', 'fawaterakCheckoutReady'));
    }

    /**
     * إتمام الطلب للمسار التعليمي
     */
    public function completeLearningPath(Request $request, $slug)
    {
        // التحقق من تسجيل الدخول
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'يجب تسجيل الدخول أولاً');
        }

        if (PlatformSettings::paymentMode() !== 'manual') {
            $msg = PlatformSettings::paymentMode() === 'fawaterak'
                ? 'لا يمكن رفع إيصال يدوي أثناء تفعيل فواتيرك. الدفع عبر فواتيرك متاح حالياً لشراء الكورسات فقط؛ للمسارات غيّر وضع الدفع إلى «يدوي» من إعدادات النظام أو تواصل مع الإدارة.'
                : 'لا يمكن إرسال إيصال يدوي بينما مفعّل الدفع الإلكتروني. استخدم صفحة إتمام الطلب المناسبة.';

            return redirect()->route('public.learning-path.show', $slug)->with('error', $msg);
        }

        // البحث عن AcademicYear بالاسم (slug)
        $learningPath = AcademicYear::active()
            ->visibleOnCurrentHost()
            ->get()
            ->first(function($year) use ($slug) {
                return Str::slug($year->name) === $slug;
            });
        
        if (!$learningPath) {
            abort(404, 'المسار التعليمي غير موجود');
        }

        // التحقق من التسجيل السابق
        $isEnrolled = LearningPathEnrollment::where('user_id', Auth::id())
            ->where('academic_year_id', $learningPath->id)
            ->where('status', 'active')
            ->exists();

        if ($isEnrolled) {
            return redirect()->route('public.learning-path.show', $slug)
                ->with('info', 'أنت مسجل بالفعل في هذا المسار التعليمي');
        }

        // التحقق من صحة البيانات (wallet_id: عند المحفظة مطلوب، عند التحويل البنكي اختياري)
        $validated = $request->validate([
            'payment_method' => 'required|in:bank_transfer,wallet,online',
            'wallet_id' => [
                'nullable',
                'required_if:payment_method,wallet',
                Rule::exists('wallets', 'id')->where('is_active', true)->whereIn('type', ['vodafone_cash', 'instapay', 'bank_transfer']),
            ],
            'payment_proof' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'notes' => 'nullable|string|max:1000',
        ], [
            'payment_method.required' => 'طريقة الدفع مطلوبة',
            'payment_method.in' => 'طريقة الدفع غير صحيحة',
            'wallet_id.required_if' => 'يجب اختيار محفظة للدفع',
            'wallet_id.exists' => 'المحفظة المختارة غير صالحة أو غير متاحة. يرجى اختيار محفظة من القائمة.',
            'payment_proof.required' => 'صورة إيصال الدفع مطلوبة',
            'payment_proof.image' => 'يجب أن يكون الملف صورة',
            'payment_proof.mimes' => 'يجب أن تكون الصورة بصيغة jpeg, png أو jpg',
            'payment_proof.max' => 'حجم الصورة يجب ألا تتجاوز 2 ميجابايت',
            'notes.max' => 'الملاحظات يجب ألا تتجاوز 1000 حرف',
        ]);

        // التحقق من وجود طلب قيد الانتظار
        $existingOrder = Order::where('user_id', Auth::id())
            ->where('academic_year_id', $learningPath->id)
            ->where('status', Order::STATUS_PENDING)
            ->first();

        if ($existingOrder) {
            return redirect()->route('public.learning-path.show', $slug)
                ->with('info', 'لديك طلب قيد الانتظار لهذا المسار. يرجى انتظار المراجعة.');
        }

        DB::beginTransaction();
        try {
            // حساب السعر النهائي
            $originalAmount = $learningPath->price ?? 0;
            $finalAmount = $originalAmount;
            $discountAmount = 0;

            // رفع صورة الإيصال
            if (!$request->hasFile('payment_proof')) {
                throw new \Exception('صورة الإيصال مطلوبة');
            }

            $paymentProofPath = $request->file('payment_proof')->store('payment-proofs', 'public');

            // إنشاء الطلب
            $order = Order::create([
                'user_id' => Auth::id(),
                'academic_year_id' => $learningPath->id,
                'original_amount' => $originalAmount,
                'discount_amount' => $discountAmount,
                'amount' => $finalAmount,
                'payment_method' => $request->payment_method === 'wallet' ? 'bank_transfer' : $request->payment_method,
                'payment_proof' => $paymentProofPath,
                'wallet_id' => in_array($request->payment_method, ['wallet', 'bank_transfer']) ? ($request->wallet_id ?? null) : null,
                'notes' => $request->notes ?? '',
                'status' => Order::STATUS_PENDING,
            ]);

            DB::commit();

            \Log::info('Order created successfully', [
                'order_id' => $order->id,
                'user_id' => Auth::id(),
                'learning_path_id' => $learningPath->id,
            ]);

            return redirect()->route('public.learning-path.show', $slug)
                ->with('success', 'تم إرسال طلبك بنجاح! سيتم مراجعته وتفعيل المسار تلقائياً بعد الموافقة.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            \Log::error('Validation error in completeLearningPath', [
                'errors' => $e->errors(),
                'user_id' => Auth::id(),
            ]);
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error in completeLearningPath: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'learning_path_id' => $learningPath->id ?? null,
                'trace' => $e->getTraceAsString(),
            ]);
            return back()->with('error', 'حدث خطأ أثناء إتمام الطلب: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * تسجيل مجاني للمسارات المجانية
     */
    public function enrollFreeLearningPath($slug)
    {
        // التحقق من تسجيل الدخول
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'يجب تسجيل الدخول أولاً');
        }

        // البحث عن AcademicYear بالاسم (slug)
        $learningPath = AcademicYear::active()
            ->visibleOnCurrentHost()
            ->get()
            ->first(function($year) use ($slug) {
                return Str::slug($year->name) === $slug;
            });
        
        if (!$learningPath) {
            abort(404, 'المسار التعليمي غير موجود');
        }

        // التحقق من أن المسار مجاني
        if (($learningPath->price ?? 0) > 0) {
            return redirect()->route('public.learning-path.show', $slug)
                ->with('error', 'هذا المسار ليس مجانياً');
        }

        // التحقق من التسجيل السابق
        $existingEnrollment = LearningPathEnrollment::where('user_id', Auth::id())
            ->where('academic_year_id', $learningPath->id)
            ->first();

        if ($existingEnrollment && $existingEnrollment->status === 'active') {
            return redirect()->route('public.learning-path.show', $slug)
                ->with('info', 'أنت مسجل بالفعل في هذا المسار التعليمي');
        }

        DB::beginTransaction();
        try {
            $enrollment = null;
            // إذا كان هناك تسجيل غير نشط، تفعيله
            if ($existingEnrollment) {
                $existingEnrollment->update([
                    'status' => 'active',
                    'activated_at' => now(),
                    'activated_by' => Auth::id(),
                ]);
                $enrollment = $existingEnrollment;
            } else {
                // إنشاء تسجيل جديد
                $enrollment = LearningPathEnrollment::create([
                    'user_id' => Auth::id(),
                    'academic_year_id' => $learningPath->id,
                    'enrolled_at' => now(),
                    'activated_at' => now(),
                    'activated_by' => Auth::id(),
                    'status' => 'active',
                    'progress' => 0,
                ]);
            }

            // تفعيل جميع الكورسات في المسار للطالب
            $this->enrollInPathCourses($enrollment);

            DB::commit();

            return redirect()->route('public.learning-path.show', $slug)
                ->with('success', 'تم تسجيلك في المسار التعليمي بنجاح! يمكنك الآن البدء بالتعلم.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('public.learning-path.show', $slug)
                ->with('error', 'حدث خطأ أثناء التسجيل. يرجى المحاولة مرة أخرى.');
        }
    }
}

