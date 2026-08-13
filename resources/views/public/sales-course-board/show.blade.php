<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $entry->name }} — Mindlytics Academy</title>
    <meta name="description" content="{{ \Illuminate\Support\Str::limit(strip_tags($entry->summary ?? $entry->name), 160) }}">
    <meta property="og:title" content="{{ $entry->name }} — Mindlytics Academy">
    <meta property="og:description" content="{{ \Illuminate\Support\Str::limit(strip_tags($entry->summary ?? ''), 200) }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body class="bg-gradient-to-br from-slate-50 via-emerald-50/30 to-sky-50 min-h-screen text-slate-900">
    <div class="max-w-3xl mx-auto px-4 py-8 sm:py-12">
        <header class="text-center mb-8">
            <p class="text-sm font-semibold text-emerald-700 tracking-wide uppercase mb-2">Mindlytics Academy</p>
            <h1 class="text-3xl sm:text-4xl font-black text-slate-900">{{ $entry->name }}</h1>
            @if(filled($entry->summary))
                <p class="text-slate-600 mt-3 text-lg leading-relaxed">{{ $entry->summary }}</p>
            @endif
        </header>

        <div class="bg-white rounded-3xl border border-slate-200 shadow-xl shadow-slate-200/50 overflow-hidden">
            <div class="grid sm:grid-cols-2 divide-y sm:divide-y-0 sm:divide-x sm:divide-x-reverse divide-slate-100">
                @foreach([
                    ['icon' => 'fa-users', 'label' => 'الفئة', 'value' => $entry->audience],
                    ['icon' => 'fa-chalkboard-teacher', 'label' => 'المدرب', 'value' => $entry->instructor_name],
                    ['icon' => 'fa-calendar-day', 'label' => 'البداية', 'value' => $entry->start_label],
                    ['icon' => 'fa-calendar-week', 'label' => 'الأيام', 'value' => $entry->schedule_days],
                    ['icon' => 'fa-hourglass-half', 'label' => 'المدة', 'value' => $entry->duration],
                    ['icon' => 'fa-clock', 'label' => 'الساعات', 'value' => $entry->hours],
                    ['icon' => 'fa-laptop', 'label' => 'النظام', 'value' => $entry->format],
                ] as $row)
                    @if(filled($row['value']) && $row['value'] !== '—')
                        <div class="p-5 flex gap-4 items-start">
                            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center shrink-0">
                                <i class="fas {{ $row['icon'] }}"></i>
                            </div>
                            <div>
                                <div class="text-xs font-bold text-slate-500 uppercase tracking-wide">{{ $row['label'] }}</div>
                                <div class="font-bold text-slate-900 mt-0.5">{{ $row['value'] }}</div>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>

            @if($entry->price_online || $entry->price_recorded)
                <div class="px-6 py-5 bg-gradient-to-l from-emerald-600 to-teal-600 text-white">
                    <div class="text-sm font-semibold opacity-90 mb-1">السعر (EGP)</div>
                    <div class="flex flex-wrap gap-4 items-end">
                        @if($entry->price_online)
                            <div>
                                <span class="text-3xl font-black">{{ number_format((float) $entry->price_online, 0) }}</span>
                                <span class="text-sm mr-1 opacity-90">أونلاين</span>
                            </div>
                        @endif
                        @if($entry->price_recorded)
                            <div>
                                <span class="text-2xl font-black">{{ number_format((float) $entry->price_recorded, 0) }}</span>
                                <span class="text-sm mr-1 opacity-90">مسجّل</span>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            @php $highlights = $entry->landingHighlights(); @endphp
            @if($highlights !== [])
                <div class="px-6 py-6 border-t border-slate-100">
                    <h2 class="font-black text-lg mb-4">تفاصيل الكورس</h2>
                    <ul class="space-y-2">
                        @foreach($highlights as $item)
                            <li class="flex gap-2 text-slate-700">
                                <i class="fas fa-check-circle text-emerald-500 mt-1 shrink-0"></i>
                                <span>{{ $item }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if(filled($entry->landing_details))
                <div class="px-6 py-6 border-t border-slate-100 bg-slate-50/50">
                    <h2 class="font-black text-lg mb-3">معلومات إضافية</h2>
                    <div class="text-slate-700 leading-relaxed whitespace-pre-line">{{ $entry->landing_details }}</div>
                </div>
            @endif
        </div>

        <footer class="text-center mt-8 text-sm text-slate-500">
            <p>Mindlytics Academy — للاستفسار تواصل مع فريق المبيعات</p>
        </footer>
    </div>
</body>
</html>
