<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>موعد العمل — {{ config('app.name') }}</title>
    @include('components.favicon-meta')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { font-family: 'Tajawal', sans-serif; }

        body {
            min-height: 100vh;
            background: #f8fafc;
            overflow-x: hidden;
        }

        .bg-mesh {
            position: fixed;
            inset: 0;
            z-index: 0;
            background:
                radial-gradient(ellipse 80% 60% at 50% -10%, rgba(56, 189, 248, 0.18), transparent),
                radial-gradient(ellipse 60% 50% at 100% 50%, rgba(16, 185, 129, 0.08), transparent),
                radial-gradient(ellipse 50% 40% at 0% 80%, rgba(59, 130, 246, 0.1), transparent);
        }

        .icon-ring {
            width: 88px;
            height: 88px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            margin: 0 auto 28px;
        }
        .icon-ring::before {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: 50%;
            background: conic-gradient(from 180deg, #38bdf8, #2563eb, #10b981, #38bdf8);
            opacity: 0.35;
            animation: spin 8s linear infinite;
        }
        .icon-ring::after {
            content: '';
            position: absolute;
            inset: 3px;
            border-radius: 50%;
            background: #f8fafc;
        }
        .icon-ring i {
            position: relative;
            z-index: 1;
            font-size: 2rem;
            color: #2563eb;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .timer {
            font-variant-numeric: tabular-nums;
            letter-spacing: 0.08em;
            background: linear-gradient(135deg, #1e40af 0%, #0891b2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .schedule-line {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 0.5rem 1.5rem;
            color: #64748b;
            font-size: 0.875rem;
        }
        .schedule-line span {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
        }
        .schedule-line strong {
            color: #1e293b;
            font-weight: 700;
        }

        .btn-primary {
            background: linear-gradient(135deg, #2563eb, #0891b2);
            box-shadow: 0 4px 14px rgba(37, 99, 235, 0.3);
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(37, 99, 235, 0.4);
        }
    </style>
</head>
<body class="text-slate-800">
@php
    $att = $state ?? ($employeeAttendance ?? []);
    $mode = $att['mode'] ?? 'locked_before_shift';
    $user = auth()->user();
    $unlock = $att['unlock'] ?? null;

    $title = match ($mode) {
        'completed' => 'انتهى يوم العمل',
        'awaiting_clock_in' => 'حان وقت الحضور',
        'manager_unlocked' => 'تم فتح النظام',
        'missed_shift' => 'فات موعد الحضور',
        'on_leave' => 'يوم إجازة',
        'off_day' => 'يوم راحة',
        default => 'نظام العمل مغلق',
    };

    $icon = match ($mode) {
        'completed' => 'fa-check',
        'awaiting_clock_in' => 'fa-fingerprint',
        'manager_unlocked' => 'fa-unlock-alt',
        'missed_shift' => 'fa-exclamation',
        'on_leave' => 'fa-umbrella-beach',
        'off_day' => 'fa-moon',
        default => 'fa-clock',
    };
@endphp

<div class="bg-mesh" aria-hidden="true"></div>

<div class="relative z-10 min-h-screen flex flex-col items-center justify-center px-5 py-12">

    {{-- Logo --}}
    <div class="mb-10 text-center">
        <img src="{{ $platformLogoUrl ?? asset('logo-fallback.svg') }}"
             alt="{{ config('app.name') }}"
             class="h-11 w-auto mx-auto mb-2 opacity-90"
             onerror="this.onerror=null; this.src='{{ asset('logo-fallback.svg') }}';">
        <p class="text-xs text-slate-400 tracking-wide">بوابة الموظفين</p>
    </div>

    {{-- Main content --}}
    <div class="w-full max-w-md text-center">

        <div class="icon-ring">
            <i class="fas {{ $icon }}"></i>
        </div>

        <h1 class="text-2xl sm:text-3xl font-black text-slate-900 mb-2">{{ $title }}</h1>

        @if($user)
            <p class="text-slate-500 text-sm mb-1">{{ $user->name }}</p>
        @endif

        <p class="text-slate-600 text-sm leading-relaxed mb-8 max-w-sm mx-auto">
            {{ $att['message'] ?? 'لا يمكن الوصول للنظام خارج موعد العمل.' }}
        </p>

        @if($unlock)
            <div class="mb-6 rounded-xl border border-teal-200 bg-teal-50 px-4 py-3 text-right text-sm text-teal-900">
                <p class="font-bold flex items-center gap-2 justify-start">
                    <i class="fas fa-unlock-alt text-teal-600"></i>
                    تصريح فتح من المدير
                </p>
                <p class="text-xs text-teal-800 mt-1.5 leading-relaxed">
                    بواسطة: <strong>{{ $unlock['manager_name'] ?? 'مدير المبيعات' }}</strong>
                    · حتى <strong>{{ $unlock['expires_at_human'] ?? '—' }}</strong>
                    @if(!empty($unlock['duration_label']))
                        ({{ $unlock['duration_label'] }})
                    @endif
                </p>
                @if(!empty($unlock['reason']))
                    <p class="text-[11px] text-teal-700/90 mt-1">السبب: {{ $unlock['reason'] }}</p>
                @endif
            </div>
        @endif

        {{-- Countdown --}}
        @if($mode === 'locked_before_shift' && ($att['seconds_until_open'] ?? 0) > 0)
            <div class="mb-8" x-data="countdown({{ (int) ($att['seconds_until_open'] ?? 0) }})" x-init="start()">
                <p class="text-xs font-semibold text-sky-600 uppercase tracking-widest mb-2">يفتح خلال</p>
                <p class="timer text-5xl sm:text-6xl font-black" x-text="display"></p>
            </div>
        @endif

        {{-- Schedule — single line, no cards --}}
        @if($att['schedule'] ?? null)
            <div class="schedule-line mb-8 pb-8 border-b border-slate-200/80">
                <span><i class="fas fa-calendar-day text-sky-400 text-xs"></i> <strong>{{ $att['schedule']->name }}</strong></span>
                <span><i class="fas fa-clock text-sky-400 text-xs"></i> <strong class="tabular-nums">{{ $att['schedule']->timeRangeLabel() }}</strong></span>
                <span><i class="fas fa-hourglass-half text-sky-400 text-xs"></i> <strong>{{ $att['schedule']->required_hours }} س</strong></span>
            </div>
        @endif

        {{-- Actions --}}
        @if(in_array($mode, ['awaiting_clock_in', 'manager_unlocked'], true) && ($att['can_clock_in'] ?? false))
            <form method="post" action="{{ route('employee.attendance.clock-in') }}" class="mb-6">
                @csrf
                <button type="submit" class="btn-primary w-full text-white font-bold py-3.5 px-6 rounded-xl inline-flex items-center justify-center gap-2">
                    <i class="fas fa-fingerprint"></i>
                    تسجيل الحضور
                </button>
            </form>
        @endif

        @if($mode === 'completed' && ($att['record'] ?? null))
            <p class="text-sm text-emerald-600 font-semibold mb-6">
                <i class="fas fa-check-circle ml-1"></i>
                {{ number_format(($att['record']->worked_minutes ?? 0) / 60, 2) }} ساعة عمل
            </p>
        @endif

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="text-sm text-slate-400 hover:text-sky-600 transition-colors">
                <i class="fas fa-sign-out-alt ml-1"></i>
                تسجيل الخروج
            </button>
        </form>
    </div>

    <p class="absolute bottom-6 text-xs text-slate-300">
        © {{ date('Y') }} {{ config('app.name') }}
    </p>
</div>

<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
<script>
function countdown(seconds) {
    return {
        remaining: seconds,
        display: '00:00:00',
        start() {
            const tick = () => {
                if (this.remaining <= 0) {
                    window.location.reload();
                    return;
                }
                const h = Math.floor(this.remaining / 3600);
                const m = Math.floor((this.remaining % 3600) / 60);
                const s = this.remaining % 60;
                this.display = [h, m, s].map(v => String(v).padStart(2, '0')).join(':');
                this.remaining--;
                setTimeout(tick, 1000);
            };
            tick();
        }
    };
}
</script>
</body>
</html>
