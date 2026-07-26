@extends('layouts.public')

@section('title', 'استبيان عملاء Mindlytics — واحصل على خصم ' . $discountPercentage . '%')

@push('styles')
@include('careers._styles')
<style>
    .survey-page {
        background: linear-gradient(180deg, #ffffff 0%, #f0f9ff 35%, #ffffff 100%);
    }

    .survey-shell {
        max-width: 46rem;
        margin-inline: auto;
    }

    .survey-panel {
        background: #fff;
        border: 1.5px solid rgba(226, 232, 240, 0.9);
        border-radius: 1.25rem;
        box-shadow: 0 8px 28px rgba(15, 23, 42, 0.06);
        overflow: hidden;
    }

    .survey-panel-head {
        display: flex;
        align-items: flex-start;
        gap: 0.875rem;
        padding: 1.25rem 1.35rem;
        border-bottom: 1px solid #e2e8f0;
        background: linear-gradient(90deg, #f8fafc 0%, #ffffff 100%);
    }

    .survey-panel-body {
        padding: 1.35rem;
    }

    @media (min-width: 640px) {
        .survey-panel-head,
        .survey-panel-body {
            padding-inline: 1.75rem;
        }
        .survey-panel-body {
            padding-block: 1.75rem;
        }
    }

    .survey-step {
        width: 2.5rem;
        height: 2.5rem;
        border-radius: 0.75rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 0.95rem;
        color: #fff;
        background: linear-gradient(135deg, #0ea5e9, #2563eb);
        flex-shrink: 0;
        box-shadow: 0 8px 18px rgba(37, 99, 235, 0.22);
    }

    .survey-input,
    .survey-select,
    .survey-textarea {
        width: 100%;
        border-radius: 0.875rem;
        border: 2px solid #e2e8f0;
        background: #fff;
        padding: 0.8rem 1rem;
        font-size: 0.9375rem;
        color: #0f172a;
        transition: border-color 0.2s, box-shadow 0.2s;
    }

    .survey-input:focus,
    .survey-select:focus,
    .survey-textarea:focus {
        outline: none;
        border-color: #0ea5e9;
        box-shadow: 0 0 0 4px rgba(14, 165, 233, 0.12);
    }

    .survey-textarea {
        min-height: 110px;
        resize: vertical;
        line-height: 1.7;
    }

    .survey-input:disabled,
    .survey-select:disabled {
        background: #f8fafc;
        color: #64748b;
        cursor: not-allowed;
    }

    .survey-label {
        display: block;
        font-size: 0.8125rem;
        font-weight: 700;
        color: #334155;
        margin-bottom: 0.4rem;
    }

    .survey-label .req { color: #e11d48; }

    .survey-error {
        font-size: 0.75rem;
        color: #e11d48;
        font-weight: 600;
        margin-top: 0.35rem;
    }

    .survey-benefits {
        display: grid;
        grid-template-columns: 1fr;
        gap: 0.85rem;
    }

    @media (min-width: 640px) {
        .survey-benefits {
            grid-template-columns: repeat(3, 1fr);
            gap: 1rem;
        }
    }

    .survey-benefit {
        display: flex;
        align-items: center;
        gap: 0.85rem;
        padding: 1.1rem 1.15rem;
        background: #fff;
        border: 1.5px solid #e2e8f0;
        border-radius: 1.15rem;
        box-shadow: 0 6px 18px rgba(15, 23, 42, 0.04);
        min-height: 5.25rem;
    }

    .survey-benefit-icon {
        width: 2.5rem;
        height: 2.5rem;
        border-radius: 0.85rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        font-size: 0.95rem;
    }

    .survey-reward {
        background: linear-gradient(135deg, #ecfdf5 0%, #f0f9ff 100%);
        border: 2px solid #6ee7b7;
        border-radius: 1.25rem;
        padding: 2rem 1.5rem;
        text-align: center;
    }

    .survey-coupon {
        font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
        letter-spacing: 0.14em;
        font-weight: 800;
        font-size: 1.25rem;
        color: #047857;
        background: #fff;
        border: 2px dashed #34d399;
        border-radius: 0.875rem;
        padding: 0.85rem 1.1rem;
        display: inline-block;
        direction: ltr;
    }

    .survey-alert {
        border-radius: 0.875rem;
        padding: 0.85rem 1rem;
        font-size: 0.875rem;
        font-weight: 600;
        line-height: 1.6;
    }

    .survey-alert-ok {
        background: #ecfdf5;
        border: 1px solid #a7f3d0;
        color: #065f46;
    }

    .survey-alert-warn {
        background: #fffbeb;
        border: 1px solid #fde68a;
        color: #92400e;
    }

    .survey-alert-err {
        background: #fff1f2;
        border: 1px solid #fecdd3;
        color: #9f1239;
    }

    .survey-alert-info {
        background: #f0f9ff;
        border: 1px solid #bae6fd;
        color: #075985;
    }

    .survey-locked {
        border: 1.5px dashed #cbd5e1;
        border-radius: 1.25rem;
        background: #f8fafc;
        padding: 2rem 1.25rem;
        text-align: center;
        color: #64748b;
    }
</style>
@endpush

@section('content')
@php
    $reward = session('survey_reward');
    $prefillJson = $prefill ? json_encode($prefill, JSON_UNESCAPED_UNICODE) : 'null';
    $hasErrors = $errors->any();
@endphp

@include('careers._hero', [
    'badge' => 'خصم ' . $discountPercentage . '% لعملاء الأكاديمية',
    'title' => 'شاركنا رأيك… وخُد خصم ' . $discountPercentage . '%',
    'subtitle' => 'دقيقتين من وقتك تساعدنا نطوّر المحتوى والخدمة، وفي المقابل ينزل في محفظتك كوبون خصم ' . $discountPercentage . '% على أي كورس في الأكاديمية.',
])

<section class="survey-page pb-16 md:pb-24 -mt-2">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="survey-shell space-y-5 sm:space-y-6">

            @if($reward)
                <div class="survey-reward fade-in-up">
                    <div class="w-14 h-14 rounded-2xl bg-emerald-500 text-white text-2xl flex items-center justify-center mx-auto mb-4 shadow-lg shadow-emerald-500/25">
                        <i class="fas fa-gift"></i>
                    </div>
                    <h2 class="text-2xl sm:text-3xl font-extrabold text-emerald-900 mb-3">
                        شكراً {{ $reward['name'] ?? '' }} — خصمك {{ $reward['percentage'] ?? $discountPercentage }}% نزل بالفعل!
                    </h2>
                    <p class="text-emerald-800/90 leading-relaxed mb-6 max-w-xl mx-auto text-sm sm:text-base">
                        الخصم متاح على أي كورس، وهيتطبق تلقائياً في صفحة الدفع. تلاقيه كمان في محفظتك داخل حسابك.
                    </p>

                    @if(!empty($reward['code']))
                        <div x-data="{ copied: false }" class="space-y-3">
                            <p class="text-sm font-bold text-emerald-900">كود الخصم الخاص بك</p>
                            <div class="flex flex-wrap items-center justify-center gap-3">
                                <span class="survey-coupon" x-ref="code">{{ $reward['code'] }}</span>
                                <button type="button"
                                        @click="navigator.clipboard.writeText($refs.code.innerText.trim()); copied = true; setTimeout(() => copied = false, 2000)"
                                        class="inline-flex items-center gap-2 px-5 py-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold transition-colors">
                                    <i class="fas" :class="copied ? 'fa-check' : 'fa-copy'"></i>
                                    <span x-text="copied ? 'تم النسخ' : 'نسخ الكود'"></span>
                                </button>
                            </div>
                            @if(!empty($reward['expires_at']))
                                <p class="text-xs text-emerald-700 font-semibold">صالح حتى {{ $reward['expires_at'] }} — استخدام واحد.</p>
                            @endif
                        </div>
                    @endif

                    <div class="mt-7 flex flex-wrap items-center justify-center gap-3">
                        <a href="{{ route('public.courses') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold transition-colors">
                            <i class="fas fa-graduation-cap"></i>
                            تصفح الكورسات واستخدم الخصم
                        </a>
                        @auth
                            <a href="{{ route('student.wallet.index') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-white border-2 border-emerald-300 text-emerald-800 hover:bg-emerald-50 font-bold transition-colors">
                                <i class="fas fa-wallet"></i>
                                محفظتي
                            </a>
                        @endauth
                    </div>
                </div>
            @else
                @if(session('info'))
                    <div class="survey-alert survey-alert-info">
                        <i class="fas fa-circle-info me-1"></i>{{ session('info') }}
                    </div>
                @endif

                {{-- شريط المزايا: كرت واحد مقسوم مش 3 كروت منفصلة --}}
                <div class="survey-benefits">
                    <div class="survey-benefit">
                        <span class="survey-benefit-icon bg-sky-100 text-sky-700"><i class="fas fa-percent"></i></span>
                        <div class="min-w-0">
                            <p class="font-bold text-slate-900 text-sm">خصم {{ $discountPercentage }}%</p>
                            <p class="text-xs text-slate-500 mt-0.5 leading-relaxed">على أي كورس في الأكاديمية</p>
                        </div>
                    </div>
                    <div class="survey-benefit">
                        <span class="survey-benefit-icon bg-amber-100 text-amber-700"><i class="fas fa-bolt"></i></span>
                        <div class="min-w-0">
                            <p class="font-bold text-slate-900 text-sm">تطبيق تلقائي</p>
                            <p class="text-xs text-slate-500 mt-0.5 leading-relaxed">ينزل في المحفظة ويُخصم عند الدفع</p>
                        </div>
                    </div>
                    <div class="survey-benefit">
                        <span class="survey-benefit-icon bg-emerald-100 text-emerald-700"><i class="fas fa-calendar-check"></i></span>
                        <div class="min-w-0">
                            <p class="font-bold text-slate-900 text-sm">صالح {{ $validDays }} يوم</p>
                            <p class="text-xs text-slate-500 mt-0.5 leading-relaxed">من لحظة إرسال الاستبيان</p>
                        </div>
                    </div>
                </div>

                <div
                    x-data="customerSurvey({{ $prefillJson }}, '{{ route('public.customer-survey.lookup') }}', '{{ csrf_token() }}', @js($hasErrors), @js(old('email')))"
                    x-init="init()"
                    class="space-y-5"
                >
                    @if($hasErrors)
                        <div class="survey-alert survey-alert-err">
                            <p class="font-bold mb-1"><i class="fas fa-triangle-exclamation me-1"></i>راجع البيانات التالية:</p>
                            <ul class="space-y-1 list-disc ps-5 font-medium">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- الخطوة 1 --}}
                    <div class="survey-panel">
                        <div class="survey-panel-head">
                            <span class="survey-step">1</span>
                            <div class="min-w-0">
                                <h2 class="text-base sm:text-lg font-extrabold text-slate-900">تأكيد أنك من عملاء الأكاديمية</h2>
                                <p class="text-sm text-slate-500 mt-1 leading-relaxed">اكتب نفس البريد المسجل وقت شراء الكورس، وهنستحضر بياناتك تلقائياً.</p>
                            </div>
                        </div>

                        <div class="survey-panel-body space-y-4">
                            <div class="grid grid-cols-1 sm:grid-cols-[minmax(0,1fr)_auto] gap-3 items-end">
                                <div class="min-w-0">
                                    <label class="survey-label" for="survey-email">البريد الإلكتروني المسجل <span class="req">*</span></label>
                                    <input id="survey-email" type="email" class="survey-input" dir="ltr"
                                           placeholder="you@example.com"
                                           x-model="email"
                                           @keydown.enter.prevent="verify()"
                                           :disabled="locked">
                                </div>
                                <button type="button" @click="verify()" :disabled="checking || locked"
                                        class="inline-flex items-center justify-center gap-2 w-full sm:w-auto px-5 py-3 rounded-xl bg-gradient-to-r from-sky-500 to-blue-600 hover:from-sky-600 hover:to-blue-700 text-white font-bold shadow-md shadow-sky-500/20 transition-all disabled:opacity-60 disabled:cursor-not-allowed whitespace-nowrap">
                                    <i class="fas" :class="checking ? 'fa-spinner fa-spin' : 'fa-magnifying-glass'"></i>
                                    <span x-text="checking ? 'جاري التحقق…' : 'تحقق من البريد'"></span>
                                </button>
                            </div>

                            <div x-show="message" x-cloak
                                 class="survey-alert"
                                 :class="verified ? 'survey-alert-ok' : 'survey-alert-err'"
                                 x-text="message"></div>

                            <div x-show="verified && customer && customer.already_submitted" x-cloak
                                 class="survey-alert survey-alert-warn">
                                <span>سجّلنا رأيك قبل كده، وخصمك متاح في محفظتك</span>
                                <span x-show="customer && customer.coupon_code">
                                    — الكود:
                                    <span class="font-mono tracking-wider font-extrabold" dir="ltr" x-text="customer.coupon_code"></span>
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- الخطوة 2: تظهر بعد التحقق فقط --}}
                    <template x-if="verified && customer && !customer.already_submitted">
                        <form method="POST" action="{{ route('public.customer-survey.store') }}" class="survey-panel">
                            @csrf
                            <input type="hidden" name="email" :value="email">

                            <div class="survey-panel-head">
                                <span class="survey-step">2</span>
                                <div class="min-w-0">
                                    <h2 class="text-base sm:text-lg font-extrabold text-slate-900">بياناتك ورأيك</h2>
                                    <p class="text-sm text-slate-500 mt-1 leading-relaxed">كل رأي بنقرأه فعلاً وبنبني عليه خطة المحتوى القادمة.</p>
                                </div>
                            </div>

                            <div class="survey-panel-body space-y-5">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="survey-label" for="survey-name">الاسم <span class="req">*</span></label>
                                        <input id="survey-name" name="name" type="text" class="survey-input" maxlength="150"
                                               value="{{ old('name') }}" x-ref="name" required>
                                        @error('name') <p class="survey-error">{{ $message }}</p> @enderror
                                    </div>

                                    <div>
                                        <label class="survey-label" for="survey-phone">رقم الهاتف</label>
                                        <input id="survey-phone" name="phone" type="text" class="survey-input" dir="ltr" maxlength="30"
                                               value="{{ old('phone') }}" x-ref="phone">
                                        @error('phone') <p class="survey-error">{{ $message }}</p> @enderror
                                    </div>

                                    <div class="md:col-span-2">
                                        <label class="survey-label" for="survey-course">الكورس الذي درسته معنا <span class="req">*</span></label>
                                        <select id="survey-course" name="advanced_course_id" class="survey-select" required>
                                            <option value="">اختر الكورس</option>
                                            <template x-for="course in customer.courses" :key="course.id">
                                                <option :value="course.id" :selected="String(course.id) === selectedCourseId" x-text="course.title"></option>
                                            </template>
                                        </select>
                                        @error('advanced_course_id') <p class="survey-error">{{ $message }}</p> @enderror
                                    </div>

                                    <div>
                                        <label class="survey-label" for="survey-governorate">المحافظة <span class="req">*</span></label>
                                        <select id="survey-governorate" name="governorate" class="survey-select" required>
                                            <option value="">اختر المحافظة</option>
                                            @foreach($governorates as $key => $label)
                                                <option value="{{ $key }}" @selected(old('governorate') === $key)>{{ $label }}</option>
                                            @endforeach
                                        </select>
                                        @error('governorate') <p class="survey-error">{{ $message }}</p> @enderror
                                    </div>

                                    <div>
                                        <label class="survey-label" for="survey-job">الوظيفة / المجال <span class="req">*</span></label>
                                        <select id="survey-job" name="job" class="survey-select" x-model="job" required>
                                            <option value="">اختر الوظيفة</option>
                                            @foreach($jobs as $key => $label)
                                                <option value="{{ $key }}">{{ $label }}</option>
                                            @endforeach
                                        </select>
                                        @error('job') <p class="survey-error">{{ $message }}</p> @enderror

                                        <div x-show="job === 'other'" x-cloak class="mt-3">
                                            <input name="job_other" type="text" class="survey-input" maxlength="150"
                                                   placeholder="اكتب وظيفتك" value="{{ old('job_other') }}">
                                            @error('job_other') <p class="survey-error">{{ $message }}</p> @enderror
                                        </div>
                                    </div>

                                    <div class="md:col-span-2">
                                        <label class="survey-label" for="survey-heard">عرفتنا منين؟ <span class="req">*</span></label>
                                        <select id="survey-heard" name="heard_from" class="survey-select" x-model="heardFrom" required>
                                            <option value="">اختر القناة</option>
                                            @foreach($heardFromOptions as $key => $label)
                                                <option value="{{ $key }}">{{ $label }}</option>
                                            @endforeach
                                        </select>
                                        @error('heard_from') <p class="survey-error">{{ $message }}</p> @enderror

                                        <div x-show="heardFrom === 'other'" x-cloak class="mt-3">
                                            <input name="heard_from_other" type="text" class="survey-input" maxlength="150"
                                                   placeholder="اكتب كيف عرفتنا" value="{{ old('heard_from_other') }}">
                                            @error('heard_from_other') <p class="survey-error">{{ $message }}</p> @enderror
                                        </div>
                                    </div>

                                    <div class="md:col-span-2">
                                        <label class="survey-label" for="survey-interested">مهتم بإيه الفترة الجاية؟ <span class="req">*</span></label>
                                        <textarea id="survey-interested" name="interested_in" class="survey-textarea" maxlength="2000"
                                                  placeholder="مثال: تحليل بيانات متقدم، Power BI، تعلم آلة…" required>{{ old('interested_in') }}</textarea>
                                        @error('interested_in') <p class="survey-error">{{ $message }}</p> @enderror
                                    </div>

                                    <div class="md:col-span-2">
                                        <label class="survey-label" for="survey-opinion">رأيك في الكورس والأكاديمية <span class="req">*</span></label>
                                        <textarea id="survey-opinion" name="opinion" class="survey-textarea" maxlength="3000"
                                                  placeholder="إيه اللي عجبك؟ وإيه اللي كان ناقص؟" required>{{ old('opinion') }}</textarea>
                                        @error('opinion') <p class="survey-error">{{ $message }}</p> @enderror
                                    </div>

                                    <div class="md:col-span-2">
                                        <label class="survey-label" for="survey-needed">محتاج كورسات تانية في إيه؟</label>
                                        <textarea id="survey-needed" name="needed_courses" class="survey-textarea" maxlength="2000"
                                                  placeholder="اكتب المواضيع اللي نفسك نعملها">{{ old('needed_courses') }}</textarea>
                                        @error('needed_courses') <p class="survey-error">{{ $message }}</p> @enderror
                                    </div>

                                    <div class="md:col-span-2">
                                        <label class="survey-label" for="survey-recommendations">توصيات للتحسين</label>
                                        <textarea id="survey-recommendations" name="recommendations" class="survey-textarea" maxlength="2000"
                                                  placeholder="أي اقتراح يخلي التجربة أفضل">{{ old('recommendations') }}</textarea>
                                        @error('recommendations') <p class="survey-error">{{ $message }}</p> @enderror
                                    </div>
                                </div>

                                <div class="pt-5 border-t border-slate-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                                    <p class="text-xs text-slate-500 leading-relaxed sm:max-w-sm">
                                        بإرسال الاستبيان ينزل كوبون خصم {{ $discountPercentage }}% في محفظتك مباشرة، صالح {{ $validDays }} يوم.
                                    </p>
                                    <button type="submit"
                                            class="inline-flex items-center justify-center gap-2 px-7 py-3.5 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white font-bold shadow-lg shadow-emerald-500/20 transition-all w-full sm:w-auto">
                                        <i class="fas fa-paper-plane"></i>
                                        إرسال واستلام الخصم
                                    </button>
                                </div>
                            </div>
                        </form>
                    </template>

                    <div x-show="!verified || (customer && customer.already_submitted)" x-cloak class="survey-locked">
                        <div class="w-12 h-12 rounded-xl bg-slate-200 text-slate-500 flex items-center justify-center mx-auto mb-3">
                            <i class="fas fa-lock"></i>
                        </div>
                        <p class="font-bold text-slate-700 mb-1" x-text="(customer && customer.already_submitted) ? 'تم استلام رأيك مسبقاً' : 'خطوة 2 هتفتح بعد التحقق من البريد'"></p>
                        <p class="text-sm text-slate-500 leading-relaxed max-w-md mx-auto"
                           x-text="(customer && customer.already_submitted) ? 'تقدر تستخدم خصمك من المحفظة أو صفحة الدفع مباشرة.' : 'بعد ما نتأكد إنك عميل عندنا، هنبعت بياناتك ونفتح فورم الرأي.'"></p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
    function customerSurvey(prefill, lookupUrl, csrfToken, hasErrors, oldEmail) {
        return {
            email: '',
            checking: false,
            verified: false,
            locked: false,
            message: '',
            customer: null,
            job: @js(old('job', '')),
            heardFrom: @js(old('heard_from', '')),
            selectedCourseId: @js((string) old('advanced_course_id', '')),

            init() {
                if (prefill) {
                    this.email = prefill.email || '';
                    this.locked = true;
                    let msg = 'هذا الاستبيان مخصص لعملاء اشتروا كورساً من الأكاديمية.';
                    if (prefill.courses && prefill.courses.length) {
                        msg = prefill.already_submitted
                            ? 'تم التعرف عليك، وخصمك متاح في محفظتك.'
                            : 'تم التعرف عليك ✔ أكمل باقي البيانات لتحصل على الخصم.';
                    }
                    this.applyCustomer(prefill, msg);
                } else if (oldEmail) {
                    this.email = oldEmail;
                    if (hasErrors) {
                        this.verify();
                    }
                }
            },

            applyCustomer(customer, message) {
                this.customer = customer;
                this.verified = Array.isArray(customer.courses) && customer.courses.length > 0;
                this.message = '';

                if (!this.verified) {
                    this.message = message || 'هذا الاستبيان مخصص لعملاء اشتروا كورساً من الأكاديمية.';
                    return;
                }

                if (!customer.already_submitted) {
                    this.message = message || '';
                }

                // x-if بيطلع الفورم بعد التحقق؛ نستنى رندر الخطوة التانية قبل تعبئة الحقول
                this.$nextTick(() => {
                    setTimeout(() => this.fillVerifiedFields(customer), 30);
                });
            },

            fillVerifiedFields(customer) {
                if (this.$refs.name && !this.$refs.name.value) {
                    this.$refs.name.value = customer.name || '';
                }
                if (this.$refs.phone && !this.$refs.phone.value) {
                    this.$refs.phone.value = customer.phone || '';
                }
                if (customer.courses.length === 1 && !this.selectedCourseId) {
                    this.selectedCourseId = String(customer.courses[0].id);
                }

                const select = document.getElementById('survey-course');
                if (select && this.selectedCourseId) {
                    select.value = this.selectedCourseId;
                }
            },

            async verify() {
                const email = (this.email || '').trim();

                if (!email) {
                    this.verified = false;
                    this.customer = null;
                    this.message = 'من فضلك اكتب البريد الإلكتروني أولاً.';
                    return;
                }

                this.checking = true;
                this.message = '';

                try {
                    const response = await fetch(lookupUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                        },
                        body: JSON.stringify({ email }),
                    });

                    const data = await response.json().catch(() => ({}));

                    if (!response.ok) {
                        this.verified = false;
                        this.customer = null;
                        this.message = data.message
                            || (data.errors && data.errors.email && data.errors.email[0])
                            || 'تعذر التحقق الآن. حاول مرة أخرى بعد لحظات.';
                        return;
                    }

                    if (data.eligible && data.customer) {
                        this.applyCustomer(data.customer, data.message);
                    } else {
                        this.verified = false;
                        this.customer = null;
                        this.message = data.message || 'لم نتمكن من التحقق من هذا البريد.';
                    }
                } catch (error) {
                    this.verified = false;
                    this.customer = null;
                    this.message = 'تعذر الاتصال بالسيرفر. تحقق من الإنترنت وحاول مرة أخرى.';
                } finally {
                    this.checking = false;
                }
            },
        };
    }
</script>
@endpush
