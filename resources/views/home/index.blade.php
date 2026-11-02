{{-- الصفحة الرئيسية الجديدة: مدرسة متخصصة في البيانات والذكاء الاصطناعي (docs/HOMEPAGE.md). عربي فقط؛ الإنجليزي على welcome. --}}
@php
    $photo1600 = asset('images/home/classroom-1600.jpg');
    $photo900 = asset('images/home/classroom-900.jpg');

    // البرنامج الرئيسي: أول دبلومة نشطة من لوحة الدبلومات، وإلا أول مسار تعليمي
    $paths = collect($landingPaths ?? []);
    $flagship = null;
    if ($homeDiploma) {
        $flagship = [
            'name' => $homeDiploma->name,
            'summary' => $homeDiploma->summary,
            'price' => $homeDiploma->price_online && (float) $homeDiploma->price_online > 0 ? number_format((float) $homeDiploma->price_online, 0).' ج.م' : null,
            'highlights' => array_slice($homeDiploma->landingHighlights(), 0, 6),
            'url' => $homeDiploma->landingUrl(),
            'label' => 'دبلومة · البرنامج الرئيسي',
        ];
    } elseif ($paths->isNotEmpty()) {
        $p = $paths->first();
        $flagship = [
            'name' => $p->name,
            'summary' => $p->description ? \Illuminate\Support\Str::limit(strip_tags($p->description), 220) : null,
            'price' => $p->price > 0 ? number_format($p->price, 0).' ج.م' : null,
            'highlights' => $p->courses_count ? [$p->courses_count.' كورس داخل المسار'] : [],
            'url' => route('public.learning-path.show', $p->slug),
            'label' => 'مسار تعليمي · البرنامج الرئيسي',
        ];
        $paths = $paths->slice(1);
    }
    $sidePrograms = $paths->take(2)->map(fn ($p) => [
        'label' => 'مسار تعليمي'.($p->courses_count ? ' · '.$p->courses_count.' كورس' : ''),
        'name' => $p->name,
        'text' => $p->description ? \Illuminate\Support\Str::limit(strip_tags($p->description), 110) : null,
        'url' => route('public.learning-path.show', $p->slug),
    ]);
    if ($sidePrograms->count() < 2) {
        $sidePrograms = $sidePrograms->concat(collect($featuredCourses ?? [])->take(2 - $sidePrograms->count())->map(fn ($c) => [
            'label' => 'كورس'.($c->lessons_count ? ' · '.$c->lessons_count.' درس' : ''),
            'name' => $c->title,
            'text' => $c->description ? \Illuminate\Support\Str::limit(strip_tags($c->description), 110) : null,
            'url' => route('public.course.show', $c->id),
        ]));
    }

    $intakes = collect($homeIntakes ?? []);
    $hasCampus = $intakes->contains('mode_key', 'campus');
    $hasOnline = $intakes->contains('mode_key', 'online');
    $hasRecorded = $intakes->contains('mode_key', 'recorded');
    $hasCourses = $intakes->contains('kind', 'course');
    $hasDiplomas = $intakes->contains('kind', 'diploma');

    $goals = [
        ['id' => 'first-job', 'label' => 'أول وظيفة', 'text' => 'حديث التخرّج؟ ابدأ ببرنامج من الصفر، واخرج بمشروع وملف أعمال وشهادة يمكن التحقق منها.', 'cta' => 'اعرض الدفعات القادمة', 'href' => '#intakes'],
        ['id' => 'switch', 'label' => 'تغيير المسار', 'text' => 'تعمل في مجال آخر؟ الدفعات المسائية والمحاضرات المسجّلة تسمح لك بالدراسة دون ترك عملك.', 'cta' => 'احجز استشارة', 'href' => '#consult'],
        ['id' => 'upskill', 'label' => 'تطوير مهاراتي', 'text' => 'تريد أن تعمل أسرع أو تترقّى؟ الكورسات القصيرة تركّز على أداة واحدة تستخدمها من الأسبوع الأول.', 'cta' => 'تصفّح الكورسات', 'href' => route('public.courses')],
        ['id' => 'team', 'label' => 'تدريب فريق', 'text' => 'تدير فريقاً؟ نصمّم برنامجاً على احتياج شركتك، مع متابعة لتقدّم كل موظف.', 'cta' => 'اطلب عرض تدريب', 'href' => '#business'],
    ];
    $faqs = [
        ['q' => 'هل أحتاج خبرة سابقة؟', 'a' => 'لا في أغلب البرامج. يساعدك المستشار الأكاديمي على اختيار نقطة البداية المناسبة لمستواك.'],
        ['q' => 'هل يمكن تقسيط الرسوم؟', 'a' => 'نعم، تتوفّر خطط تقسيط للبرامج الطويلة، وتُحدَّد تفاصيلها عند التسجيل.'],
        ['q' => 'ماذا لو فاتتني محاضرة؟', 'a' => 'المحاضرات المباشرة تُسجَّل وتُتاح لك على بوابة الطالب.'],
        ['q' => 'كيف تتحقق الشركات من شهادتي؟', 'a' => 'لكل شهادة رقم تسلسلي يمكن لأي جهة التحقق منه من صفحة التحقق من الشهادات على موقعنا.'],
        ['q' => 'ما الفرق بين الأونلاين المباشر والحضوري؟', 'a' => 'المنهج والمحاضر واحد. الحضوري في الفرع، والأونلاين مباشر من أي مكان بالتفاعل نفسه.'],
    ];
