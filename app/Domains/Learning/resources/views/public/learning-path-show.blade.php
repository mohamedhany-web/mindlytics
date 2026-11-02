{{-- صفحة البرنامج التدريبي (AcademicYear) على هوية الموقع. بيانات الدفعة (البداية، المواعيد، المميزات) من لوحة الدبلومات المربوطة بالبرنامج. --}}
@php
    use Illuminate\Support\Str;

    $siteAr = app()->getLocale() === 'ar';
    $t = fn (string $ar, string $en) => $siteAr ? $ar : $en;
    $clean = fn ($v) => filled($v) && trim((string) $v) !== '—' ? trim((string) $v) : null;
    $slug = $learningPath->slug;
    $price = (float) ($learningPath->price ?? 0);
    $courses = $learningPath->courses ?? collect();
    $start = $board?->starts_at ? $board->starts_at->locale(app()->getLocale())->translatedFormat('j F Y') : ($board ? $clean($board->startDisplay()) : null);
    $video = \App\Support\IntroVideoResolver::resolve($learningPath->video_url ?? '');
    $summary = filled($board?->summary) ? $board->summary : $learningPath->description;
    $pending = ! ($isEnrolled ?? false) && ($enrollment ?? null) && $enrollment->status === 'pending';

    $facts = array_values(array_filter([
        ['icon' => 'fa-calendar-day', 'label' => $t('البداية', 'Starts'), 'value' => $start],
        ['icon' => 'fa-hourglass-half', 'label' => $t('المدة', 'Duration'), 'value' => $clean($board?->duration)],
        ['icon' => 'fa-calendar-week', 'label' => $t('أيام المحاضرات', 'Days'), 'value' => $clean($board?->schedule_days)],
        ['icon' => 'fa-clock', 'label' => $t('ساعات المحاضرة', 'Hours'), 'value' => $clean($board?->hours)],
        ['icon' => 'fa-laptop-house', 'label' => $t('نظام الدراسة', 'Format'), 'value' => $clean($board?->format)],
        ['icon' => 'fa-users', 'label' => $t('مناسب لـ', 'For'), 'value' => $clean($board?->audience)],
        ['icon' => 'fa-chalkboard-user', 'label' => $t('المحاضر', 'Instructor'), 'value' => $clean($board?->instructor_name)],
        ['icon' => 'fa-layer-group', 'label' => $t('الكورسات', 'Courses'), 'value' => $courses->count() > 0 ? $courses->count().' '.$t('كورس', 'courses') : null],
    ], fn ($f) => $f['value'] !== null));
    // المميزات المكتوبة في اللوحة بس (المميزات التلقائية بتكرر «نظرة سريعة»)
    $highlights = $board && is_array($board->highlights) ? array_values(array_filter($board->highlights)) : [];
    $chips = array_values(array_filter([
        $start ? ['label' => $t('تبدأ ', 'Starts ').$start, 'icon' => 'fas fa-calendar-day'] : null,
        $clean($board?->duration) ? ['label' => $clean($board->duration), 'icon' => 'fas fa-hourglass-half'] : null,
        $clean($board?->format) ? ['label' => $clean($board->format), 'icon' => 'fas fa-laptop-house'] : null,
    ]));
    $consultUrl = route('home').'#consult';
    $contactUrl = route('public.contact').'?subject='.urlencode($t('استفسار عن برنامج: ', 'Question about: ').$learningPath->name);
    $siteHideMobileBar = true;
@endphp
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ $siteAr ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $learningPath->name }} — Mindlytics Academy</title>
    <meta name="description" content="{{ Str::limit(strip_tags((string) $summary), 160) }}">
    <meta property="og:title" content="{{ $learningPath->name }} — Mindlytics Academy">
    <meta property="og:description" content="{{ Str::limit(strip_tags((string) $summary), 200) }}">
    <x-tracking-tags placement="head" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    @include('components.site.styles')
    @include('components.site.program-styles')
