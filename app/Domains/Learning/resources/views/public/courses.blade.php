{{--
    الكورسات على هوية الموقع:
    1) كورسات قصيرة بدفعات جاية (لوحة الكورسات في المبيعات، $intakes)
    2) الكورسات الأونلاين المنشورة (AdvancedCourse، $courses paginator) بالبحث والفلترة
    3) الباقات
--}}
@php
    use Illuminate\Support\Str;

    $siteAr = app()->getLocale() === 'ar';
    $t = fn (string $ar, string $en) => $siteAr ? $ar : $en;
    $clean = fn ($v) => filled($v) && trim((string) $v) !== '—' ? trim((string) $v) : null;
    $intakes = $intakes ?? collect();
    $levels = ['beginner' => $t('مبتدئ', 'Beginner'), 'intermediate' => $t('متوسط', 'Intermediate'), 'advanced' => $t('متقدم', 'Advanced')];
    $filtering = filled($filters['q'] ?? null) || filled($filters['level'] ?? null) || filled($filters['subject'] ?? null);
    $money = fn (float $v) => $v > 0 ? number_format($v, 0).' '.$t('ج.م', 'EGP') : $t('مجاني', 'Free');
@endphp
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ $siteAr ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $t('الكورسات', 'Courses') }} — Mindlytics Academy</title>
    <meta name="description" content="{{ $t('كورسات قصيرة في أدوات ومهارات البرمجيات، بدفعات مباشرة أو أونلاين على المنصة.', 'Short courses in software tools and skills, live cohorts or online on the platform.') }}">
    <x-tracking-tags placement="head" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    @include('components.site.styles')
    @include('components.site.program-styles')
    <style>
        .cr-filters { display: flex; flex-wrap: wrap; gap: 12px; align-items: flex-end; margin-bottom: 24px; }
        .cr-filters .h-field { flex: 1 1 220px; }
        .cr-filters .h-field:first-child { flex: 2 1 280px; }
        .cr-pages nav { margin-top: 24px; }
        .cr-section + .cr-section { margin-top: 72px; }
        .cr-section-head { margin-bottom: 24px; }
        .cr-section-head p { margin: 6px 0 0; color: var(--h-text-2); }
    </style>