@endphp
<!DOCTYPE html>
<html lang="ar" dir="rtl" itemscope itemtype="https://schema.org/EducationalOrganization">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('landing.meta.title') }}</title>
    <meta name="description" content="{{ __('landing.meta.description') }}">
    <meta name="robots" content="index, follow, max-image-preview:large">
    <link rel="canonical" href="{{ url('/') }}">
    <link rel="alternate" hreflang="ar" href="{{ url('/') }}?lang=ar">
    <link rel="alternate" hreflang="en" href="{{ url('/') }}?lang=en">
    <link rel="alternate" hreflang="x-default" href="{{ url('/') }}?lang=ar">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url('/') }}">
    <meta property="og:title" content="{{ __('landing.meta.og_title') }}">
    <meta property="og:description" content="{{ __('landing.meta.og_description') }}">
    <meta property="og:image" content="{{ $photo1600 }}">
    <meta property="og:locale" content="ar_AR">
    <meta property="og:site_name" content="Mindlytics">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:image" content="{{ $photo1600 }}">
    @include('components.favicon-meta')
    <x-tracking-tags placement="head" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@400;500;600;700&family=Noto+Kufi+Arabic:wght@600;700&display=swap">
    <link rel="preload" as="image" href="{{ $photo1600 }}" imagesrcset="{{ $photo900 }} 900w, {{ $photo1600 }} 1600w" imagesizes="(max-width: 960px) 100vw, 50vw">
    @include('home.partials.styles')
</head>
<body>
<x-tracking-tags placement="body" />

<div class="h-topbar">
    <div class="h-wrap">
        <span>القبول مفتوح للدفعات القادمة — <a href="#intakes" style="color: var(--h-accent); font-weight: 600">اعرض المواعيد</a></span>
        <div class="h-topbar-links">
            <a href="{{ route('public.certificates.verify') }}" class="h-hide-sm">التحقق من شهادة</a>
            <a href="{{ url('/') }}?lang=en">English</a>
            @auth
                <a href="{{ route('dashboard') }}" style="font-weight: 600">حسابي</a>
            @else
                <a href="{{ route('login') }}" style="font-weight: 600">بوابة الطالب</a>
            @endauth
        </div>
    </div>
</div>

