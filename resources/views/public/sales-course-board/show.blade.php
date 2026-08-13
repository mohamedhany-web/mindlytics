@extends('layouts.public')

@section('title', $entry->name . ' | Mindlytics Academy')
@section('meta_description', \Illuminate\Support\Str::limit(strip_tags($entry->summary ?? $entry->name), 160))

@push('meta')
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="{{ $entry->name }} — Mindlytics Academy">
    <meta property="og:description" content="{{ \Illuminate\Support\Str::limit(strip_tags($entry->summary ?? ''), 200) }}">
@endpush

@push('styles')
@include('careers._styles')
<style>
    .course-info-hero { min-height: 42vh; }
    .price-panel {
        background: linear-gradient(145deg, #1e3a8a 0%, #1d4ed8 42%, #0891b2 100%);
        border-radius: 1.5rem;
        box-shadow: 0 20px 50px rgba(29, 78, 216, 0.25);
        color: #fff;
    }
    .price-tag {
        background: rgba(255, 255, 255, 0.12);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 1rem;
        backdrop-filter: blur(8px);
    }
    .detail-list li {
        display: flex;
        gap: 0.75rem;
        align-items: flex-start;
        padding: 0.65rem 0;
        border-bottom: 1px solid #f1f5f9;
    }
    .detail-list li:last-child { border-bottom: none; }
    .detail-list .icon-wrap {
        width: 2rem;
        height: 2rem;
        border-radius: 0.65rem;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        background: linear-gradient(135deg, #eff6ff, #ecfdf5);
        color: #2563eb;
    }
    .cta-primary {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        width: 100%;
        padding: 0.875rem 1.25rem;
        border-radius: 0.875rem;
        font-weight: 800;
        background: linear-gradient(135deg, #2563eb, #0891b2);
        color: #fff;
        transition: transform 0.2s, box-shadow 0.2s;
        box-shadow: 0 8px 24px rgba(37, 99, 235, 0.35);
    }
    .cta-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 32px rgba(37, 99, 235, 0.4);
        color: #fff;
    }
    .cta-outline {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        width: 100%;
        padding: 0.75rem 1.25rem;
        border-radius: 0.875rem;
        font-weight: 700;
        border: 2px solid #e2e8f0;
        color: #1e3a8a;
        background: #fff;
        transition: all 0.2s;
    }
    .cta-outline:hover {
        border-color: #93c5fd;
        background: #f8fafc;
        color: #1d4ed8;
    }
    .highlight-item {
        display: flex;
        gap: 0.75rem;
        padding: 0.75rem 1rem;
        border-radius: 0.875rem;
        background: linear-gradient(135deg, #f0fdf4 0%, #ecfdf5 100%);
        border: 1px solid #bbf7d0;
        color: #065f46;
        font-weight: 600;
    }
    .details-prose p { margin-bottom: 0.75rem; line-height: 1.8; color: #334155; }
    .details-prose ul { list-style: none; padding: 0; margin: 0; }
    .details-prose li {
        position: relative;
        padding-right: 1.25rem;
        margin-bottom: 0.5rem;
        color: #475569;
        line-height: 1.75;
    }
    .details-prose li::before {
        content: '';
        position: absolute;
        right: 0;
        top: 0.65rem;
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: linear-gradient(135deg, #2563eb, #10b981);
    }
</style>
@endpush

@section('content')
@php
    $metaChips = array_values(array_filter([
        filled($entry->format) && $entry->format !== '—' ? ['label' => $entry->format, 'icon' => 'fas fa-laptop', 'tone' => 'blue'] : null,
        filled($entry->audience) ? ['label' => $entry->audience, 'icon' => 'fas fa-users', 'tone' => 'green'] : null,
        filled($entry->start_label) && $entry->start_label !== '—' ? ['label' => 'البداية: '.$entry->start_label, 'icon' => 'fas fa-calendar-day', 'tone' => 'violet'] : null,
    ]));

    $detailRows = array_values(array_filter([
        ['icon' => 'fa-chalkboard-teacher', 'label' => 'المدرب', 'value' => $entry->instructor_name, 'tone' => 'blue'],
        ['icon' => 'fa-calendar-week', 'label' => 'أيام المحاضرات', 'value' => $entry->schedule_days, 'tone' => 'violet'],
        ['icon' => 'fa-hourglass-half', 'label' => 'المدة', 'value' => $entry->duration, 'tone' => 'green'],
        ['icon' => 'fa-clock', 'label' => 'عدد الساعات', 'value' => $entry->hours, 'tone' => 'blue'],
    ], fn ($r) => filled($r['value']) && $r['value'] !== '—'));

    $highlights = $entry->landingHighlights();
    $detailLines = filled($entry->landing_details)
        ? array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n|•|·(?=\s)/', $entry->landing_details))))
        : [];

    $courseUrl = $entry->advanced_course_id
        ? route('public.course.show', $entry->advanced_course_id)
        : null;
    $shareUrl = url()->current();
@endphp

@include('careers._hero', [
    'badge' => 'Mindlytics Academy · معلومات الكورس',
    'title' => $entry->name,
    'subtitle' => $entry->summary,
    'backUrl' => url('/courses'),
    'backLabel' => 'كل الكورسات',
    'metaChips' => $metaChips,
])

<section class="py-10 bg-white border-b border-slate-100">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 max-w-6xl">
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach($detailRows as $row)
                @php
                    $tone = $row['tone'];
                    $bg = match($tone) {
                        'green' => 'bg-emerald-100 text-emerald-600',
                        'violet' => 'bg-violet-100 text-violet-600',
                        default => 'bg-blue-100 text-blue-600',
                    };
                @endphp
                <div class="stat-card p-5 flex items-center gap-4">
                    <div class="w-11 h-11 rounded-xl {{ $bg }} flex items-center justify-center flex-shrink-0">
                        <i class="fas {{ $row['icon'] }}"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs font-bold text-slate-500 mb-0.5">{{ $row['label'] }}</p>
                        <p class="text-sm font-extrabold text-slate-900 truncate">{{ $row['value'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

<section class="py-14 md:py-20 bg-gradient-to-b from-white via-blue-50/25 to-white">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 max-w-6xl">
        <div class="grid lg:grid-cols-12 gap-8 lg:gap-10 items-start">
            <div class="lg:col-span-7 space-y-6">
                @if($highlights !== [])
                    <div class="content-panel">
                        <div class="content-panel-head">
                            <h2 class="text-xl font-extrabold text-blue-900 section-title">تفاصيل الكورس</h2>
                        </div>
                        <div class="p-5 sm:p-6 space-y-3">
                            @foreach($highlights as $item)
                                <div class="highlight-item">
                                    <i class="fas fa-check-circle text-emerald-600 mt-0.5 shrink-0"></i>
                                    <span>{{ $item }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if($detailLines !== [])
                    <div class="content-panel">
                        <div class="content-panel-head">
                            <h2 class="text-xl font-extrabold text-blue-900 section-title">معلومات إضافية</h2>
                        </div>
                        <div class="p-5 sm:p-6 details-prose">
                            <ul>
                                @foreach($detailLines as $line)
                                    @if(mb_strlen($line) > 1)
                                        <li>{{ $line }}</li>
                                    @endif
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif

                <div class="content-panel p-5 sm:p-6">
                    <h3 class="text-lg font-extrabold text-blue-900 mb-4 flex items-center gap-2">
                        <span class="section-bar"></span>
                        ملخص سريع
                    </h3>
                    <ul class="detail-list text-sm">
                        @foreach([
                            ['fa-users', 'الفئة المستهدفة', $entry->audience],
                            ['fa-laptop', 'نظام الحضور', $entry->format],
                            ['fa-calendar-day', 'موعد البداية', $entry->start_label],
                        ] as [$icon, $label, $value])
                            @if(filled($value) && $value !== '—')
                                <li>
                                    <span class="icon-wrap"><i class="fas {{ $icon }} text-sm"></i></span>
                                    <div>
                                        <p class="text-xs font-bold text-slate-500">{{ $label }}</p>
                                        <p class="font-bold text-slate-900">{{ $value }}</p>
                                    </div>
                                </li>
                            @endif
                        @endforeach
                    </ul>
                </div>
            </div>

            <div class="lg:col-span-5 lg:sticky lg:top-28 space-y-4">
                <div class="price-panel p-6 sm:p-8">
                    <p class="text-sm font-bold text-blue-100 mb-1">الاستثمار في الكورس</p>
                    <h2 class="text-2xl font-black mb-6">{{ $entry->name }}</h2>

                    @if($entry->price_online || $entry->price_recorded)
                        <div class="space-y-3 mb-6">
                            @if($entry->price_online)
                                <div class="price-tag p-4 flex items-end justify-between gap-3">
                                    <div>
                                        <p class="text-xs font-bold text-blue-100 mb-1">أونلاين · Live</p>
                                        <p class="text-3xl font-black leading-none">{{ number_format((float) $entry->price_online, 0) }}</p>
                                    </div>
                                    <span class="text-sm font-bold opacity-80">ج.م</span>
                                </div>
                            @endif
                            @if($entry->price_recorded)
                                <div class="price-tag p-4 flex items-end justify-between gap-3">
                                    <div>
                                        <p class="text-xs font-bold text-blue-100 mb-1">مسجّل · Recorded</p>
                                        <p class="text-2xl font-black leading-none">{{ number_format((float) $entry->price_recorded, 0) }}</p>
                                    </div>
                                    <span class="text-sm font-bold opacity-80">ج.م</span>
                                </div>
                            @endif
                        </div>
                    @else
                        <p class="text-blue-100 mb-6 text-sm">السعر قيد التحديث — تواصل مع فريق المبيعات للتفاصيل.</p>
                    @endif

                    <a href="{{ route('public.contact') }}?subject={{ urlencode('استفسار عن كورس: '.$entry->name) }}" class="cta-primary mb-3">
                        <i class="fas fa-headset"></i>
                        تواصل مع فريق المبيعات
                    </a>
                    @if($courseUrl)
                        <a href="{{ $courseUrl }}" class="cta-outline mb-3">
                            <i class="fas fa-graduation-cap"></i>
                            صفحة الكورس على المنصة
                        </a>
                    @endif
                    <button type="button" id="copy-course-link" data-url="{{ $shareUrl }}" class="cta-outline">
                        <i class="fas fa-link"></i>
                        نسخ رابط الصفحة
                    </button>
                </div>

                <div class="content-panel p-5 text-center text-sm text-slate-600">
                    <i class="fas fa-shield-alt text-blue-600 ml-1"></i>
                    Mindlytics Academy — أكاديمية تقنية معتمدة للتدريب العملي
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
document.getElementById('copy-course-link')?.addEventListener('click', function () {
    var url = this.getAttribute('data-url');
    if (!url || !navigator.clipboard) return;
    navigator.clipboard.writeText(url).then(function () {
        var btn = document.getElementById('copy-course-link');
        var old = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-check"></i> تم نسخ الرابط';
        setTimeout(function () { btn.innerHTML = old; }, 2000);
    });
});
</script>
@endpush
