{{-- الصفحة الرئيسية: أكاديمية متخصصة في البرمجة وتطوير البرمجيات (docs/HOMEPAGE.md). عربي وإنجليزي ($t). --}}
@php
    $siteAr = app()->getLocale() === 'ar';
    $t = fn (string $ar, string $en) => $siteAr ? $ar : $en;
    $photo1600 = asset('images/home/classroom-1600.jpg');
    $photo900 = asset('images/home/classroom-900.jpg');

    // البرنامج الرئيسي: أول دبلومة نشطة من لوحة الدبلومات، وإلا أول مسار تعليمي
    $paths = collect($landingPaths ?? []);
    $flagship = null;
    if ($homeDiploma) {
        $flagship = [
            'name' => $homeDiploma->name,
            'summary' => $homeDiploma->summary,
            // نفس سعر الـ checkout: سعر المسار المربوط لو موجود، وإلا سعر اللوحة
            'price' => $homeDiploma->publicPrice() !== null ? number_format($homeDiploma->publicPrice(), 0).$t(' ج.م', ' EGP') : null,
            'highlights' => array_slice($homeDiploma->landingHighlights(), 0, 6),
            'url' => $homeDiploma->detailsUrl(),
            'label' => $t('دبلومة · البرنامج الرئيسي', 'Diploma · flagship program'),
        ];
    } elseif ($paths->isNotEmpty()) {
        $p = $paths->first();
        $flagship = [
            'name' => $p->name,
            'summary' => $p->description ? \Illuminate\Support\Str::limit(strip_tags($p->description), 220) : null,
            'price' => $p->price > 0 ? number_format($p->price, 0).$t(' ج.م', ' EGP') : null,
            'highlights' => $p->courses_count ? [$p->courses_count.$t(' كورس داخل المسار', ' courses in this path')] : [],
            'url' => route('public.learning-path.show', $p->slug),
            'label' => $t('مسار تعليمي · البرنامج الرئيسي', 'Learning path · flagship program'),
        ];
        $paths = $paths->slice(1);
    }
    $sidePrograms = $paths->take(2)->map(fn ($p) => [
        'label' => $t('مسار تعليمي', 'Learning path').($p->courses_count ? ' · '.$p->courses_count.$t(' كورس', ' courses') : ''),
        'name' => $p->name,
        'text' => $p->description ? \Illuminate\Support\Str::limit(strip_tags($p->description), 110) : null,
        'url' => route('public.learning-path.show', $p->slug),
    ]);
    if ($sidePrograms->count() < 2) {
        $sidePrograms = $sidePrograms->concat(collect($featuredCourses ?? [])->take(2 - $sidePrograms->count())->map(fn ($c) => [
            'label' => $t('كورس', 'Course').($c->lessons_count ? ' · '.$c->lessons_count.$t(' درس', ' lessons') : ''),
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
        ['id' => 'first-job', 'label' => $t('أول وظيفة', 'First job'), 'text' => $t('حديث التخرّج؟ ابدأ ببرنامج من الصفر، واخرج بمشروع وملف أعمال وشهادة يمكن التحقق منها.', 'Just graduated? Start a program from scratch and finish with a project, a portfolio and a verifiable certificate.'), 'cta' => $t('اعرض الدفعات القادمة', 'See upcoming intakes'), 'href' => '#intakes'],
        ['id' => 'switch', 'label' => $t('تغيير المسار', 'Career change'), 'text' => $t('تعمل في مجال آخر؟ الدفعات المسائية والمحاضرات المسجّلة تسمح لك بالدراسة دون ترك عملك.', 'Working in another field? Evening intakes and recorded sessions let you study without leaving your job.'), 'cta' => $t('احجز استشارة', 'Book a consultation'), 'href' => '#consult', 'goal' => 'career_change'],
        ['id' => 'upskill', 'label' => $t('تطوير مهاراتي', 'Upskill'), 'text' => $t('تريد أن تعمل أسرع أو تترقّى؟ الكورسات القصيرة تركّز على أداة واحدة تستخدمها من الأسبوع الأول.', 'Want to work faster or get promoted? Short courses focus on one tool you use from week one.'), 'cta' => $t('تصفّح الكورسات', 'Browse courses'), 'href' => route('public.courses')],
        ['id' => 'team', 'label' => $t('تدريب فريق', 'Train a team'), 'text' => $t('تدير فريقاً؟ نصمّم برنامجاً على احتياج شركتك، مع متابعة لتقدّم كل موظف.', 'Leading a team? We design a program around your company’s needs, with progress tracking for every employee.'), 'cta' => $t('اطلب عرض تدريب', 'Request a training proposal'), 'href' => '#business'],
    ];
    // الحقائق دي بتتحسب من البيانات (LandingController::homeFacts) عشان الصفحة ما تقولش حاجة مش موجودة
    $facts = $homeFacts ?? ['campus' => false, 'installments' => false];
    $faqs = [
        ['q' => $t('هل أحتاج خبرة سابقة؟', 'Do I need prior experience?'), 'a' => $t('لا في أغلب البرامج. يساعدك المستشار الأكاديمي على اختيار نقطة البداية المناسبة لمستواك.', 'Not for most programs. An academic advisor helps you pick the right starting point for your level.')],
        ['q' => $t('هل يمكن تقسيط الرسوم؟', 'Can I pay in instalments?'), 'a' => $facts['installments']
            ? $t('فيه خطط تقسيط لبعض البرامج، وبتظهر على كارت الدفعة لما تكون متاحة. المستشار يوضّحلك الخطة قبل التسجيل.', 'Some programs have instalment plans, shown on the intake card when available. An advisor explains the plan before you enrol.')
            : $t('طرق الدفع بتتحدد مع المستشار قبل التسجيل، احجز جلسة واسأل عن البرنامج اللي يهمّك.', 'Payment options are set with an advisor before you enrol. Book a session and ask about the program you want.')],
        ['q' => $t('ماذا لو فاتتني محاضرة؟', 'What if I miss a session?'), 'a' => $t('المحاضرات المباشرة تُسجَّل وتُتاح لك على بوابة الطالب.', 'Live sessions are recorded and available on the student portal.')],
        ['q' => $t('كيف تتحقق الشركات من شهادتي؟', 'How do employers verify my certificate?'), 'a' => $t('لكل شهادة رقم تسلسلي يمكن لأي جهة التحقق منه من صفحة التحقق من الشهادات على موقعنا.', 'Every certificate has a serial number that anyone can check on our certificate verification page.')],
    ];
    if ($facts['campus']) {
        $faqs[] = ['q' => $t('ما الفرق بين الأونلاين المباشر والحضوري؟', 'What is the difference between live online and on campus?'), 'a' => $t('المنهج والمحاضر واحد. الحضوري في الفرع، والأونلاين مباشر من أي مكان بالتفاعل نفسه.', 'Same curriculum and instructor. On campus in the branch, or live online from anywhere with the same interaction.')];
    }
@endphp
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ $siteAr ? 'rtl' : 'ltr' }}" itemscope itemtype="https://schema.org/EducationalOrganization">
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
    <meta property="og:locale" content="{{ $siteAr ? 'ar_AR' : 'en_US' }}">
    <meta property="og:site_name" content="Mindlytics">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:image" content="{{ $photo1600 }}">
    @include('components.favicon-meta')
    <x-tracking-tags placement="head" />
    <link rel="preload" as="image" href="{{ $photo1600 }}" imagesrcset="{{ $photo900 }} 900w, {{ $photo1600 }} 1600w" imagesizes="(max-width: 960px) 100vw, 50vw">
    @include('components.site.styles')
</head>
<body class="h-site h-has-mobile-bar">
<x-tracking-tags placement="body" />

@include('components.site.header')

<main>
    {{-- البطل --}}
    <section class="h-hero" aria-labelledby="h-hero-title">
        <div class="h-hero-copy">
            <div class="h-hero-inner">
                <span class="h-eyebrow h-eyebrow-accent" style="margin: 0">{{ $t('أكاديمية متخصصة في البرمجة وتطوير البرمجيات', 'A specialized software development academy') }}</span>
                <h1 id="h-hero-title">{{ $t('نؤهّلك لمهنة حقيقية في عالم البرمجيات', 'We prepare you for a real career in software') }}</h1>
                <p>{{ $t('برامج في مختلف مجالات البرمجيات، بدفعات لها مواعيد ثابتة، ومحاضرون من السوق، ومشاريع تطبيقية حقيقية، وشهادة يمكن لأي جهة التحقق منها.', 'Programs across software development, with fixed intake dates, industry instructors, real projects and a certificate anyone can verify.') }}</p>
                <div class="h-hero-actions">
                    <a href="#intakes" class="h-btn h-btn-accent h-btn-lg">{{ $t('ابحث عن دفعتك', 'Find your intake') }}</a>
                    <a href="#consult" class="h-link">{{ $t('أو احجز استشارة مجانية', 'or book a free consultation') }}</a>
                </div>
                <ul class="h-hero-facts">
                    <li>{{ $facts['campus'] ? $t('أونلاين مباشر أو حضوري', 'Live online or on campus') : $t('أونلاين مباشر', 'Live online') }}</li>
                    <li>{{ $t('شهادة برقم تحقق', 'Verifiable certificate') }}</li>
                    @if($facts['installments'])<li>{{ $t('تقسيط لبعض البرامج', 'Instalments on some programs') }}</li>@endif
                </ul>
            </div>
        </div>
        <figure class="h-photo">
            <img src="{{ $photo1600 }}" srcset="{{ $photo900 }} 900w, {{ $photo1600 }} 1600w" sizes="(max-width: 960px) 100vw, 50vw" width="1600" height="1067" alt="{{ $t('طلاب Mindlytics مع المحاضر داخل قاعة الدراسة في الفرع', 'Mindlytics students with their instructor in a classroom on campus') }}" fetchpriority="high">
            <figcaption>{{ $t('طلاب Mindlytics داخل الفرع', 'Mindlytics students on campus') }}</figcaption>
        </figure>
    </section>

    {{-- الأرقام: من قاعدة البيانات، وتختفي لو صفر --}}
    @if(($statsLearners ?? 0) > 0 || ($statsCourses ?? 0) > 0)
        <section class="h-stats" aria-label="{{ $t('Mindlytics بالأرقام', 'Mindlytics in numbers') }}">
            <div class="h-wrap">
                @if(($statsLearners ?? 0) > 0)<div class="h-stat"><b>+{{ number_format($statsLearners) }}</b><span>{{ $t('متعلّم مسجّل', 'registered learners') }}</span></div>@endif
                @if(($statsCourses ?? 0) > 0)<div class="h-stat"><b>+{{ number_format($statsCourses) }}</b><span>{{ $t('كورس وبرنامج', 'courses and programs') }}</span></div>@endif
                @if(($statsLearningPaths ?? 0) > 0)<div class="h-stat"><b>{{ number_format($statsLearningPaths) }}</b><span>{{ $t('مسار تعليمي', 'Learning path') }}</span></div>@endif
                @if($intakes->isNotEmpty())<div class="h-stat"><b>{{ $intakes->count() }}</b><span>{{ $t('دفعات قادمة', 'upcoming intakes') }}</span></div>@endif
            </div>
        </section>
    @endif

    {{-- الدفعات القادمة --}}
    <section id="intakes" class="h-section h-section-mist" aria-labelledby="h-intakes-title">
        <div class="h-wrap">
            <div class="h-section-head">
                <div>
                    <span class="h-eyebrow">{{ $t('الدفعات القادمة', 'Upcoming intakes') }}</span>
                    <h2 id="h-intakes-title" class="h-h2">{{ $t('اختر نوع البرنامج وطريقة الدراسة، وشاهد أقرب دفعة', 'Pick a program type and study mode to see the next intake') }}</h2>
                </div>
                @if($intakes->isNotEmpty())<span style="font-size: 15px; color: var(--h-text-2)" aria-live="polite" data-intake-count data-l-none="{{ $t('لا توجد دفعات مطابقة', 'No matching intakes') }}" data-l-one="{{ $t('دفعة واحدة مطابقة', '1 matching intake') }}" data-l-many="{{ $t('دفعات مطابقة', 'matching intakes') }}">{{ $intakes->count() }} {{ $t('دفعات متاحة', 'intakes available') }}</span>@endif
            </div>

            @if($intakes->isNotEmpty())
                @if(($hasCourses && $hasDiplomas) || ($hasCampus + $hasOnline + $hasRecorded) > 1)
                    <div class="h-filters">
                        @if($hasCourses && $hasDiplomas)
                            <div class="h-filter-group" role="group" aria-label="{{ $t('نوع البرنامج', 'Program type') }}">
                                <span>{{ $t('النوع', 'Type') }}</span>
                                <button type="button" class="h-chip" aria-pressed="true" data-filter="kind" data-value="all">{{ $t('الكل', 'All') }}</button>
                                <button type="button" class="h-chip" aria-pressed="false" data-filter="kind" data-value="diploma">{{ $t('دبلومات', 'Diplomas') }}</button>
                                <button type="button" class="h-chip" aria-pressed="false" data-filter="kind" data-value="course">{{ $t('كورسات', 'Courses') }}</button>
                            </div>
                        @endif
                        @if(($hasCampus + $hasOnline + $hasRecorded) > 1)
                            <div class="h-filter-group" role="group" aria-label="{{ $t('طريقة الدراسة', 'Study mode') }}">
                                <span>{{ $t('الطريقة', 'Mode') }}</span>
                                <button type="button" class="h-chip" aria-pressed="true" data-filter="mode" data-value="all">{{ $t('الكل', 'All') }}</button>
                                @if($hasOnline)<button type="button" class="h-chip" aria-pressed="false" data-filter="mode" data-value="online">{{ $t('أونلاين', 'Online') }}</button>@endif
                                @if($hasCampus)<button type="button" class="h-chip" aria-pressed="false" data-filter="mode" data-value="campus">{{ $t('حضوري', 'On campus') }}</button>@endif
                                @if($hasRecorded)<button type="button" class="h-chip" aria-pressed="false" data-filter="mode" data-value="recorded">{{ $t('مسجّل', 'Recorded') }}</button>@endif
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
                                <div><dt>{{ $t('تبدأ', 'Starts') }}</dt><dd>{{ $i['start'] ?? $t('يُعلن قريباً', 'Announced soon') }}</dd></div>
                                <div><dt>{{ $t('المواعيد', 'Schedule') }}</dt><dd>{{ $i['days'] ?? $t('تُحدَّد مع الدفعة', 'Set with the intake') }}</dd></div>
                                <div><dt>{{ $t('الرسوم', 'Fees') }}</dt><dd>{{ $i['price'] ?? $t('تواصل معنا', 'Contact us') }}</dd></div>
                                @if(! empty($i['installments']))<div><dt>{{ $t('الدفع', 'Payment') }}</dt><dd>{{ $i['installments'] }}</dd></div>@endif
                            </dl>
                            <div class="h-intake-actions">
                                <a href="#consult" class="h-btn h-btn-primary" data-program="{{ $i['name'] }}">{{ $t('احجز مقعدك', 'Reserve your seat') }}</a>
                                @if($i['url'])<a href="{{ $i['url'] }}" class="h-link" style="font-size: 15px">{{ $t('التفاصيل', 'Details') }}</a>@endif
                            </div>
                        </article>
                    @endforeach
                </div>
                <div class="h-empty" data-intake-empty hidden>
                    <span>{{ $t('لا توجد دفعة قادمة بهذه الاختيارات حالياً. اترك بياناتك ونبلغك أول ما تُفتح دفعة جديدة.', 'No upcoming intake matches these choices right now. Leave your details and we will tell you when one opens.') }}</span>
                    <a href="#consult" class="h-btn h-btn-outline">{{ $t('أبلغوني بالدفعة القادمة', 'Notify me about the next intake') }}</a>
                </div>
            @else
                <div class="h-empty">
                    <span>{{ $t('تُعلن مواعيد الدفعات القادمة قريباً. اترك بياناتك ونبلغك أول ما يُفتح التسجيل.', 'Upcoming intake dates will be announced soon. Leave your details and we will tell you when registration opens.') }}</span>
                    <a href="#consult" class="h-btn h-btn-outline">{{ $t('أبلغوني بالدفعة القادمة', 'Notify me about the next intake') }}</a>
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
                        <span class="h-eyebrow">{{ $t('البرامج', 'Programs') }}</span>
                        <h2 id="h-programs-title" class="h-h2">{{ $t('برنامج رئيسي، وبرامج أقصر تكمّله', 'A flagship program, and shorter ones around it') }}</h2>
                    </div>
                    <a href="{{ route('public.learning-paths.index') }}" class="h-link">{{ $t('كل البرامج التدريبية ←', 'All programs →') }}</a>
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
                                <a href="#intakes" class="h-btn h-btn-primary">{{ $t('مواعيد الدفعات', 'Intake dates') }}</a>
                                @if($flagship['url'])<a href="{{ $flagship['url'] }}" class="h-btn h-btn-outline">{{ $t('تفاصيل البرنامج', 'Program details') }}</a>@endif
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
            <span class="h-eyebrow">{{ $t('ابدأ من هدفك', 'Start from your goal') }}</span>
            <h2 id="h-goals-title" class="h-h2" style="margin-bottom: 24px">{{ $t('ما الذي تريد الوصول إليه؟', 'What do you want to achieve?') }}</h2>
            <div class="h-tabs" role="tablist" aria-label="{{ $t('اختر هدفك', 'Choose your goal') }}">
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
                <span class="h-eyebrow">{{ $t('نظام الدراسة', 'How you study') }}</span>
                <h2 id="h-system-title" class="h-h2">{{ $t('حضور ومتابعة وتقييم، لا مشاهدة فقط', 'Attendance, follow-up and assessment, not just videos') }}</h2>
                <div class="h-system">
                    <div><b>{{ $t('محاضرات مباشرة', 'Live sessions') }}</b><span>{{ $facts['campus'] ? $t('أونلاين أو في الفرع، مع تسجيل للمحاضرات.', 'Online or on campus, with every session recorded.') : $t('أونلاين مباشر، مع تسجيل للمحاضرات.', 'Live online, with every session recorded.') }}</span></div>
                    <div><b>{{ $t('بوابة الطالب', 'Student portal') }}</b><span>{{ $t('واجبات واختبارات وتقدّم واضح لكل طالب.', 'Assignments, exams and clear progress for every student.') }}</span></div>
                    <div><b>{{ $t('مشروع تطبيقي', 'Applied project') }}</b><span>{{ $t('على مشروع أو حالة عمل حقيقية، يُضاف إلى ملف أعمالك.', 'On a real project or business case, added to your portfolio.') }}</span></div>
                    <div><b>{{ $t('شهادة برقم تحقق', 'Verifiable certificate') }}</b><span>{{ $t('يمكن لأي جهة توظيف التأكد منها من موقعنا.', 'Any employer can confirm it on our website.') }}</span></div>
                </div>
            </div>
            <form class="h-side h-boxform" method="get" action="{{ route('public.certificates.verify') }}" aria-labelledby="h-verify-title">
                <h2 id="h-verify-title">{{ $t('تحقّق من شهادة', 'Verify a certificate') }}</h2>
                <p>{{ $t('أدخل الرقم التسلسلي أو رمز التحقق المكتوب على الشهادة.', 'Enter the serial number or verification code printed on the certificate.') }}</p>
                <label class="h-field">{{ $t('رقم الشهادة', 'Certificate number') }}
                    <input type="text" name="code" dir="ltr" required autocomplete="off" style="text-align: right">
                </label>
                <button type="submit" class="h-btn" style="background: var(--h-ink); color: #FFFFFF">{{ $t('تحقّق', 'Verify') }}</button>
            </form>
        </div>
    </section>

    {{-- المحاضرون --}}
    @if(($homeInstructors ?? collect())->isNotEmpty())
        <section id="faculty" class="h-section" aria-labelledby="h-faculty-title">
            <div class="h-wrap">
                <div class="h-section-head">
                    <div>
                        <span class="h-eyebrow">{{ $t('المحاضرون', 'Instructors') }}</span>
                        <h2 id="h-faculty-title" class="h-h2">{{ $t('يدرّسك من يعمل في المجال فعلاً', 'Taught by people who work in the field') }}</h2>
                    </div>
                    <a href="{{ route('public.instructors.index') }}" class="h-link">{{ $t('كل المحاضرين ←', 'All instructors →') }}</a>
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
                    <span class="h-eyebrow h-eyebrow-accent">{{ $t('الرسوم والتقسيط', 'Fees and instalments') }}</span>
                    <h2 id="h-fees-title" class="h-h2" style="color: #FFFFFF">{{ $t('رسوم واضحة من البداية، وطرق دفع مرنة', 'Clear fees upfront, flexible ways to pay') }}</h2>
                    <p class="h-lead" style="color: var(--h-on-dark-2)">{{ $t('لا توجد رسوم مخفية. السعر هنا هو نفسه اللي بتدفعه عند التسجيل.', 'No hidden fees. The price here is the same one you pay at checkout.') }}</p>
                </div>
                <div class="h-grow h-fees">
                    <div><small>{{ $flagship['name'] }}</small><b>{{ $flagship['price'] }}</b><span>{{ $t('سداد كامل', 'Paid in full') }}</span></div>
                    @if($facts['installments'])<div><small>{{ $t('تقسيط', 'Instalments') }}</small><b>{{ $t('لبعض البرامج', 'Some programs') }}</b><span>{{ $t('بيظهر على كارت الدفعة لما يكون متاح', 'Shown on the intake card when available') }}</span></div>@endif
                    <div><small>{{ $t('استشارة قبل الدفع', 'Advice before you pay') }}</small><b>{{ $t('مجانية', 'Free') }}</b><span>{{ $t('نساعدك تختار البرنامج المناسب أولاً', 'We help you pick the right program first') }}</span></div>
                </div>
            </div>
        </section>
    @endif

    {{-- الشركات --}}
    <section id="business" class="h-business" aria-labelledby="h-business-title">
        <div class="h-wrap">
            <div style="flex: 1 1 520px; min-width: 0">
                <h2 id="h-business-title" class="h-h2" style="color: #FFFFFF; font-size: 28px">{{ $t('تدريب فرق الشركات على البرمجة والتقنيات الحديثة', 'Train your team in software and modern tech') }}</h2>
                <p>{{ $t('برنامج مخصّص لاحتياج فريقك، في مقرّكم أو في الفرع، مع متابعة لتقدّم كل موظف.', 'A program built for your team, at your office or our branch, with progress tracking for every employee.') }}</p>
            </div>
            <a href="#consult" class="h-btn h-btn-accent h-btn-lg" data-advising-goal="team">{{ $t('اطلب عرض تدريب', 'Request a training proposal') }}</a>
        </div>
    </section>

    {{-- الأسئلة + الاستشارة --}}
    <section id="consult" class="h-section" aria-labelledby="h-faq-title">
        <div class="h-wrap h-split">
            <div class="h-grow" style="flex-basis: 460px">
                <h2 id="h-faq-title" class="h-h2" style="margin-bottom: 20px">{{ $t('أسئلة متكرّرة قبل التسجيل', 'Questions before you enrol') }}</h2>
                <div class="h-faq">
                    @foreach($faqs as $f)
                        <details @if($loop->first) open @endif>
                            <summary>{{ $f['q'] }}</summary>
                            <p>{{ $f['a'] }}</p>
                        </details>
                    @endforeach
                </div>
                <a href="{{ route('public.faq') }}" class="h-link" style="display: inline-block; margin-top: 16px">{{ $t('كل الأسئلة ←', 'All questions →') }}</a>
            </div>
            <div class="h-side h-boxform h-adv-card" style="flex-basis: 400px">
                <span class="h-eyebrow" style="margin: 0">{{ $t('جلسة إرشاد مهني مجانية', 'Free career guidance session') }}</span>
                <h2 id="h-consult-title" class="h-kufi" style="font-size: 23px">{{ $t('مش عارف تبدأ منين؟ احجز جلسة مع مرشد', 'Not sure where to start? Book a session with an advisor') }}</h2>
                <ul class="h-adv-points">
                    <li>{{ $t('نفهم هدفك ومستواك الحالي', 'We understand your goal and current level') }}</li>
                    <li>{{ $t('نرشّحلك المسار والدفعة المناسبين', 'We recommend the right path and intake') }}</li>
                    <li>{{ $t('نرسم معاك خطة دخولك لسوق العمل', 'We map your way into the job market') }}</li>
                </ul>
                <a href="{{ route('advising.show') }}" data-advising-open class="h-btn h-btn-primary h-btn-lg">{{ $t('اختار ميعاد الجلسة', 'Pick a session time') }}</a>
                <p style="font-size: 13px">{{ $t('مجانية، والمواعيد المتاحة بتتحدّث أول بأول.', 'Free, with live availability.') }}</p>
            </div>
        </div>
    </section>
</main>

@include('components.site.footer')

@include('home.partials.scripts')

@if(isset($popupAd) && $popupAd)
    @include('partials.popup-ad', ['ad' => $popupAd])
@endif
</body>
</html>