<header class="h-header">
    <div class="h-wrap">
        <a href="{{ url('/') }}" class="h-logo" aria-label="Mindlytics — الرئيسية">
            <span class="h-logo-mark" aria-hidden="true">M</span>
            <span class="h-logo-text"><b>Mindlytics</b><span>مدرسة البيانات والذكاء الاصطناعي</span></span>
        </a>
        <nav class="h-nav" aria-label="القائمة الرئيسية">
            <a href="#programs">البرامج</a>
            <a href="#intakes">الدفعات القادمة</a>
            <a href="{{ route('public.courses') }}">الكورسات</a>
            @if(($homeInstructors ?? collect())->isNotEmpty())<a href="#faculty">المحاضرون</a>@endif
            <a href="#business">للشركات</a>
        </nav>
        <a href="#consult" class="h-btn h-btn-primary h-header-cta">احجز استشارة</a>
        <button type="button" class="h-menu-btn" aria-expanded="false" aria-controls="h-mobile-menu" aria-label="فتح القائمة" data-menu-toggle>
            <svg width="20" height="14" viewBox="0 0 20 14" aria-hidden="true"><path d="M0 1H20M0 7H20M0 13H20" stroke="#17142A" stroke-width="2" fill="none"/></svg>
        </button>
    </div>
    <nav id="h-mobile-menu" class="h-mobile-menu" aria-label="القائمة على الموبايل">
        <a href="#intakes" data-menu-close>الدفعات القادمة</a>
        <a href="#programs" data-menu-close>البرامج</a>
        <a href="{{ route('public.courses') }}">الكورسات</a>
        <a href="{{ route('public.certificates.verify') }}">التحقق من شهادة</a>
        <a href="#business" data-menu-close>للشركات</a>
        <a href="{{ route('login') }}" style="color: var(--h-brand)">بوابة الطالب</a>
    </nav>
</header>

