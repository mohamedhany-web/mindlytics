{{-- البرامج التدريبية (AcademicYears) على هوية الموقع. البداية والمواعيد من لوحة الدبلومات المربوطة بكل برنامج ($boards). --}}
@php
    use Illuminate\Support\Str;

    $siteAr = app()->getLocale() === 'ar';
    $t = fn (string $ar, string $en) => $siteAr ? $ar : $en;
    $clean = fn ($v) => filled($v) && trim((string) $v) !== '—' ? trim((string) $v) : null;
    $boards = $boards ?? collect();
@endphp
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ $siteAr ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $t('البرامج التدريبية', 'Training programs') }} — Mindlytics Academy</title>
    <meta name="description" content="{{ $t('برامج تدريبية طويلة بدفعات ومواعيد ثابتة ومحاضرين من سوق العمل، في كل مجالات البرمجيات.', 'Long-form training programs with fixed intakes and industry instructors across software fields.') }}">
    <x-tracking-tags placement="head" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    @include('components.site.styles')
    @include('components.site.program-styles')
</head>
<body class="h-site h-has-mobile-bar">
    <x-tracking-tags placement="body" />
    @include('components.site.header')

    @include('components.site.page-head', [
        'eyebrow' => $t('برامج طويلة بدفعات', 'Cohort programs'),
        'title' => $t('البرامج التدريبية', 'Training programs'),
        'subtitle' => $t('برامج من أول الأساسيات لحد الشغل: دفعات بمواعيد ثابتة، محاضر من سوق العمل، مشاريع حقيقية، ومتابعة لحد ما تبدأ.', 'From the basics to the job: fixed intakes, industry instructors, real projects and follow-up until you start.'),
        'crumbs' => [['label' => $t('البرامج التدريبية', 'Programs')]],
    ])

    <main class="dip-body">
        <div class="h-wrap">
            @if($learningPaths->isEmpty())
                <div class="h-empty">
                    <div><b>{{ $t('بنجهّز البرامج الجاية', 'New programs are on the way') }}</b><p style="margin: 4px 0 0; color: var(--h-text-2)">{{ $t('احجز استشارة ونقولك أنسب بداية ليك دلوقتي.', 'Book a consultation and we will suggest the best start for you.') }}</p></div>
                    <a href="{{ route('home') }}#consult" class="h-btn h-btn-primary">{{ $t('احجز استشارة مجانية', 'Book a free consultation') }}</a>
                </div>
            @else
                <div class="pg-list">
                    @foreach($learningPaths as $path)
                        @php
                            $board = $boards->get($path->id);
                            $start = $board?->starts_at ? $board->starts_at->locale(app()->getLocale())->translatedFormat('j F Y') : ($board ? $clean($board->startDisplay()) : null);
                            $summary = filled($board?->summary) ? $board->summary : $path->description;
                            $meta = array_values(array_filter([
                                $start ? ['fa-calendar-day', $t('تبدأ ', 'Starts ').$start] : null,
                                $clean($board?->duration) ? ['fa-hourglass-half', $clean($board->duration)] : null,
                                $clean($board?->format) ? ['fa-laptop-house', $clean($board->format)] : null,
                                $clean($board?->schedule_days) ? ['fa-calendar-week', $clean($board->schedule_days)] : null,
                                ($path->courses_count ?? 0) > 0 ? ['fa-layer-group', $path->courses_count.' '.$t('كورس', 'courses')] : null,
                            ]));
                            $url = route('public.learning-path.show', $path->slug);
                        @endphp
                        <article class="pg-card">
                            <div class="pg-card-main">
                                <span class="h-eyebrow" style="margin: 0">{{ $t('برنامج تدريبي', 'Training program') }}</span>
                                <h2><a href="{{ $url }}">{{ $path->name }}</a></h2>
                                @if(filled($summary))<p>{{ Str::limit(strip_tags((string) $summary), 260) }}</p>@endif
                                @if($meta !== [])
                                    <ul class="pg-meta">@foreach($meta as [$icon, $label])<li><i class="fas {{ $icon }}" aria-hidden="true"></i>{{ $label }}</li>@endforeach</ul>
                                @endif
                            </div>
                            <div class="pg-card-side">
                                <div>
                                    <small>{{ $t('رسوم البرنامج', 'Program fee') }}</small>
                                    <b>@if($path->price > 0){{ number_format($path->price, 0) }} <span>{{ $t('ج.م', 'EGP') }}</span>@else{{ $t('مجاني', 'Free') }}@endif</b>
                                </div>
                                <a href="{{ $url }}" class="h-btn h-btn-primary">{{ $t('تفاصيل البرنامج', 'Program details') }}</a>
                                <a href="{{ route('home') }}#consult" data-program="{{ $path->name }}" class="h-link">{{ $t('احجز استشارة', 'Book a consultation') }}</a>
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif

            <div class="pg-help" style="margin-top: 48px">
                <div>
                    <h2>{{ $t('مش متأكد أنهي برنامج يناسبك؟', 'Not sure which program fits?') }}</h2>
                    <p>{{ $t('جلسة مجانية مع مرشد من الأكاديمية: نفهم وضعك وهدفك، ونرشّحلك البرنامج والدفعة المناسبين.', 'A free session with an academy advisor to match you with the right program and intake.') }}</p>
                </div>
                <a href="{{ route('advising.show') }}" data-advising-open class="h-btn h-btn-accent h-btn-lg">{{ $t('احجز استشارة مجانية', 'Book a free consultation') }}</a>
            </div>

            <p style="margin: 32px 0 0; color: var(--h-text-2)">{{ $t('عايز تتعلم أداة أو مهارة واحدة بس؟', 'Want a single skill instead?') }} <a href="{{ route('public.courses') }}" class="h-link">{{ $t('شوف الكورسات القصيرة ←', 'See short courses →') }}</a></p>
        </div>
    </main>

    @include('components.site.footer')
</body>
</html>