</head>
<body class="h-site h-has-mobile-bar">
    <x-tracking-tags placement="body" />
    @if(is_array(session('analytics_purchase')))
        <x-ecommerce-datalayer :payload="session('analytics_purchase')" />
    @endif

    @include('components.site.header')

    @include('components.site.page-head', [
        'eyebrow' => $t('برنامج تدريبي', 'Training program'),
        'title' => $learningPath->name,
        'subtitle' => filled($summary) ? Str::limit(strip_tags((string) $summary), 320) : null,
        'crumbs' => [['label' => $t('البرامج التدريبية', 'Programs'), 'url' => route('public.learning-paths.index')], ['label' => $learningPath->name]],
        'chips' => $chips,
    ])

    <main class="dip-body">
        <div class="h-wrap h-split">
            <div class="h-grow">
                @if(session('success'))<div class="pg-note is-good dip-block">{{ session('success') }}</div>@endif
                @if(session('info'))<div class="pg-note dip-block">{{ session('info') }}</div>@endif
                @if(session('error'))<div class="pg-note is-bad dip-block">{{ session('error') }}</div>@endif

                @if(in_array($video['type'], ['youtube', 'vimeo', 'bunny_embed'], true) && ! empty($video['embed']))
                    <section class="dip-block" aria-label="{{ $t('فيديو تعريفي', 'Intro video') }}">
                        <div class="pg-video"><iframe src="{{ $video['embed'] }}" title="{{ $learningPath->name }}" loading="lazy" allow="accelerometer; clipboard-write; encrypted-media; gyroscope; picture-in-picture; fullscreen" allowfullscreen></iframe></div>
                    </section>
                @elseif($video['type'] === 'html5' && ! empty($video['direct']))
                    <section class="dip-block" aria-label="{{ $t('فيديو تعريفي', 'Intro video') }}">
                        <div class="pg-video"><video src="{{ $video['direct'] }}" controls preload="metadata" playsinline></video></div>
                    </section>
                @endif

                @if($facts !== [])
                    <section class="dip-block" aria-labelledby="pg-facts">
                        <h2 id="pg-facts">{{ $t('نظرة سريعة', 'At a glance') }}</h2>
                        <dl class="dip-facts">
                            @foreach($facts as $f)
                                <div><i class="fas {{ $f['icon'] }}" aria-hidden="true"></i><span><dt>{{ $f['label'] }}</dt><dd>{{ $f['value'] }}</dd></span></div>
                            @endforeach
                        </dl>
                    </section>
                @endif

                @if(filled($learningPath->description) && trim(strip_tags((string) $learningPath->description)) !== trim(strip_tags((string) $summary)))
                    <section class="dip-block" aria-labelledby="pg-about">
                        <h2 id="pg-about">{{ $t('عن البرنامج', 'About the program') }}</h2>
                        <p class="dip-details">{{ $learningPath->description }}</p>
                    </section>
                @endif

                @if($highlights !== [])
                    <section class="dip-block" aria-labelledby="pg-highlights">
                        <h2 id="pg-highlights">{{ $t('هتتعلّم وهتاخد إيه', 'What you get') }}</h2>
                        <ul class="dip-highlights">@foreach($highlights as $item)<li>{{ $item }}</li>@endforeach</ul>
                    </section>
                @endif

                @if($courses->isNotEmpty())
                    <section class="dip-block" aria-labelledby="pg-courses">
                        <h2 id="pg-courses">{{ $t('محتوى البرنامج', 'Curriculum') }}</h2>
                        <div class="pg-curriculum">
                            @foreach($courses as $i => $course)
                                <a href="{{ route('public.course.show', $course->id) }}">
                                    <span class="pg-num">{{ $loop->iteration }}</span>
                                    <span><b>{{ $course->title }}</b>
                                        <small>{{ collect([$course->academicSubject?->name, ($course->lessons_count ?? 0) > 0 ? $course->lessons_count.' '.$t('درس', 'lessons') : null])->filter()->implode(' · ') }}</small></span>
                                </a>
                            @endforeach
                        </div>
                    </section>
                @endif

                @if(filled($board?->landing_details))
                    <section class="dip-block" aria-labelledby="pg-more">
                        <h2 id="pg-more">{{ $t('معلومات إضافية', 'More details') }}</h2>
                        <p class="dip-details">{{ $board->landing_details }}</p>
                    </section>
                @endif

                @if(($reviewsCount ?? 0) > 0 || ($isEnrolled ?? false))
                    <section class="dip-block" aria-labelledby="pg-reviews">
                        <h2 id="pg-reviews">{{ $t('آراء الطلاب', 'Student reviews') }} @if(($reviewsCount ?? 0) > 0)<span class="pg-stars" aria-label="{{ number_format($reviewsAvg, 1) }} / 5">{{ str_repeat('★', (int) round($reviewsAvg)) }}</span> <small style="font-size: 15px; color: var(--h-muted)">({{ $reviewsCount }})</small>@endif</h2>
                        @if($errors->any())<div class="pg-note is-bad" style="margin-bottom: 16px">{{ $errors->first() }}</div>@endif
                        @if($isEnrolled ?? false)
                            <form action="{{ route('public.learning-path.reviews.store', $slug) }}" method="POST" class="h-boxform" style="margin-bottom: 20px">
                                @csrf
                                <label class="h-field">{{ $t('تقييمك', 'Your rating') }}
                                    <select name="rating" required><option value="">{{ $t('اختار', 'Choose') }}</option>@for($i = 5; $i >= 1; $i--)<option value="{{ $i }}" @selected((string) old('rating') === (string) $i)>{{ str_repeat('★', $i) }} ({{ $i }}/5)</option>@endfor</select>
                                </label>
                                <label class="h-field">{{ $t('رأيك في البرنامج', 'Your review') }}<textarea name="comment" rows="3" required>{{ old('comment') }}</textarea></label>
                                <button type="submit" class="h-btn h-btn-primary">{{ $t('نشر التقييم', 'Post review') }}</button>
                            </form>
                        @endif
                        @foreach($approvedReviews as $r)
                            <article class="pg-review">
                                <header><span>{{ $r->user->name ?? $t('طالب', 'Student') }}</span><span class="pg-stars" aria-label="{{ (int) $r->rating }} / 5">{{ str_repeat('★', (int) $r->rating) }}</span></header>
                                <p>{{ $r->comment }}</p>
                            </article>
                        @endforeach
                    </section>
                @endif
            </div>

            <aside class="h-side dip-side" aria-label="{{ $t('الرسوم والتسجيل', 'Fees and enrolment') }}">
                <div class="dip-card">
                    <div class="dip-price">
                        <small>{{ $t('رسوم البرنامج', 'Program fee') }}</small>
                        @if($price > 0)
                            <b>{{ number_format($price, 0) }} <span>{{ $t('ج.م', 'EGP') }}</span></b>
                            @if($board?->price_recorded && (float) $board->price_recorded > 0)<p>{{ $t('النسخة المسجّلة: ', 'Recorded edition: ') }}{{ number_format((float) $board->price_recorded, 0) }} {{ $t('ج.م', 'EGP') }}</p>@endif
                        @else
                            <b>{{ $t('مجاني', 'Free') }}</b>
                        @endif
                    </div>
                    <div class="dip-card-body">
                        @if($start)<div class="dip-start"><span>{{ $t('أقرب دفعة', 'Next intake') }}</span><b>{{ $start }}</b></div>@endif
                        @if($isEnrolled ?? false)
                            <a href="{{ route('student.learning-path.show', $slug) }}" class="h-btn h-btn-primary h-btn-lg h-btn-block">{{ $t('ادخل على البرنامج', 'Go to the program') }}</a>
                        @elseif($pending)
                            <div class="pg-note">{{ $t('طلب اشتراكك قيد المراجعة، وهنكلمك قريب.', 'Your enrolment request is under review.') }}</div>
                        @elseif($price > 0)
                            <a href="{{ route('public.learning-path.checkout', $slug) }}" class="h-btn h-btn-primary h-btn-lg h-btn-block">{{ $t('سجّل وادفع أونلاين', 'Enrol and pay online') }}</a>
                        @else
                            @auth
                                <form action="{{ route('public.learning-path.enroll.free', $slug) }}" method="POST">@csrf<button type="submit" class="h-btn h-btn-primary h-btn-lg h-btn-block">{{ $t('اشترك مجاناً', 'Enrol for free') }}</button></form>
                            @else
                                <a href="{{ route('register') }}" class="h-btn h-btn-primary h-btn-lg h-btn-block">{{ $t('اعمل حساب واشترك', 'Sign up to enrol') }}</a>
                            @endauth
                        @endif
                        <a href="{{ $consultUrl }}" data-program="{{ $learningPath->name }}" class="h-btn h-btn-outline h-btn-block">{{ $t('احجز استشارة قبل ما تقرر', 'Book a consultation first') }}</a>
                        <a href="{{ $contactUrl }}" class="h-link">{{ $t('اسأل فريق القبول', 'Ask admissions') }}</a>
                    </div>
                    @if($board && $board->bookingMethodList() !== [])
                        <div class="dip-pay" style="border-top: 1px solid var(--h-line)">
                            <h3>{{ $t('طرق الدفع الأخرى', 'Other ways to pay') }}</h3>
                            <ul>@foreach($board->bookingMethodList() as $m)<li><i class="fas fa-wallet" aria-hidden="true"></i><span>{{ $m }}</span></li>@endforeach</ul>
                        </div>
                    @endif
                </div>
            </aside>
        </div>

        @if(isset($relatedPaths) && $relatedPaths->isNotEmpty())
            <section class="h-section h-section-mist" style="margin-top: 72px; padding: 64px 0">
                <div class="h-wrap">
                    <h2 class="h-h2" style="margin-bottom: 24px">{{ $t('برامج تانية ممكن تناسبك', 'Other programs') }}</h2>
                    <div class="pg-grid">
                        @foreach($relatedPaths as $p)
                            <a href="{{ route('public.learning-path.show', $p->slug) }}" class="pg-tile">
                                <div class="pg-tile-body">
                                    <h3>{{ $p->name }}</h3>
                                    @if(filled($p->description))<p>{{ Str::limit(strip_tags((string) $p->description), 120) }}</p>@endif
                                    <div class="pg-tile-foot"><b>{{ $p->price > 0 ? number_format($p->price, 0).' '.$t('ج.م', 'EGP') : $t('مجاني', 'Free') }}</b><span class="h-link">{{ $t('التفاصيل', 'Details') }}</span></div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif
    </main>

    <div class="dip-mobile-bar">
        <div><small>{{ $t('الرسوم', 'Fee') }}</small><b>{{ $price > 0 ? number_format($price, 0).' '.$t('ج.م', 'EGP') : $t('مجاني', 'Free') }}</b></div>
        @if($isEnrolled ?? false)
            <a href="{{ route('student.learning-path.show', $slug) }}" class="h-btn h-btn-primary">{{ $t('ادخل على البرنامج', 'Go to the program') }}</a>
        @elseif($price > 0 && ! $pending)
            <a href="{{ route('public.learning-path.checkout', $slug) }}" class="h-btn h-btn-primary">{{ $t('سجّل الآن', 'Enrol now') }}</a>
        @else
            <a href="{{ $consultUrl }}" data-program="{{ $learningPath->name }}" class="h-btn h-btn-primary">{{ $t('احجز استشارة', 'Book a consultation') }}</a>
        @endif
    </div>

    @include('components.site.footer')
</body>
</html>
