@php
    use Illuminate\Support\Str;

    $clean = fn ($v) => filled($v) && trim((string) $v) !== '—' ? trim((string) $v) : null;
    $start = $entry->starts_at ? $entry->starts_at->locale('ar')->translatedFormat('j F Y') : $clean($entry->startDisplay());
    $price = $entry->publicPrice();
    $recorded = $entry->price_recorded !== null && (float) $entry->price_recorded > 0 ? (float) $entry->price_recorded : null;
    $year = $entry->academicYear;
    $pathUrl = $year && $year->is_active ? route('public.learning-path.show', Str::slug($year->name)) : null;
    $contactUrl = route('public.contact').'?subject='.urlencode('استفسار عن دبلومة: '.$entry->name);

    $facts = array_filter([
        ['icon' => 'fa-calendar-day', 'label' => 'البداية', 'value' => $start],
        ['icon' => 'fa-hourglass-half', 'label' => 'المدة', 'value' => $clean($entry->duration)],
        ['icon' => 'fa-calendar-week', 'label' => 'أيام المحاضرات', 'value' => $clean($entry->schedule_days)],
        ['icon' => 'fa-clock', 'label' => 'ساعات المحاضرة', 'value' => $clean($entry->hours)],
        ['icon' => 'fa-laptop-house', 'label' => 'نظام الدراسة', 'value' => $clean($entry->format)],
        ['icon' => 'fa-users', 'label' => 'مناسبة لـ', 'value' => $clean($entry->audience)],
        ['icon' => 'fa-chalkboard-user', 'label' => 'المحاضر', 'value' => $clean($entry->instructor_name)],
    ], fn ($f) => $f['value'] !== null);

    $highlights = $entry->landingHighlights();
    $bookingMethods = $entry->bookingMethodList();
    $lectures = collect($entry->freeLectureLinkList())->values()->map(function ($l, $i) {
        $url = html_entity_decode($l['url'], ENT_QUOTES | ENT_HTML5);
        $title = html_entity_decode($l['title'], ENT_QUOTES | ENT_HTML5);
        $isLink = $title === '' || $title === $url || Str::startsWith($title, ['http://', 'https://']);

        return ['url' => $url, 'title' => $isLink ? 'محاضرة مجانية '.($i + 1) : $title, 'host' => parse_url($url, PHP_URL_HOST)];
    });
    $chips = array_values(array_filter([
        $start ? ['label' => 'تبدأ '.$start, 'icon' => 'fas fa-calendar-day'] : null,
        $clean($entry->duration) ? ['label' => $clean($entry->duration), 'icon' => 'fas fa-hourglass-half'] : null,
        $clean($entry->format) ? ['label' => $clean($entry->format), 'icon' => 'fas fa-laptop-house'] : null,
    ]));
    $siteHideMobileBar = true;
