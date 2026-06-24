@extends('layouts.admin')

@section('title', 'سجل أخطاء المنصة')
@section('header', 'سجل أخطاء المنصة')

@section('content')
@php
    $statCards = [
        ['label' => 'مفتوحة', 'value' => number_format($stats['open'] ?? 0), 'icon' => 'fas fa-bug', 'bg' => 'bg-rose-100', 'text' => 'text-rose-600', 'description' => 'تحتاج معالجة'],
        ['label' => 'اليوم', 'value' => number_format($stats['today'] ?? 0), 'icon' => 'fas fa-calendar-day', 'bg' => 'bg-amber-100', 'text' => 'text-amber-600', 'description' => 'أخطاء جديدة'],
        ['label' => 'حرجة مفتوحة', 'value' => number_format($stats['critical'] ?? 0), 'icon' => 'fas fa-fire', 'bg' => 'bg-orange-100', 'text' => 'text-orange-600', 'description' => 'أولوية عالية'],
        ['label' => 'حُلّت هذا الأسبوع', 'value' => number_format($stats['resolved_week'] ?? 0), 'icon' => 'fas fa-check-circle', 'bg' => 'bg-emerald-100', 'text' => 'text-emerald-600', 'description' => 'تم الإغلاق'],
    ];
    $quickFilters = [
        ['key' => 'unresolved', 'label' => 'غير محلولة', 'icon' => 'fas fa-exclamation-circle'],
        ['key' => 'today', 'label' => 'اليوم', 'icon' => 'fas fa-clock'],
        ['key' => 'critical', 'label' => 'حرجة', 'icon' => 'fas fa-fire'],
        ['key' => 'open', 'label' => 'مفتوحة فقط', 'icon' => 'fas fa-folder-open'],
    ];
@endphp

<div class="space-y-6">
    @if(!empty($setupRequired))
        <div class="rounded-xl border border-amber-300 bg-amber-50 px-4 py-4 text-sm text-amber-950">
            <p class="font-bold text-base mb-1"><i class="fas fa-database ml-1"></i> إعداد قاعدة البيانات مطلوب</p>
            <p class="mb-2">جدول <code class="font-mono text-xs bg-white px-1 rounded">platform_error_logs</code> غير موجود على السيرفر. نفّذ على الاستضافة:</p>
            <pre class="text-xs bg-slate-900 text-emerald-300 rounded-lg p-3 overflow-x-auto font-mono">php artisan migrate --force
