@extends('layouts.public')

@section('title', $internship->title . ' | تدريب Mindlytics')
@section('meta_description', \Illuminate\Support\Str::limit($internship->summary ?: strip_tags($internship->description ?? ''), 160))

@push('styles')
@include('careers._styles')
@endpush

@section('content')
@php
    $requirementLines = array_values(array_filter(
        array_map('trim', preg_split('/\r\n|\r|\n|•|·|-(?=\s)/', (string) ($internship->requirements ?? ''))),
        fn ($line) => $line !== '' && mb_strlen($line) > 1
    ));
    $benefitLines = array_values(array_filter(
        array_map('trim', preg_split('/\r\n|\r|\n|•|·|-(?=\s)/', (string) ($internship->benefits ?? ''))),
        fn ($line) => $line !== '' && mb_strlen($line) > 1
    ));
    $isOpen = $internship->isOpenForApply();
@endphp

@include('careers._hero', [
    'title' => $internship->title,
    'subtitle' => $internship->summary ?: ($internship->department ? 'تدريب في قسم ' . $internship->department : 'فرصة تدريب في Mindlytics'),
    'backUrl' => route('public.internships.index'),
    'backLabel' => 'كل فرص التدريب',
    'metaChips' => array_values(array_filter([
        $internship->is_featured ? ['label' => 'Featured', 'icon' => 'fas fa-star', 'tone' => 'violet'] : null,
        ['label' => $internship->typeLabel(), 'icon' => 'fas fa-laptop-house', 'tone' => 'blue'],
        $internship->department ? ['label' => $internship->department, 'icon' => 'fas fa-building', 'tone' => 'blue'] : null,
        $internship->location ? ['label' => $internship->location, 'icon' => 'fas fa-map-marker-alt', 'tone' => 'green'] : null,
        ! $isOpen ? ['label' => 'التقديم مغلق', 'icon' => 'fas fa-lock', 'tone' => 'violet'] : null,
    ])),
])

