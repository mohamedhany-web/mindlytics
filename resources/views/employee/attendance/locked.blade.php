<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>موعد العمل — {{ config('app.name') }}</title>
    @include('components.favicon-meta')
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="min-h-screen bg-gradient-to-br from-slate-900 via-slate-800 to-indigo-950 text-white flex items-center justify-center p-4">
@php
    $att = $state ?? ($employeeAttendance ?? []);
    $mode = $att['mode'] ?? 'locked_before_shift';
@endphp
<div class="w-full max-w-lg">
    <div class="rounded-3xl bg-white/10 backdrop-blur border border-white/20 shadow-2xl p-8 text-center">
        <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-amber-500/20 flex items-center justify-center text-amber-300 text-2xl">
            <i class="fas fa-clock"></i>
        </div>
        <h1 class="text-2xl font-black mb-2">نظام العمل مغلق</h1>
        <p class="text-white/80 text-sm mb-6">{{ $att['message'] ?? '' }}</p>

        @if($att['schedule'] ?? null)
            <div class="rounded-xl bg-black/20 px-4 py-3 text-sm mb-6 text-right space-y-1">
                <p><span class="text-white/60">الموعد:</span> <strong>{{ $att['schedule']->name }}</strong></p>
                <p><span class="text-white/60">الدوام:</span> {{ $att['schedule']->timeRangeLabel() }}</p>
                <p><span class="text-white/60">الساعات المطلوبة:</span> {{ $att['schedule']->required_hours }} ساعة</p>
            </div>
        @endif

        @if(in_array($mode, ['locked_before_shift'], true) && ($att['seconds_until_open'] ?? 0) > 0)
            <div class="mb-6" x-data="countdown({{ (int) ($att['seconds_until_open'] ?? 0) }})" x-init="start()">
                <p class="text-xs text-white/60 mb-2">يفتح النظام خلال</p>
                <p class="text-4xl font-black tabular-nums tracking-wider" x-text="display"></p>
            </div>
        @endif

        @if($mode === 'completed')
            <p class="text-emerald-300 font-semibold mb-4"><i class="fas fa-check-circle ml-1"></i> أنهيت يوم العمل بنجاح.</p>
            @if($att['record'] ?? null)
                <p class="text-sm text-white/70 mb-4">ساعات العمل: {{ number_format(($att['record']->worked_minutes ?? 0) / 60, 2) }} ساعة</p>
            @endif
        @endif

        @if($mode === 'awaiting_clock_in')
            <form method="post" action="{{ route('employee.attendance.clock-in') }}" class="mb-4">
                @csrf
                <button type="submit" class="w-full inline-flex items-center justify-center gap-2 bg-emerald-500 hover:bg-emerald-600 text-white font-bold py-4 px-6 rounded-2xl shadow-lg transition">
                    <i class="fas fa-fingerprint"></i>
                    تسجيل الحضور
                </button>
            </form>
        @endif

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="text-sm text-white/60 hover:text-white underline">تسجيل الخروج</button>
        </form>
    </div>
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
                this.display = [h,m,s].map(v => String(v).padStart(2,'0')).join(':');
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
