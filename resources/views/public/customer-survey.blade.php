@extends('layouts.public')

@section('title', 'استبيان عملاء Mindlytics — واحصل على خصم ' . $discountPercentage . '%')

@push('styles')
@include('careers._styles')
<style>
    .survey-card {
        background: #fff;
        border: 1.5px solid #e2e8f0;
        border-radius: 1.5rem;
        box-shadow: 0 12px 40px rgba(15, 23, 42, 0.06);
    }
    .survey-input,
    .survey-select,
    .survey-textarea {
        width: 100%;
        border-radius: 0.875rem;
        border: 2px solid #e2e8f0;
        background: #fff;
        padding: 0.75rem 1rem;
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
    .survey-textarea { min-height: 120px; resize: vertical; line-height: 1.7; }
    .survey-input:disabled,
    .survey-select:disabled,
    .survey-textarea:disabled { background: #f8fafc; color: #94a3b8; cursor: not-allowed; }
    .survey-label {
        display: block;
        font-size: 0.875rem;
        font-weight: 700;
        color: #334155;
        margin-bottom: 0.5rem;
    }
    .survey-label .req { color: #e11d48; }
    .survey-hint { font-size: 0.75rem; color: #64748b; margin-top: 0.375rem; }
    .survey-error { font-size: 0.75rem; color: #e11d48; font-weight: 600; margin-top: 0.375rem; }
    .step-badge {
        width: 2.25rem;
        height: 2.25rem;
        border-radius: 9999px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        background: linear-gradient(135deg, #0ea5e9, #2563eb);
        color: #fff;
        flex-shrink: 0;
        box-shadow: 0 6px 18px rgba(37, 99, 235, 0.28);
    }
    .reward-card {
        background: linear-gradient(135deg, #ecfdf5 0%, #f0f9ff 100%);
        border: 2px solid #6ee7b7;
    }
    .coupon-code {
        font-family: 'Courier New', monospace;
        letter-spacing: 0.18em;
        font-weight: 800;
        font-size: 1.5rem;
        color: #047857;
        background: #fff;
        border: 2px dashed #34d399;
        border-radius: 0.875rem;
        padding: 0.875rem 1.25rem;
        display: inline-block;
    }
    .perk-tile {
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
        padding: 1rem 1.125rem;
        border-radius: 1rem;
        border: 1.5px solid rgba(226, 232, 240, 0.95);
        background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
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

<section class="pb-20 -mt-4">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 max-w-4xl space-y-6">

        @if($reward)
            <div class="reward-card rounded-3xl p-7 sm:p-10 text-center fade-in-up">
                <div class="w-16 h-16 rounded-2xl bg-emerald-500 text-white text-3xl flex items-center justify-center mx-auto mb-5 shadow-lg shadow-emerald-500/30">
                    <i class="fas fa-gift"></i>
                </div>
                <h2 class="text-2xl sm:text-3xl font-extrabold text-emerald-900 mb-3">
                    شكراً {{ $reward['name'] ?? '' }} — خصمك {{ $reward['percentage'] ?? $discountPercentage }}% نزل بالفعل!
                </h2>
                <p class="text-emerald-800/90 leading-relaxed mb-6 max-w-2xl mx-auto">
                    الخصم متاح على أي كورس في الأكاديمية، وهيتطبق تلقائياً في صفحة الدفع. تلاقيه كذلك في
                    <span class="font-bold">محفظتك</span> داخل حسابك.
                </p>

                @if(!empty($reward['code']))
                    <div x-data="{ copied: false }" class="space-y-3">
                        <p class="text-sm font-bold text-emerald-900">كود الخصم الخاص بك</p>
                        <div class="flex flex-wrap items-center justify-center gap-3">
                            <span class="coupon-code" x-ref="code">{{ $reward['code'] }}</span>
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

                <div class="mt-8 flex flex-wrap items-center justify-center gap-3">
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
                <div class="rounded-2xl border border-sky-200 bg-sky-50 text-sky-800 px-5 py-4 text-sm font-semibold">
                    <i class="fas fa-circle-info me-2"></i>{{ session('info') }}
                </div>
            @endif

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div class="perk-tile">
                    <i class="fas fa-percent text-blue-600 mt-1"></i>
                    <div>
                        <p class="font-bold text-slate-900 text-sm">خصم {{ $discountPercentage }}%</p>
                        <p class="text-xs text-slate-500 mt-0.5">على أي كورس في الأكاديمية</p>
                    </div>
                </div>
                <div class="perk-tile">
                    <i class="fas fa-bolt text-amber-500 mt-1"></i>
                    <div>
                        <p class="font-bold text-slate-900 text-sm">تطبيق تلقائي</p>
                        <p class="text-xs text-slate-500 mt-0.5">ينزل في محفظتك ويُخصم عند الدفع</p>
                    </div>
                </div>
                <div class="perk-tile">
                    <i class="fas fa-calendar-check text-emerald-600 mt-1"></i>
                    <div>
                        <p class="font-bold text-slate-900 text-sm">صالح {{ $validDays }} يوم</p>
                        <p class="text-xs text-slate-500 mt-0.5">من لحظة إرسال الاستبيان</p>
                    </div>
                </div>
            </div>

            <div
                x-data="customerSurvey({{ $prefillJson }}, '{{ route('public.customer-survey.lookup') }}', '{{ csrf_token() }}', @js($hasErrors), @js(old('email')))"
                x-init="init()"
                class="space-y-6"
            >
                @if($hasErrors)
                    <div class="rounded-2xl border border-rose-200 bg-rose-50 px-5 py-4">
                        <p class="font-bold text-rose-800 text-sm mb-2"><i class="fas fa-triangle-exclamation me-2"></i>راجع البيانات التالية:</p>
                        <ul class="space-y-1 text-sm text-rose-700 list-disc ps-5">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- الخطوة 1: التحقق من البريد --}}
                <div class="survey-card p-6 sm:p-8">
                    <div class="flex items-start gap-4 mb-6">
                        <span class="step-badge">1</span>
                        <div>
                            <h2 class="text-lg sm:text-xl font-extrabold text-slate-900">تأكيد أنك من عملاء الأكاديمية</h2>
                            <p class="text-sm text-slate-500 mt-1">اكتب نفس البريد الإلكتروني المسجل عندنا وقت شراء الكورس، وهنستحضر بياناتك تلقائياً.</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-[1fr_auto] gap-3 items-end">
                        <div>
                            <label class="survey-label" for="survey-email">البريد الإلكتروني المسجل <span class="req">*</span></label>
                            <input id="survey-email" type="email" class="survey-input" dir="ltr"
                                   placeholder="you@example.com"
                                   x-model="email"
                                   @keydown.enter.prevent="verify()"
                                   :disabled="locked">
                        </div>
                        <button type="button" @click="verify()" :disabled="checking || locked"
                                class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl bg-gradient-to-r from-sky-500 to-blue-600 hover:from-sky-600 hover:to-blue-700 text-white font-bold shadow-lg shadow-sky-500/25 transition-all disabled:opacity-60 disabled:cursor-not-allowed">
                            <i class="fas" :class="checking ? 'fa-spinner fa-spin' : 'fa-magnifying-glass'"></i>
                            <span x-text="checking ? 'جاري التحقق…' : 'تحقق من البريد'"></span>
                        </button>
                    </div>

                    <template x-if="message">
                        <div class="mt-4 rounded-xl px-4 py-3 text-sm font-semibold"
                             :class="verified ? 'bg-emerald-50 border border-emerald-200 text-emerald-800' : 'bg-rose-50 border border-rose-200 text-rose-800'"
                             x-text="message"></div>
                    </template>

                    <template x-if="verified && customer && customer.already_submitted">
                        <div class="mt-4 rounded-xl px-4 py-3 text-sm font-semibold bg-amber-50 border border-amber-200 text-amber-800">
                            سجّلنا رأيك قبل كذلك، وخصمك متاح في محفظتك
                            <template x-if="customer.coupon_code">
                                <span> — الكود: <span class="font-mono tracking-widest" x-text="customer.coupon_code"></span></span>
                            </template>
                        </div>
                    </template>
                </div>

                {{-- الخطوة 2: الاستبيان --}}
                <form method="POST" action="{{ route('public.customer-survey.store') }}"
                      class="survey-card p-6 sm:p-8"
                      :class="(verified && customer && !customer.already_submitted) ? '' : 'opacity-60 pointer-events-none select-none'">
                    @csrf
                    <input type="hidden" name="email" :value="email">

                    <div class="flex items-start gap-4 mb-7">
                        <span class="step-badge">2</span>
                        <div>
                            <h2 class="text-lg sm:text-xl font-extrabold text-slate-900">بياناتك ورأيك</h2>
                            <p class="text-sm text-slate-500 mt-1">كل رأي بنقرأه فعلاً وبنبني عليه خطة المحتوى القادمة.</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
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
                                <template x-for="course in (customer ? customer.courses : [])" :key="course.id">
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
                                      placeholder="مثال: تحليل بيانات متقدم، Power BI، تعلم آلة، مشاريع عملية…" required>{{ old('interested_in') }}</textarea>
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

                    <div class="mt-8 pt-6 border-t border-slate-200 flex flex-wrap items-center justify-between gap-4">
                        <p class="text-xs text-slate-500 max-w-md leading-relaxed">
                            بإرسال الاستبيان ينزل كوبون خصم {{ $discountPercentage }}% في محفظتك مباشرة، صالح {{ $validDays }} يوم على أي كورس.
                        </p>
                        <button type="submit" :disabled="!(verified && customer && !customer.already_submitted)"
                                class="inline-flex items-center gap-2 px-7 py-3.5 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white font-bold shadow-lg shadow-emerald-500/25 transition-all disabled:opacity-60 disabled:cursor-not-allowed">
                            <i class="fas fa-paper-plane"></i>
                            إرسال واستلام الخصم
                        </button>
                    </div>
                </form>
            </div>
        @endif
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
                    this.applyCustomer(prefill, prefill.courses && prefill.courses.length
                        ? 'تم التعرف عليك ✔ أكمل باقي البيانات لتحصل على الخصم.'
                        : 'هذا الاستبيان مخصص لعملاء اشتروا كورساً من الأكاديمية.');
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
                this.message = message || '';

                if (!this.verified) {
                    return;
                }

                this.$nextTick(() => {
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
                });
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