</head>
<body class="h-site h-has-mobile-bar">
    <x-tracking-tags placement="body" />
    @include('components.site.header')

    @include('components.site.page-head', [
        'eyebrow' => $t('مهارة واحدة في وقت قصير', 'One skill, short time'),
        'title' => $t('الكورسات', 'Courses'),
        'subtitle' => $t('كورسات قصيرة في أداة أو مهارة محددة: دفعات مباشرة مع محاضر، أو أونلاين على المنصة تذاكرها في وقتك.', 'Short courses on one tool or skill: live cohorts with an instructor, or online on the platform at your pace.'),
        'crumbs' => [['label' => $t('الكورسات', 'Courses')]],
    ])

    <main class="dip-body">
        <div class="h-wrap">
            @if($intakes->isNotEmpty())
                <section class="cr-section" aria-labelledby="cr-intakes">
                    <div class="cr-section-head">
                        <h2 id="cr-intakes" class="h-h2">{{ $t('كورسات بدفعات جاية', 'Upcoming course intakes') }}</h2>
                        <p>{{ $t('محاضرات مباشرة بمواعيد ثابتة مع محاضر، والأقرب ميعاداً الأول.', 'Live sessions on a fixed schedule, nearest first.') }}</p>
                    </div>
                    <div class="pg-list">
                        @foreach($intakes as $e)
                            @php
                                $start = $e->starts_at ? $e->starts_at->locale(app()->getLocale())->translatedFormat('j F Y') : $clean($e->startDisplay());
                                $price = $e->publicPrice();
                                $url = $e->detailsUrl();
                                $plan = $e->installmentPlan();
                                $meta = array_values(array_filter([
                                    $start ? ['fa-calendar-day', $t('تبدأ ', 'Starts ').$start] : null,
                                    $clean($e->duration) ? ['fa-hourglass-half', $clean($e->duration)] : null,
                                    $clean($e->schedule_days) ? ['fa-calendar-week', $clean($e->schedule_days)] : null,
                                    $clean($e->format) ? ['fa-laptop-house', $clean($e->format)] : null,
                                    $clean($e->instructor_name) ? ['fa-chalkboard-user', $clean($e->instructor_name)] : null,
                                ]));
                            @endphp
                            <article class="pg-card">
                                <div class="pg-card-main">
                                    <span class="h-eyebrow" style="margin: 0">{{ $t('كورس بدفعة مباشرة', 'Live cohort course') }}</span>
                                    <h3>@if($url)<a href="{{ $url }}">{{ $e->name }}</a>@else{{ $e->name }}@endif</h3>
                                    @if(filled($e->summary))<p>{{ Str::limit(strip_tags((string) $e->summary), 220) }}</p>@endif
                                    @if($meta !== [])<ul class="pg-meta">@foreach($meta as [$icon, $label])<li><i class="fas {{ $icon }}" aria-hidden="true"></i>{{ $label }}</li>@endforeach</ul>@endif
                                </div>
                                <div class="pg-card-side">
                                    @if($price !== null)
                                        <div>
                                            <small>{{ $t('الرسوم', 'Fee') }}</small>
                                            <b>{{ $money((float) $price) }}</b>
                                            @if($plan)<small style="display: block">{{ $t('متاح بالتقسيط', 'Instalments available') }}</small>@endif
                                        </div>
                                    @endif
                                    <a href="{{ route('home') }}#consult" data-program="{{ $e->name }}" class="h-btn h-btn-primary">{{ $t('احجز مقعدك', 'Reserve your seat') }}</a>
                                    @if($url)<a href="{{ $url }}" class="h-link">{{ $t('التفاصيل', 'Details') }}</a>@endif
                                </div>
                            </article>
                        @endforeach
                    </div>
                </section>
            @endif

            @if($totalPublic > 0)
                <section class="cr-section" aria-labelledby="cr-online" id="online">
                    <div class="cr-section-head">
                        <h2 id="cr-online" class="h-h2">{{ $t('كورسات أونلاين على المنصة', 'Online courses') }}</h2>
                        <p>{{ $t('محتوى مسجّل تذاكره في وقتك، بمهام واختبارات ومتابعة على المنصة.', 'Recorded content at your pace with tasks and quizzes on the platform.') }}</p>
                    </div>
                    <form method="get" action="{{ route('public.courses') }}#online" class="cr-filters" role="search">
                        <label class="h-field">{{ $t('ابحث', 'Search') }}<input type="search" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="{{ $t('اسم الكورس أو الأداة', 'Course or tool name') }}"></label>
                        <label class="h-field">{{ $t('المستوى', 'Level') }}
                            <select name="level"><option value="">{{ $t('كل المستويات', 'All levels') }}</option>@foreach($levels as $k => $l)<option value="{{ $k }}" @selected(($filters['level'] ?? '') === $k)>{{ $l }}</option>@endforeach</select>
                        </label>
                        @if($subjects->isNotEmpty())
                            <label class="h-field">{{ $t('المجال', 'Field') }}
                                <select name="subject"><option value="">{{ $t('كل المجالات', 'All fields') }}</option>@foreach($subjects as $s)<option value="{{ $s->id }}" @selected((string) ($filters['subject'] ?? '') === (string) $s->id)>{{ $s->name }}</option>@endforeach</select>
                            </label>
                        @endif
                        <button type="submit" class="h-btn h-btn-dark">{{ $t('عرض', 'Apply') }}</button>
                        @if($filtering)<a href="{{ route('public.courses') }}#online" class="h-link" style="align-self: center">{{ $t('مسح الفلاتر', 'Clear') }}</a>@endif
                    </form>

                    @if($courses->count() > 0)
                        <div class="pg-grid">
                            @foreach($courses as $i => $course)
                                @php
                                    $price = $course->effectivePrice();
                                    $thumb = filled($course->thumbnail) ? asset('storage/'.str_replace('\\', '/', $course->thumbnail)) : null;
                                @endphp
                                <a href="{{ route('public.course.show', $course->id) }}" class="pg-tile"
                                   data-ml-select-item data-ml-item-id="course:{{ $course->id }}" data-ml-item-name="{{ $course->title }}" data-ml-item-price="{{ $price }}" data-ml-item-index="{{ $i }}">
                                    <div class="pg-tile-img">@if($thumb)<img src="{{ $thumb }}" alt="" loading="lazy">@else<span aria-hidden="true">{{ Str::upper(Str::substr($course->title, 0, 1)) }}</span>@endif</div>
                                    <div class="pg-tile-body">
                                        <ul class="pg-meta">
                                            @if($course->level && isset($levels[$course->level]))<li>{{ $levels[$course->level] }}</li>@endif
                                            @if($course->academicSubject)<li>{{ $course->academicSubject->name }}</li>@endif
                                            @if(($course->lessons_count ?? 0) > 0)<li>{{ $course->lessons_count }} {{ $t('درس', 'lessons') }}</li>@endif
                                        </ul>
                                        <h3>{{ $course->title }}</h3>
                                        @if(filled($course->description))<p>{{ Str::limit(strip_tags((string) $course->description), 110) }}</p>@endif
                                        <div class="pg-tile-foot"><b>{{ $money($price) }}</b><span class="h-link">{{ $t('التفاصيل', 'Details') }}</span></div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                        <div class="cr-pages">{{ $courses->fragment('online')->links() }}</div>
                    @else
                        <div class="h-empty"><span>{{ $t('مفيش كورسات بالفلاتر دي.', 'No courses match these filters.') }}</span><a href="{{ route('public.courses') }}#online" class="h-btn h-btn-outline">{{ $t('اعرض الكل', 'Show all') }}</a></div>
                    @endif
                </section>
            @endif

            @if($packages->isNotEmpty())
                <section class="cr-section" aria-labelledby="cr-packages">
                    <div class="cr-section-head">
                        <h2 id="cr-packages" class="h-h2">{{ $t('باقات بسعر أوفر', 'Course bundles') }}</h2>
                        <p>{{ $t('أكتر من كورس مع بعض بسعر أقل من مجموعهم.', 'Several courses together for less.') }}</p>
                    </div>
                    <div class="pg-grid">
                        @foreach($packages as $pkg)
                            <a href="{{ route('public.package.show', $pkg->slug ?? $pkg->id) }}" class="pg-tile">
                                <div class="pg-tile-body">
                                    <ul class="pg-meta"><li>{{ $pkg->courses_count }} {{ $t('كورس', 'courses') }}</li></ul>
                                    <h3>{{ $pkg->name }}</h3>
                                    @if(filled($pkg->description))<p>{{ Str::limit(strip_tags((string) $pkg->description), 110) }}</p>@endif
                                    <div class="pg-tile-foot"><b>{{ $money((float) ($pkg->price ?? 0)) }}</b><span class="h-link">{{ $t('التفاصيل', 'Details') }}</span></div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </section>
            @endif

            @if($intakes->isEmpty() && $totalPublic === 0 && $packages->isEmpty())
                <div class="h-empty">
                    <div><b>{{ $t('الكورسات الجاية بتتجهّز', 'New courses are on the way') }}</b><p style="margin: 4px 0 0; color: var(--h-text-2)">{{ $t('شوف البرامج التدريبية المتاحة دلوقتي، أو احجز استشارة ونرشّحلك بداية.', 'See the training programs available now, or book a consultation.') }}</p></div>
                    <a href="{{ route('public.learning-paths.index') }}" class="h-btn h-btn-primary">{{ $t('البرامج التدريبية', 'Training programs') }}</a>
                </div>
            @endif

            <div class="pg-help" style="margin-top: 56px">
                <div>
                    <h2>{{ $t('عايز طريق كامل لحد الشغل؟', 'Want a full path to a job?') }}</h2>
                    <p>{{ $t('البرامج التدريبية بتجمع الأدوات اللي محتاجها في مجال واحد، بمشاريع ومتابعة لحد ما تبدأ.', 'Training programs bundle the tools of one field with projects and follow-up.') }}</p>
                </div>
                <a href="{{ route('public.learning-paths.index') }}" class="h-btn h-btn-accent h-btn-lg">{{ $t('شوف البرامج التدريبية', 'See training programs') }}</a>
            </div>
        </div>
    </main>

    @include('components.site.footer')

    @php
        $__mlAnalytics = app(\App\Domains\Marketing\Services\MarketingAnalyticsService::class);
        $__mlListItems = [];
        foreach ($courses as $__mlIndex => $__mlCourse) {
            $__mlListItems[] = $__mlAnalytics->itemFromCourseArray(['id' => $__mlCourse->id, 'title' => $__mlCourse->title, 'price' => $__mlCourse->effectivePrice(), 'academic_subject' => ['name' => $__mlCourse->academicSubject?->name]], (int) $__mlIndex);
        }
        $__mlViewItemList = $__mlListItems !== [] ? $__mlAnalytics->viewItemList($__mlListItems, 'courses', 'Courses') : null;
    @endphp
    <x-ecommerce-datalayer :payload="$__mlViewItemList" />
    <script>
    document.addEventListener('click', function (e) {
        var link = e.target.closest('[data-ml-select-item]');
        if (! link || typeof window.mindlyticsPushEcommerce !== 'function') { return; }
        window.mindlyticsPushEcommerce({
            event: 'select_item',
            ecommerce: {
                currency: @json(\App\Domains\Marketing\Support\MarketingWebAnalyticsSettings::currency()),
                item_list_id: 'courses', item_list_name: 'Courses',
                items: [{
                    item_id: link.getAttribute('data-ml-item-id'), item_name: link.getAttribute('data-ml-item-name') || '',
                    price: parseFloat(link.getAttribute('data-ml-item-price') || '0') || 0, quantity: 1,
                    index: parseInt(link.getAttribute('data-ml-item-index') || '0', 10) || 0,
                    item_brand: @json(\App\Domains\Marketing\Support\MarketingWebAnalyticsSettings::itemBrand()),
                    item_category: 'online_course', item_list_id: 'courses', item_list_name: 'Courses'
                }]
            }
        });
    }, true);
    </script>
</body>
</html>