<section class="py-10 bg-white border-b border-slate-100">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 max-w-6xl">
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="stat-card p-5 flex items-center gap-4">
                <div class="w-11 h-11 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-map-marker-alt"></i>
                </div>
                <div>
                    <p class="text-xs font-bold text-slate-500 mb-0.5">الموقع</p>
                    <p class="text-sm font-extrabold text-slate-900">{{ $internship->location ?: '—' }}</p>
                </div>
            </div>
            <div class="stat-card p-5 flex items-center gap-4">
                <div class="w-11 h-11 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-clock"></i>
                </div>
                <div>
                    <p class="text-xs font-bold text-slate-500 mb-0.5">المدة</p>
                    <p class="text-sm font-extrabold text-slate-900">{{ $internship->duration ?: '—' }}</p>
                </div>
            </div>
            <div class="stat-card p-5 flex items-center gap-4">
                <div class="w-11 h-11 rounded-xl bg-violet-100 text-violet-600 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-users"></i>
                </div>
                <div>
                    <p class="text-xs font-bold text-slate-500 mb-0.5">المقاعد</p>
                    <p class="text-sm font-extrabold text-slate-900">{{ $internship->seats ?: 'مفتوحة' }}</p>
                </div>
            </div>
            <div class="stat-card p-5 flex items-center gap-4">
                <div class="w-11 h-11 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <div>
                    <p class="text-xs font-bold text-slate-500 mb-0.5">آخر موعد</p>
                    <p class="text-sm font-extrabold text-slate-900">{{ $internship->application_deadline?->format('Y-m-d') ?: '—' }}</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-14 md:py-20 bg-gradient-to-b from-white via-blue-50/25 to-white">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 max-w-6xl">
        @if(session('success'))
            <div class="mb-8 rounded-2xl border border-emerald-200 bg-emerald-50 text-emerald-800 px-5 py-4 flex items-start gap-3 shadow-sm">
                <i class="fas fa-check-circle text-2xl text-emerald-600 mt-0.5"></i>
                <div>
                    <p class="font-extrabold text-base mb-1">تم إرسال طلبك بنجاح</p>
                    <p class="text-sm">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        @if($errors->any())
            <div class="mb-8 rounded-2xl border border-rose-200 bg-rose-50 text-rose-800 px-5 py-4 text-sm">
                <p class="font-bold mb-2 flex items-center gap-2"><i class="fas fa-exclamation-circle"></i> يوجد أخطاء في النموذج:</p>
                <ul class="list-disc list-inside space-y-0.5 mr-1">
                    @foreach($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid lg:grid-cols-12 gap-8 xl:gap-10 items-start">
            <div class="lg:col-span-7 space-y-6">
                <div class="text-center lg:text-right mb-2">
                    <span class="careers-badge inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-bold mb-3">
                        <i class="fas fa-info-circle text-blue-600"></i>
                        عن الفرصة
                    </span>
                    <h2 class="section-title text-2xl font-extrabold text-blue-900">تفاصيل التدريب</h2>
                </div>

                @if($internship->description)
                    <article class="content-panel">
                        <div class="content-panel-head flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center">
                                <i class="fas fa-align-right"></i>
                            </div>
                            <h3 class="text-lg font-extrabold text-slate-900">الوصف</h3>
                        </div>
                        <div class="p-6 text-slate-700 text-sm leading-relaxed whitespace-pre-line">
                            {!! nl2br(e($internship->description)) !!}
                        </div>
                    </article>
                @endif

                @if($internship->requirements)
                    <article class="content-panel">
                        <div class="content-panel-head flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center">
                                <i class="fas fa-list-check"></i>
                            </div>
                            <h3 class="text-lg font-extrabold text-slate-900">المتطلبات</h3>
                        </div>
                        <div class="p-6">
                            @if(count($requirementLines) > 1)
                                <ul class="req-list space-y-1 text-sm">
                                    @foreach($requirementLines as $line)
                                        <li>{{ $line }}</li>
                                    @endforeach
                                </ul>
                            @else
                                <div class="text-slate-700 text-sm leading-relaxed whitespace-pre-line">
                                    {!! nl2br(e($internship->requirements)) !!}
                                </div>
                            @endif
                        </div>
                    </article>
                @endif

                @if($internship->benefits)
                    <article class="content-panel">
                        <div class="content-panel-head flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-sky-100 text-sky-600 flex items-center justify-center">
                                <i class="fas fa-gift"></i>
                            </div>
                            <h3 class="text-lg font-extrabold text-slate-900">ماذا ستكتسب؟</h3>
                        </div>
                        <div class="p-6">
                            @if(count($benefitLines) > 1)
                                <ul class="req-list space-y-1 text-sm">
                                    @foreach($benefitLines as $line)
                                        <li>{{ $line }}</li>
                                    @endforeach
                                </ul>
                            @else
                                <div class="text-slate-700 text-sm leading-relaxed whitespace-pre-line">
                                    {!! nl2br(e($internship->benefits)) !!}
                                </div>
                            @endif
                        </div>
                    </article>
                @endif
            </div>

            <aside class="lg:col-span-5 space-y-6 lg:sticky lg:top-28" id="apply">
                @if($isOpen)
                    <div class="content-panel">
                        <div class="content-panel-head">
                            <div class="flex items-center gap-3 mb-1">
                                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-sky-500 text-white flex items-center justify-center shadow-md">
                                    <i class="fas fa-paper-plane"></i>
                                </div>
                                <h3 class="text-lg font-extrabold text-slate-900">قدّم الآن</h3>
                            </div>
                            <p class="text-xs text-slate-500">
                                <i class="fas fa-shield-alt text-sky-500 ml-1"></i>
                                املأ النموذج وقدّم — رفع السيرة الذاتية اختياري وسنراجع طلبك ونتواصل معك.
                            </p>
                        </div>

                        <form method="POST" action="{{ route('public.internships.apply', $internship->slug) }}"
                              enctype="multipart/form-data" class="p-5 sm:p-6 space-y-6" x-data="{ cvName: '' }">
                            @csrf

                            <div>
                                <div class="form-section-title">
                                    <span class="form-section-num">1</span>
                                    البيانات الشخصية
                                </div>
                                <div class="space-y-4">
                                    <div>
                                        <label class="careers-label">الاسم الكامل <span class="text-rose-500">*</span></label>
                                        <input type="text" name="name" value="{{ old('name') }}" required class="careers-input" placeholder="مثال: أحمد محمد">
                                    </div>
                                    <div>
                                        <label class="careers-label">البريد الإلكتروني <span class="text-rose-500">*</span></label>
                                        <input type="email" name="email" value="{{ old('email') }}" required class="careers-input" dir="ltr" placeholder="name@email.com">
                                    </div>
                                    <div>
                                        <label class="careers-label">الهاتف</label>
                                        <input type="text" name="phone" value="{{ old('phone') }}" class="careers-input" dir="ltr" placeholder="01xxxxxxxxx">
                                    </div>
                                    <div class="grid sm:grid-cols-2 gap-4">
                                        <div>
                                            <label class="careers-label">الجامعة</label>
                                            <input type="text" name="university" value="{{ old('university') }}" class="careers-input">
                                        </div>
                                        <div>
                                            <label class="careers-label">التخصص</label>
                                            <input type="text" name="major" value="{{ old('major') }}" class="careers-input">
                                        </div>
                                    </div>
                                    <div>
                                        <label class="careers-label">السنة الدراسية</label>
                                        <input type="text" name="year_of_study" value="{{ old('year_of_study') }}" class="careers-input" placeholder="ثالثة / خريج">
                                    </div>
                                </div>
                            </div>

                            <div>
                                <div class="form-section-title">
                                    <span class="form-section-num">2</span>
                                    الروابط والملف
                                </div>
                                <div class="space-y-4">
                                    <div>
                                        <label class="careers-label">Portfolio URL</label>
                                        <input type="url" name="portfolio_url" value="{{ old('portfolio_url') }}" class="careers-input" dir="ltr">
                                    </div>
                                    <div>
                                        <label class="careers-label">GitHub</label>
                                        <input type="url" name="github_url" value="{{ old('github_url') }}" class="careers-input" dir="ltr">
                                    </div>
                                    <div>
                                        <label class="careers-label">LinkedIn</label>
                                        <input type="url" name="linkedin_url" value="{{ old('linkedin_url') }}" class="careers-input" dir="ltr">
                                    </div>
                                    <div>
                                        <label class="careers-label">لماذا أنت مناسب لهذه الفرصة؟</label>
                                        <textarea name="cover_letter" rows="4" class="careers-input">{{ old('cover_letter') }}</textarea>
                                    </div>
                                    <div>
                                        <label class="careers-label">السيرة الذاتية (PDF/Word) <span class="text-slate-400 font-medium">(اختياري)</span></label>
                                        <label class="upload-zone block cursor-pointer">
                                            <input type="file" name="cv" accept=".pdf,.doc,.docx,application/pdf" class="hidden"
                                                   @change="cvName = $event.target.files[0]?.name || ''">
                                            <div class="flex items-center gap-3">
                                                <div class="w-11 h-11 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center flex-shrink-0">
                                                    <i class="fas fa-cloud-upload-alt"></i>
                                                </div>
                                                <div class="min-w-0">
                                                    <p class="text-sm font-bold text-slate-800" x-text="cvName || 'اضغط لاختيار الملف (اختياري)'"></p>
                                                    <p class="text-xs text-slate-500 mt-0.5">حد أقصى 5 ميجابايت — يمكنك التقديم بدون رفع CV</p>
                                                </div>
                                            </div>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <button type="submit" class="careers-btn-submit w-full">
                                <i class="fas fa-paper-plane"></i>
                                إرسال طلب التقديم
                            </button>
                        </form>
                    </div>
                @else
                    <div class="content-panel p-8 text-center">
                        <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-rose-50 text-rose-500 flex items-center justify-center text-2xl">
                            <i class="fas fa-lock"></i>
                        </div>
                        <h3 class="text-lg font-extrabold text-slate-900 mb-2">التقديم مغلق حالياً</h3>
                        <p class="text-sm text-slate-600 mb-5">يمكنك متابعة باقي فرص التدريب المفتوحة.</p>
                        <a href="{{ route('public.internships.index') }}" class="careers-btn-submit">
                            <i class="fas fa-list"></i>
                            عرض الفرص
                        </a>
                    </div>
                @endif

                <div class="content-panel p-5">
                    <h4 class="text-sm font-extrabold text-blue-900 mb-4">خطوات التقديم</h4>
                    <div class="space-y-4">
                        <div class="step-item">
                            <span class="step-num">1</span>
                            <div>
                                <p class="text-sm font-bold text-slate-800">املأ بياناتك</p>
                                <p class="text-xs text-slate-500">الاسم والبريد والمعلومات الأكاديمية</p>
                            </div>
                        </div>
                        <div class="step-item">
                            <span class="step-num">2</span>
                            <div>
                                <p class="text-sm font-bold text-slate-800">أضف روابطك أو السيرة (اختياري)</p>
                                <p class="text-xs text-slate-500">Portfolio / GitHub / CV إن رغبت</p>
                            </div>
                        </div>
                        <div class="step-item">
                            <span class="step-num">3</span>
                            <div>
                                <p class="text-sm font-bold text-slate-800">انتظر المراجعة</p>
                                <p class="text-xs text-slate-500">فريق التدريب يتواصل مع المناسبين</p>
                            </div>
                        </div>
                    </div>
                </div>
            </aside>
        </div>
    </div>
</section>
@endsection