<main>
    {{-- البطل --}}
    <section class="h-hero" aria-labelledby="h-hero-title">
        <div class="h-hero-copy">
            <div class="h-hero-inner">
                <span class="h-eyebrow h-eyebrow-accent" style="margin: 0">مدرسة متخصصة في البيانات والذكاء الاصطناعي</span>
                <h1 id="h-hero-title">نُعِدّ محللي بيانات جاهزين لسوق العمل</h1>
                <p>برامج بدفعات لها مواعيد ثابتة، ومحاضرون من السوق، ومشروع تخرّج على بيانات حقيقية، وشهادة يمكن لأي جهة التحقق منها.</p>
                <div class="h-hero-actions">
                    <a href="#intakes" class="h-btn h-btn-accent h-btn-lg">ابحث عن دفعتك</a>
                    <a href="#consult" class="h-link">أو احجز استشارة مجانية</a>
                </div>
                <ul class="h-hero-facts">
                    <li>أونلاين مباشر أو حضوري</li>
                    <li>شهادة برقم تحقق</li>
                    <li>تقسيط للبرامج الطويلة</li>
                </ul>
            </div>
        </div>
        <figure class="h-photo">
            <img src="{{ $photo1600 }}" srcset="{{ $photo900 }} 900w, {{ $photo1600 }} 1600w" sizes="(max-width: 960px) 100vw, 50vw" width="1600" height="1067" alt="طلاب Mindlytics مع المحاضر داخل قاعة الدراسة في الفرع" fetchpriority="high">
            <figcaption>طلاب Mindlytics داخل الفرع</figcaption>
        </figure>
    </section>

    {{-- الأرقام: من قاعدة البيانات، وتختفي لو صفر --}}
    @if(($statsLearners ?? 0) > 0 || ($statsCourses ?? 0) > 0)
        <section class="h-stats" aria-label="Mindlytics بالأرقام">
            <div class="h-wrap">
                @if(($statsLearners ?? 0) > 0)<div class="h-stat"><b>+{{ number_format($statsLearners) }}</b><span>متعلّم مسجّل</span></div>@endif
                @if(($statsCourses ?? 0) > 0)<div class="h-stat"><b>+{{ number_format($statsCourses) }}</b><span>كورس وبرنامج</span></div>@endif
                @if(($statsLearningPaths ?? 0) > 0)<div class="h-stat"><b>{{ number_format($statsLearningPaths) }}</b><span>مسار تعليمي</span></div>@endif
                <div class="h-stat"><b>2</b><span>طريقتا دراسة: أونلاين مباشر وحضوري</span></div>
            </div>
        </section>
    @endif

    {{-- الدفعات القادمة --}}
    <section id="intakes" class="h-section h-section-mist" aria-labelledby="h-intakes-title">
        <div class="h-wrap">
            <div class="h-section-head">
                <div>
                    <span class="h-eyebrow">الدفعات القادمة</span>
                    <h2 id="h-intakes-title" class="h-h2">اختر نوع البرنامج وطريقة الدراسة، وشاهد أقرب دفعة</h2>
                </div>
                @if($intakes->isNotEmpty())<span style="font-size: 15px; color: var(--h-text-2)" aria-live="polite" data-intake-count>{{ $intakes->count() }} دفعات متاحة</span>@endif
            </div>

            @if($intakes->isNotEmpty())
                @if(($hasCourses && $hasDiplomas) || ($hasCampus + $hasOnline + $hasRecorded) > 1)
                    <div class="h-filters">
                        @if($hasCourses && $hasDiplomas)
                            <div class="h-filter-group" role="group" aria-label="نوع البرنامج">
                                <span>النوع</span>
                                <button type="button" class="h-chip" aria-pressed="true" data-filter="kind" data-value="all">الكل</button>
                                <button type="button" class="h-chip" aria-pressed="false" data-filter="kind" data-value="diploma">دبلومات</button>
                                <button type="button" class="h-chip" aria-pressed="false" data-filter="kind" data-value="course">كورسات</button>
                            </div>
                        @endif
                        @if(($hasCampus + $hasOnline + $hasRecorded) > 1)
                            <div class="h-filter-group" role="group" aria-label="طريقة الدراسة">
                                <span>الطريقة</span>
                                <button type="button" class="h-chip" aria-pressed="true" data-filter="mode" data-value="all">الكل</button>
                                @if($hasOnline)<button type="button" class="h-chip" aria-pressed="false" data-filter="mode" data-value="online">أونلاين</button>@endif
                                @if($hasCampus)<button type="button" class="h-chip" aria-pressed="false" data-filter="mode" data-value="campus">حضوري</button>@endif
                                @if($hasRecorded)<button type="button" class="h-chip" aria-pressed="false" data-filter="mode" data-value="recorded">مسجّل</button>@endif
                            </div>
                        @endif
                    </div>
                @endif

                <div class="h-cards">
                    @foreach($intakes as $i)
                        <article class="h-intake" data-kind="{{ $i['kind'] }}" data-mode="{{ $i['mode_key'] }}">
                            <div class="h-intake-top">
                                <div>
                                    <span class="h-intake-type">{{ $i['type'] }}</span>
                                    <h3>{{ $i['name'] }}</h3>
                                </div>
                                <span class="h-tag">{{ \Illuminate\Support\Str::limit($i['mode'], 22) }}</span>
                            </div>
                            <dl class="h-facts">
                                <div><dt>تبدأ</dt><dd>{{ $i['start'] ?? 'يُعلن قريباً' }}</dd></div>
                                <div><dt>المواعيد</dt><dd>{{ $i['days'] ?? 'تُحدَّد مع الدفعة' }}</dd></div>
                                <div><dt>الرسوم</dt><dd>{{ $i['price'] ?? 'تواصل معنا' }}</dd></div>
                            </dl>
                            <div class="h-intake-actions">
                                <a href="#consult" class="h-btn h-btn-primary" data-program="{{ $i['name'] }}">احجز مقعدك</a>
                                @if($i['url'])<a href="{{ $i['url'] }}" class="h-link" style="font-size: 15px">التفاصيل</a>@endif
                            </div>
                        </article>
                    @endforeach
                </div>
                <div class="h-empty" data-intake-empty hidden>
                    <span>لا توجد دفعة قادمة بهذه الاختيارات حالياً. اترك بياناتك ونبلغك أول ما تُفتح دفعة جديدة.</span>
                    <a href="#consult" class="h-btn h-btn-outline">أبلغوني بالدفعة القادمة</a>
                </div>
            @else
                <div class="h-empty">
                    <span>تُعلن مواعيد الدفعات القادمة قريباً. اترك بياناتك ونبلغك أول ما يُفتح التسجيل.</span>
                    <a href="#consult" class="h-btn h-btn-outline">أبلغوني بالدفعة القادمة</a>
                </div>
            @endif
        </div>
    </section>

    {{-- البرامج --}}
    @if($flagship || $sidePrograms->isNotEmpty())
        <section id="programs" class="h-section" aria-labelledby="h-programs-title">
            <div class="h-wrap">
                <div class="h-section-head">
                    <div>
                        <span class="h-eyebrow">البرامج</span>
                        <h2 id="h-programs-title" class="h-h2">برنامج رئيسي، وبرامج أقصر تكمّله</h2>
                    </div>
                    <a href="{{ route('public.learning-paths.index') }}" class="h-link">كل المسارات ←</a>
                </div>
                <div class="h-programs">
                    @if($flagship)
                        <article class="h-flagship">
                            <div class="h-flagship-head">
                                <div>
                                    <span class="h-eyebrow" style="margin-bottom: 4px">{{ $flagship['label'] }}</span>
                                    <h3>{{ $flagship['name'] }}</h3>
                                </div>
                                @if($flagship['price'])<span class="h-price">{{ $flagship['price'] }}</span>@endif
                            </div>
                            @if($flagship['summary'])<p style="margin: 0; color: var(--h-text-2)">{{ $flagship['summary'] }}</p>@endif
                            @if(! empty($flagship['highlights']))
                                <ul class="h-highlights">
                                    @foreach($flagship['highlights'] as $h)<li>{{ $h }}</li>@endforeach
                                </ul>
                            @endif
                            <div style="display: flex; flex-wrap: wrap; gap: 12px; margin-top: auto">
                                <a href="#intakes" class="h-btn h-btn-primary">مواعيد الدفعات</a>
                                @if($flagship['url'])<a href="{{ $flagship['url'] }}" class="h-btn h-btn-outline">تفاصيل البرنامج</a>@endif
                            </div>
                        </article>
                    @endif
                    @if($sidePrograms->isNotEmpty())
                        <div class="h-side-programs">
                            @foreach($sidePrograms as $sp)
                                <a href="{{ $sp['url'] }}" class="h-program">
                                    <small>{{ $sp['label'] }}</small>
                                    <b>{{ $sp['name'] }}</b>
                                    @if($sp['text'])<span>{{ $sp['text'] }}</span>@endif
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </section>
    @endif

    {{-- ابدأ من هدفك --}}
    <section class="h-section" style="padding-top: 0" aria-labelledby="h-goals-title">
        <div class="h-wrap">
            <span class="h-eyebrow">ابدأ من هدفك</span>
            <h2 id="h-goals-title" class="h-h2" style="margin-bottom: 24px">ما الذي تريد الوصول إليه؟</h2>
            <div class="h-tabs" role="tablist" aria-label="اختر هدفك">
                @foreach($goals as $g)
                    <button type="button" class="h-tab" role="tab" id="h-tab-{{ $g['id'] }}" aria-controls="h-panel-{{ $g['id'] }}" aria-selected="{{ $loop->first ? 'true' : 'false' }}" tabindex="{{ $loop->first ? '0' : '-1' }}" data-tab>{{ $g['label'] }}</button>
                @endforeach
            </div>
            @foreach($goals as $g)
                <div class="h-panel" role="tabpanel" id="h-panel-{{ $g['id'] }}" aria-labelledby="h-tab-{{ $g['id'] }}" @unless($loop->first) hidden @endunless>
                    <p>{{ $g['text'] }}</p>
                    <a href="{{ $g['href'] }}" class="h-btn h-btn-outline">{{ $g['cta'] }}</a>
                </div>
            @endforeach
        </div>
    </section>

    {{-- نظام الدراسة + التحقق من الشهادة --}}
    <section class="h-section h-section-mist" aria-labelledby="h-system-title">
        <div class="h-wrap h-split">
            <div class="h-grow">
                <span class="h-eyebrow">نظام الدراسة</span>
                <h2 id="h-system-title" class="h-h2">حضور ومتابعة وتقييم، لا مشاهدة فقط</h2>
                <div class="h-system">
                    <div><b>محاضرات مباشرة</b><span>أونلاين أو في الفرع، مع تسجيل للمحاضرات.</span></div>
                    <div><b>بوابة الطالب</b><span>واجبات واختبارات وتقدّم واضح لكل طالب.</span></div>
                    <div><b>مشروع تطبيقي</b><span>على بيانات أو حالة عمل حقيقية، يُضاف إلى ملف أعمالك.</span></div>
                    <div><b>شهادة برقم تحقق</b><span>يمكن لأي جهة توظيف التأكد منها من موقعنا.</span></div>
                </div>
            </div>
            <form class="h-side h-boxform" method="get" action="{{ route('public.certificates.verify') }}" aria-labelledby="h-verify-title">
                <h2 id="h-verify-title">تحقّق من شهادة</h2>
                <p>أدخل الرقم التسلسلي أو رمز التحقق المكتوب على الشهادة.</p>
                <label class="h-field">رقم الشهادة
                    <input type="text" name="code" dir="ltr" required autocomplete="off" style="text-align: right">
                </label>
                <button type="submit" class="h-btn" style="background: var(--h-ink); color: #FFFFFF">تحقّق</button>
            </form>
        </div>
    </section>

    {{-- المحاضرون --}}
    @if(($homeInstructors ?? collect())->isNotEmpty())
        <section id="faculty" class="h-section" aria-labelledby="h-faculty-title">
            <div class="h-wrap">
                <div class="h-section-head">
                    <div>
                        <span class="h-eyebrow">المحاضرون</span>
                        <h2 id="h-faculty-title" class="h-h2">يدرّسك من يعمل في المجال فعلاً</h2>
                    </div>
                    <a href="{{ route('public.instructors.index') }}" class="h-link">كل المحاضرين ←</a>
                </div>
                <div class="h-faculty">
                    @foreach($homeInstructors as $profile)
                        @php($initial = mb_substr(trim((string) $profile->user->name), 0, 1))
                        <a href="{{ route('public.instructors.show', $profile->user->id) }}" class="h-person">
                            <span class="h-person-photo">
                                @if($profile->photo_url)
                                    <img src="{{ $profile->photo_url }}" alt="{{ $profile->user->name }}" loading="lazy">
                                @else
                                    <span aria-hidden="true">{{ $initial }}</span>
                                @endif
                            </span>
                            <b>{{ $profile->user->name }}</b>
                            @if($profile->headline)<small>{{ $profile->headline }}</small>@endif
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- الرسوم --}}
    @if($flagship && $flagship['price'])
        <section class="h-section h-section-dark" aria-labelledby="h-fees-title">
            <div class="h-wrap h-split">
                <div class="h-side">
                    <span class="h-eyebrow h-eyebrow-accent">الرسوم والتقسيط</span>
                    <h2 id="h-fees-title" class="h-h2" style="color: #FFFFFF">رسوم واضحة من البداية، وطرق دفع مرنة</h2>
                    <p class="h-lead" style="color: var(--h-on-dark-2)">لا توجد رسوم مخفية. تُحدَّد خطة الدفع عند التسجيل، والدفع أونلاين أو في الفرع.</p>
                </div>
                <div class="h-grow h-fees">
                    <div><small>{{ $flagship['name'] }}</small><b>{{ $flagship['price'] }}</b><span>سداد كامل</span></div>
                    <div><small>تقسيط</small><b>متاح</b><span>مقدّم ثم أقساط، حسب خطة البرنامج</span></div>
                    <div><small>استشارة قبل الدفع</small><b>مجانية</b><span>نساعدك تختار البرنامج المناسب أولاً</span></div>
                </div>
            </div>
        </section>
    @endif

    {{-- الشركات --}}
    <section id="business" class="h-business" aria-labelledby="h-business-title">
        <div class="h-wrap">
            <div style="flex: 1 1 520px; min-width: 0">
                <h2 id="h-business-title" class="h-h2" style="color: #FFFFFF; font-size: 28px">تدريب فرق الشركات على البيانات والذكاء الاصطناعي</h2>
                <p>برنامج مخصّص لاحتياج فريقك، في مقرّكم أو في الفرع، مع متابعة لتقدّم كل موظف.</p>
            </div>
            <a href="#consult" class="h-btn h-btn-accent h-btn-lg" data-program="تدريب لشركة">اطلب عرض تدريب</a>
        </div>
    </section>

    {{-- الأسئلة + الاستشارة --}}
    <section id="consult" class="h-section" aria-labelledby="h-faq-title">
        <div class="h-wrap h-split">
            <div class="h-grow" style="flex-basis: 460px">
                <h2 id="h-faq-title" class="h-h2" style="margin-bottom: 20px">أسئلة متكرّرة قبل التسجيل</h2>
                <div class="h-faq">
                    @foreach($faqs as $f)
                        <details @if($loop->first) open @endif>
                            <summary>{{ $f['q'] }}</summary>
                            <p>{{ $f['a'] }}</p>
                        </details>
                    @endforeach
                </div>
                <a href="{{ route('public.faq') }}" class="h-link" style="display: inline-block; margin-top: 16px">كل الأسئلة ←</a>
            </div>
            <form class="h-side h-boxform" style="background: var(--h-mist); flex-basis: 400px" method="post" action="{{ route('public.contact.store') }}" aria-labelledby="h-consult-title" data-consult-form>
                @csrf
                <h2 id="h-consult-title">احجز استشارة أكاديمية مجانية</h2>
                <p>يتواصل معك مستشار ليقترح البرنامج والدفعة المناسبين لك.</p>
                <label class="h-field">الاسم
                    <input type="text" name="name" required maxlength="255" autocomplete="name" value="{{ old('name') }}">
                </label>
                <label class="h-field">رقم الموبايل (واتساب)
                    <input type="tel" name="phone" maxlength="20" autocomplete="tel" dir="ltr" style="text-align: right" value="{{ old('phone') }}">
                </label>
                <label class="h-field">البريد الإلكتروني
                    <input type="email" name="email" required maxlength="255" autocomplete="email" dir="ltr" style="text-align: right" value="{{ old('email') }}">
                </label>
                <label class="h-field">البرنامج الذي يهمّك
                    <select name="program" data-program-select>
                        @foreach($intakes->pluck('name')->unique()->take(8) as $name)<option>{{ $name }}</option>@endforeach
                        <option>تدريب لشركة</option>
                        <option>لم أحدّد بعد</option>
                    </select>
                </label>
                <input type="hidden" name="subject" value="طلب استشارة أكاديمية — الصفحة الرئيسية">
                <input type="hidden" name="message" value="طلب استشارة أكاديمية من الصفحة الرئيسية." data-consult-message>
                <button type="submit" class="h-btn h-btn-primary h-btn-lg">اطلب الاستشارة</button>
                @if($errors->any())<p role="alert" style="color: #B42318; margin: 0">{{ $errors->first() }}</p>@endif
            </form>
        </div>
    </section>