@endphp
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $entry->name }} — Mindlytics Academy</title>
    <meta name="description" content="{{ Str::limit(strip_tags($entry->summary ?? $entry->name), 160) }}">
    <meta property="og:title" content="{{ $entry->name }} — Mindlytics Academy">
    <meta property="og:description" content="{{ Str::limit(strip_tags($entry->summary ?? ''), 200) }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    {{-- هوية الموقع الموحّدة (docs/SITE_DESIGN.md) --}}
    @include('components.site.styles')
    <style>
        .dip-body { padding: 56px 0 88px; }
        .dip-block + .dip-block { margin-top: 48px; }
        .dip-block > h2 { margin: 0 0 18px; font-family: var(--h-display); font-size: 24px; line-height: 1.5; }
        .dip-facts { display: grid; grid-template-columns: repeat(auto-fill, minmax(min(100%, 230px), 1fr)); margin: 0; border-top: 2px solid var(--h-ink); }
        .dip-facts div { display: flex; gap: 14px; align-items: flex-start; padding: 18px 0; border-bottom: 1px solid var(--h-line); padding-inline-end: 16px; }
        .dip-facts i { width: 40px; height: 40px; flex: none; display: flex; align-items: center; justify-content: center; background: var(--h-mist); color: var(--h-brand); }
        .dip-facts dt { font-size: 13px; color: var(--h-muted); }
        .dip-facts dd { margin: 2px 0 0; font-weight: 700; overflow-wrap: anywhere; unicode-bidi: plaintext; text-align: right; }
        .dip-highlights { list-style: none; margin: 0; padding: 0; display: grid; grid-template-columns: repeat(auto-fill, minmax(min(100%, 300px), 1fr)); gap: 12px; }
        .dip-highlights li { display: flex; gap: 12px; align-items: flex-start; padding: 16px 18px; border: 1px solid var(--h-line); background: #FFFFFF; line-height: 1.65; }
        .dip-highlights li::before { content: ''; flex: none; width: 8px; height: 8px; margin-top: 9px; background: var(--h-accent); }
        .dip-lectures { display: flex; flex-direction: column; gap: 10px; }
        .dip-lecture { display: flex; align-items: center; gap: 14px; padding: 14px 18px; border: 1px solid var(--h-line); color: var(--h-ink); text-decoration: none; }
        .dip-lecture:hover { border-color: var(--h-brand); }
        .dip-lecture .dip-play { width: 44px; height: 44px; flex: none; display: flex; align-items: center; justify-content: center; background: var(--h-ink); color: var(--h-accent); }
        .dip-lecture b { display: block; }
        .dip-lecture small { color: var(--h-muted); font-size: 13px; }
        .dip-lecture .fa-arrow-up-right-from-square { margin-inline-start: auto; color: var(--h-muted); }
        .dip-details { color: var(--h-text-2); white-space: pre-line; margin: 0; }
        .dip-side { position: sticky; top: 100px; display: flex; flex-direction: column; gap: 16px; }
        .dip-card { border: 1px solid #D5D2E0; background: #FFFFFF; }
        .dip-price { background: var(--h-ink); color: #FFFFFF; padding: 24px 26px; background-image: radial-gradient(60% 80% at 0% 0%, rgba(126, 104, 214, .38), rgba(126, 104, 214, 0) 72%), radial-gradient(50% 70% at 100% 100%, rgba(242, 182, 48, .2), rgba(242, 182, 48, 0) 70%); }
        .dip-price small { display: block; font-size: 13px; color: var(--h-on-dark-2); }
        .dip-price b { display: block; font-family: var(--h-display); font-size: 34px; line-height: 1.4; }
        .dip-price b span { font-size: 16px; font-weight: 600; color: var(--h-on-dark); }
        .dip-price p { margin: 6px 0 0; font-size: 14px; color: var(--h-on-dark); }
        .dip-card-body { padding: 22px 26px; display: flex; flex-direction: column; gap: 12px; }
        .dip-card-body .h-link { text-align: center; font-size: 15px; }
        .dip-start { display: flex; justify-content: space-between; gap: 12px; font-size: 14px; color: var(--h-text-2); padding-bottom: 12px; border-bottom: 1px solid var(--h-line); }
        .dip-start b { color: var(--h-ink); }
        .dip-pay { padding: 20px 26px; }
        .dip-pay h3 { margin: 0 0 10px; font-size: 16px; }
        .dip-pay ul { list-style: none; margin: 0; padding: 0; display: flex; flex-direction: column; gap: 8px; font-size: 15px; }
        .dip-pay li { display: flex; gap: 10px; align-items: baseline; overflow-wrap: anywhere; }
        .dip-pay li i { color: var(--h-brand); }
        .dip-mobile-bar { display: none; }
        @media (max-width: 960px) {
            .dip-body { padding: 32px 0 48px; }
            .dip-side { position: static; }
            .dip-block + .dip-block { margin-top: 36px; }
            .dip-block > h2 { font-size: 21px; }
            .dip-facts { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .dip-facts div { flex-direction: column; gap: 8px; padding: 14px 0; padding-inline-end: 12px; }
            .dip-facts i { width: 32px; height: 32px; font-size: 13px; }
            .dip-facts dd { font-size: 15px; }
            .dip-mobile-bar { display: flex; align-items: center; gap: 12px; position: fixed; inset-inline: 0; bottom: 0; z-index: 50; padding: 10px 16px calc(10px + env(safe-area-inset-bottom)); background: #FFFFFF; border-top: 1px solid #D5D2E0; }
            .dip-mobile-bar div { display: flex; flex-direction: column; line-height: 1.3; }
            .dip-mobile-bar small { font-size: 12px; color: var(--h-muted); }
            .dip-mobile-bar b { font-family: var(--h-display); font-size: 18px; }
            .dip-mobile-bar .h-btn { flex: 1; }
            body.h-has-mobile-bar { padding-bottom: 76px; }
        }
    </style>
</head>
<body class="h-site h-has-mobile-bar">
    @include('components.site.header')

    @include('components.site.page-head', [
        'eyebrow' => 'دبلومة',
        'title' => $entry->name,
        'subtitle' => filled($entry->summary) ? $entry->summary : null,
        'crumbs' => [['label' => 'الدفعات القادمة', 'url' => route('home').'#intakes'], ['label' => $entry->name]],
        'chips' => $chips,
    ])

    <main class="dip-body">
        <div class="h-wrap h-split">
            <div class="h-grow">
                @if($facts !== [])
                    <section class="dip-block" aria-labelledby="dip-facts">
                        <h2 id="dip-facts">نظرة سريعة</h2>
                        <dl class="dip-facts">
                            @foreach($facts as $f)
                                <div><i class="fas {{ $f['icon'] }}" aria-hidden="true"></i><span><dt>{{ $f['label'] }}</dt><dd>{{ $f['value'] }}</dd></span></div>
                            @endforeach
                        </dl>
                    </section>
                @endif

                @if($highlights !== [])
                    <section class="dip-block" aria-labelledby="dip-highlights">
                        <h2 id="dip-highlights">هتتعلّم وهتاخد إيه</h2>
                        <ul class="dip-highlights">
                            @foreach($highlights as $item)<li>{{ $item }}</li>@endforeach
                        </ul>
                    </section>
                @endif

                @if($lectures->isNotEmpty())
                    <section class="dip-block" aria-labelledby="dip-lectures">
                        <h2 id="dip-lectures">جرّب قبل ما تحجز</h2>
                        <div class="dip-lectures">
                            @foreach($lectures as $l)
                                <a href="{{ $l['url'] }}" target="_blank" rel="noopener noreferrer" class="dip-lecture">
                                    <span class="dip-play"><i class="fas fa-play" aria-hidden="true"></i></span>
                                    <span><b>{{ $l['title'] }}</b>@if($l['host'])<small>{{ Str::after($l['host'], 'www.') }}</small>@endif</span>
                                    <i class="fas fa-arrow-up-right-from-square" aria-hidden="true"></i>
                                </a>
                            @endforeach
                        </div>
                    </section>
                @endif

                @if(filled($entry->landing_details))
                    <section class="dip-block" aria-labelledby="dip-more">
                        <h2 id="dip-more">معلومات إضافية</h2>
                        <p class="dip-details">{{ $entry->landing_details }}</p>
                    </section>
                @endif
            </div>

            <aside class="h-side dip-side" aria-label="الرسوم والحجز">
                <div class="dip-card">
                    @if($price !== null || $recorded !== null)
                        <div class="dip-price">
                            @if($price !== null)
                                <small>رسوم الدبلومة</small>
                                <b>{{ number_format($price, 0) }} <span>ج.م</span></b>
                                @if($recorded !== null)<p>النسخة المسجّلة: {{ number_format($recorded, 0) }} ج.م</p>@endif
                            @else
                                <small>النسخة المسجّلة</small>
                                <b>{{ number_format($recorded, 0) }} <span>ج.م</span></b>
                            @endif
                        </div>
                    @endif
                    <div class="dip-card-body">
                        @if($start)<div class="dip-start"><span>أقرب دفعة</span><b>{{ $start }}</b></div>@endif
                        <a href="{{ route('home') }}#consult" data-program="{{ $entry->name }}" class="h-btn h-btn-primary h-btn-lg h-btn-block">احجز مقعدك</a>
                        <a href="{{ $contactUrl }}" class="h-btn h-btn-outline h-btn-block">اسأل فريق القبول</a>
                        @if($pathUrl)<a href="{{ $pathUrl }}" class="h-link">صفحة المسار والتسجيل أونلاين</a>@endif
                    </div>
                    @if($bookingMethods !== [])
                        <div class="dip-pay" style="border-top: 1px solid var(--h-line)">
                            <h3>طرق الدفع</h3>
                            <ul>@foreach($bookingMethods as $m)<li><i class="fas fa-wallet" aria-hidden="true"></i><span>{{ $m }}</span></li>@endforeach</ul>
                        </div>
                    @endif
                </div>
            </aside>
        </div>
    </main>

    <div class="dip-mobile-bar">
        @if($price !== null)<div><small>الرسوم</small><b>{{ number_format($price, 0) }} ج.م</b></div>@endif
        <a href="{{ route('home') }}#consult" data-program="{{ $entry->name }}" class="h-btn h-btn-primary">احجز مقعدك</a>
    </div>

    @include('components.site.footer')
</body>
</html>