php artisan route:clear
php artisan view:clear</pre>
        </div>
    @endif

    @if(!empty($loadError))
        <div class="rounded-xl border border-rose-300 bg-rose-50 px-4 py-3 text-sm text-rose-900 font-medium">
            <i class="fas fa-exclamation-triangle ml-1"></i>{{ $loadError }}
        </div>
    @endif

    @if(session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900 font-medium">
            <i class="fas fa-check-circle ml-1"></i>{{ session('success') }}
        </div>
    @endif

    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-5 py-6 sm:px-8 border-b border-slate-200 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h2 class="text-2xl font-black text-slate-900">مراقبة أخطاء المنصة</h2>
                <p class="text-sm text-slate-600 mt-1">كل خطأ 500 أو استثناء يُسجَّل تلقائياً مع المستخدم والرابط وسياق الطلب</p>
            </div>
            <a href="{{ route('admin.activity-log') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-slate-200 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                <i class="fas fa-history"></i>
                سجل النشاطات
            </a>
        </div>

        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 p-5 sm:p-6 border-b border-slate-100 bg-slate-50/50">
            @foreach($statCards as $card)
                <div class="rounded-xl bg-white border border-slate-200 p-4 shadow-sm">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg {{ $card['bg'] }} {{ $card['text'] }} flex items-center justify-center">
                            <i class="{{ $card['icon'] }}"></i>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500">{{ $card['label'] }}</p>
                            <p class="text-xl font-black text-slate-900">{{ $card['value'] }}</p>
                        </div>
                    </div>
                    <p class="text-[11px] text-slate-400 mt-2">{{ $card['description'] }}</p>
                </div>
            @endforeach
        </div>

        @if($topFingerprints->isNotEmpty())
            <div class="px-5 sm:px-6 py-4 border-b border-slate-100 bg-rose-50/40">
                <p class="text-xs font-bold text-rose-800 mb-2"><i class="fas fa-layer-group ml-1"></i> أكثر الأخطاء تكراراً (غير محلولة)</p>
                <div class="flex flex-wrap gap-2">
                    @foreach($topFingerprints as $fp)
                        <a href="{{ route('admin.platform-errors.index', ['search' => \Illuminate\Support\Str::limit($fp->sample_message, 60)]) }}"
                           class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-white border border-rose-200 text-xs text-rose-900 hover:bg-rose-50">
                            <span class="font-bold text-rose-600">{{ $fp->hits }}×</span>
                            <span class="max-w-[220px] truncate">{{ $fp->sample_class ?: \Illuminate\Support\Str::limit($fp->sample_message, 50) }}</span>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        <div class="px-5 sm:px-6 py-4 border-b border-slate-100 space-y-3">
            <div class="flex flex-wrap gap-2">
                @foreach($quickFilters as $qf)
                    <a href="{{ route('admin.platform-errors.index', array_merge(request()->except('quick', 'page'), ['quick' => $qf['key']])) }}"
                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold border transition-colors
                       {{ request('quick') === $qf['key'] ? 'bg-slate-900 text-white border-slate-900' : 'bg-white text-slate-700 border-slate-200 hover:border-slate-400' }}">
                        <i class="{{ $qf['icon'] }}"></i>{{ $qf['label'] }}
                    </a>
                @endforeach
                @if(request()->hasAny(['quick','status','level','user_id','search','date_from','date_to','guest_only']))
                    <a href="{{ route('admin.platform-errors.index') }}" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-full text-xs font-semibold text-slate-500 hover:text-rose-600">
                        <i class="fas fa-times"></i> مسح الفلاتر
                    </a>
                @endif
            </div>

            <form method="GET" action="{{ route('admin.platform-errors.index') }}" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-6 gap-3">
                @if(request('quick'))<input type="hidden" name="quick" value="{{ request('quick') }}">@endif
                <div class="xl:col-span-2">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="بحث: رسالة، رابط، ملف، مستخدم…"
                           class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">
                </div>
                <div>
                    <select name="status" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm">
                        <option value="">كل الحالات</option>
                        @foreach(\App\Models\PlatformErrorLog::STATUSES as $k => $label)
                            <option value="{{ $k }}" @selected(request('status') === $k)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <select name="level" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm">
                        <option value="">كل المستويات</option>
                        @foreach(\App\Models\PlatformErrorLog::LEVELS as $k => $label)
                            <option value="{{ $k }}" @selected(request('level') === $k)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <select name="user_id" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm">
                        <option value="">كل المستخدمين</option>
                        @foreach($userOptions as $u)
                            <option value="{{ $u->id }}" @selected((string) request('user_id') === (string) $u->id)>{{ $u->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="flex-1 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-sm font-bold py-2.5">
                        <i class="fas fa-search ml-1"></i> فلترة
                    </button>
                </div>
                <div>
                    <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm" title="من تاريخ">
                </div>
                <div>
                    <input type="date" name="date_to" value="{{ request('date_to') }}" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm" title="إلى تاريخ">
                </div>
                <label class="inline-flex items-center gap-2 text-sm text-slate-700 px-1">
                    <input type="checkbox" name="guest_only" value="1" @checked(request()->boolean('guest_only')) class="rounded border-slate-300 text-rose-600">
                    زوار فقط (بدون مستخدم)
                </label>
            </form>
        </div>

        <form method="POST" action="{{ route('admin.platform-errors.bulk') }}" id="bulk-errors-form">
            @csrf
            <div class="px-5 sm:px-6 py-3 border-b border-slate-100 flex flex-wrap items-center gap-2 bg-slate-50/80">
                <span class="text-xs font-semibold text-slate-600">إجراء جماعي:</span>
                <button type="submit" name="status" value="acknowledged" class="px-3 py-1.5 rounded-lg bg-amber-100 text-amber-900 text-xs font-bold hover:bg-amber-200">تعيين: قيد المعالجة</button>
                <button type="submit" name="status" value="resolved" class="px-3 py-1.5 rounded-lg bg-emerald-100 text-emerald-900 text-xs font-bold hover:bg-emerald-200">تعيين: تم الحل</button>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-3 py-3 w-10"><input type="checkbox" id="select-all-errors" class="rounded border-slate-300"></th>
                            <th class="px-4 py-3 text-right text-xs font-bold text-slate-500 uppercase">الوقت</th>
                            <th class="px-4 py-3 text-right text-xs font-bold text-slate-500 uppercase">المستوى</th>
                            <th class="px-4 py-3 text-right text-xs font-bold text-slate-500 uppercase">الرسالة</th>
                            <th class="px-4 py-3 text-right text-xs font-bold text-slate-500 uppercase">المستخدم</th>
                            <th class="px-4 py-3 text-right text-xs font-bold text-slate-500 uppercase">الرابط</th>
                            <th class="px-4 py-3 text-right text-xs font-bold text-slate-500 uppercase">الحالة</th>
                            <th class="px-4 py-3 text-center text-xs font-bold text-slate-500 uppercase">تفاصيل</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @forelse($errorLogs as $err)
                            @php
                                $levelClass = match($err->level) {
                                    'critical' => 'bg-rose-100 text-rose-800 border-rose-200',
                                    'warning' => 'bg-amber-100 text-amber-800 border-amber-200',
                                    default => 'bg-slate-100 text-slate-800 border-slate-200',
                                };
                                $statusClass = match($err->status) {
                                    'resolved' => 'bg-emerald-100 text-emerald-800',
                                    'acknowledged' => 'bg-sky-100 text-sky-800',
                                    default => 'bg-rose-100 text-rose-800',
                                };
                            @endphp
                            <tr class="hover:bg-slate-50/80 {{ $err->status === 'open' ? 'bg-rose-50/20' : '' }}">
                                <td class="px-3 py-3 text-center">
                                    <input type="checkbox" name="ids[]" value="{{ $err->id }}" form="bulk-errors-form" class="err-checkbox rounded border-slate-300">
                                </td>
                                <td class="px-4 py-3 text-xs text-slate-600 whitespace-nowrap tabular-nums">
                                    {{ $err->created_at->format('m-d H:i') }}
                                    <div class="text-[10px] text-slate-400">{{ $err->created_at->diffForHumans() }}</div>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex px-2 py-0.5 rounded-full text-[11px] font-bold border {{ $levelClass }}">
                                        {{ \App\Models\PlatformErrorLog::levelLabel($err->level) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 max-w-md">
                                    <p class="font-semibold text-slate-900 truncate" title="{{ $err->message }}">{{ $err->message }}</p>
                                    @if($err->exception_class)
                                        <p class="text-[11px] text-slate-500 truncate font-mono">{{ class_basename($err->exception_class) }}</p>
                                    @endif
                                    @if($err->shortLocation())
                                        <p class="text-[10px] text-slate-400 truncate font-mono">{{ $err->shortLocation() }}</p>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-xs">
                                    @if($err->user_id)
                                        <p class="font-semibold text-slate-800">{{ $err->user?->name }}</p>
                                        <p class="text-slate-500 truncate max-w-[140px]">{{ $err->user?->email }}</p>
                                    @else
                                        <span class="text-slate-400">زائر</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-xs max-w-[180px]">
                                    @if($err->url)
                                        <span class="text-slate-600 truncate block" title="{{ $err->url }}">{{ \Illuminate\Support\Str::limit($err->url, 45) }}</span>
                                        @if($err->method)<span class="text-[10px] font-mono text-slate-400">{{ $err->method }}</span>@endif
                                    @else
                                        <span class="text-slate-300">—</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex px-2 py-0.5 rounded-full text-[11px] font-bold {{ $statusClass }}">
                                        {{ \App\Models\PlatformErrorLog::statusLabel($err->status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <a href="{{ route('admin.platform-errors.show', $err) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700">
                                        <i class="fas fa-eye text-xs"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-16 text-center text-slate-500">
                                    <i class="fas fa-check-circle text-4xl text-emerald-300 mb-3"></i>
                                    <p class="font-semibold text-slate-700">لا توجد أخطاء مطابقة للفلتر</p>
                                    <p class="text-sm mt-1">عند حدوث أي خطأ في المنصة سيظهر هنا تلقائياً</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </form>

        @if($errorLogs->hasPages())
            <div class="px-5 py-4 border-t border-slate-100">{{ $errorLogs->links() }}</div>
        @endif
    </section>
</div>

@push('scripts')
<script>
    document.getElementById('select-all-errors')?.addEventListener('change', function () {
        document.querySelectorAll('.err-checkbox').forEach(cb => { cb.checked = this.checked; });
    });
</script>
@endpush
@endsection