</main>

<footer class="h-footer">
    <div class="h-wrap">
        <div class="h-footer-grid">
            <div style="display: flex; flex-direction: column; gap: 8px">
                <b class="h-kufi" style="font-size: 19px">Mindlytics</b>
                <span>مدرسة متخصصة في البيانات والذكاء الاصطناعي.</span>
            </div>
            <nav aria-label="البرامج">
                <b>البرامج</b>
                <a href="{{ route('public.learning-paths.index') }}">المسارات التعليمية</a>
                <a href="{{ route('public.courses') }}">الكورسات</a>
                <a href="#business">للشركات</a>
            </nav>
            <nav aria-label="الطلاب">
                <b>الطلاب</b>
                <a href="{{ route('login') }}">بوابة الطالب</a>
                <a href="{{ route('public.certificates.verify') }}">التحقق من شهادة</a>
                <a href="{{ route('public.faq') }}">الأسئلة الشائعة</a>
            </nav>
            <nav aria-label="المدرسة">
                <b>Mindlytics</b>
                <a href="{{ route('public.about') }}">عن Mindlytics</a>
                <a href="{{ route('public.instructors.index') }}">المحاضرون</a>
                <a href="{{ route('public.contact') }}">تواصل معنا</a>
            </nav>
        </div>
        <div class="h-footer-bottom">
            <span>© {{ date('Y') }} Mindlytics</span>
            <span style="display: flex; gap: 16px"><a href="{{ route('public.terms') }}">الشروط والأحكام</a><a href="{{ route('public.privacy') }}">سياسة الخصوصية</a></span>
        </div>
    </div>
</footer>

<div class="h-mobile-bar">
    <a href="#consult" class="h-btn h-btn-primary">احجز استشارة مجانية</a>
    <a href="#intakes" class="h-btn h-btn-outline">الدفعات</a>
</div>

@include('home.partials.scripts')

@if(isset($popupAd) && $popupAd)
    @include('partials.popup-ad', ['ad' => $popupAd])
@endif
</body>
</html>
