@extends('layouts.employee')

@section('title', 'طلب فيديو: ' . $montageRequest->title)
@section('header', 'تفاصيل طلب محرر الفيديو')

@php
    use App\Models\ModeratorMontageRequest;

    $step = match ($montageRequest->status) {
        ModeratorMontageRequest::STATUS_PENDING, ModeratorMontageRequest::STATUS_IN_PROGRESS => 1,
        ModeratorMontageRequest::STATUS_SUBMITTED => 2,
        ModeratorMontageRequest::STATUS_MODERATOR_DELIVERY_PENDING => 3,
        ModeratorMontageRequest::STATUS_COMPLETED => 4,
        ModeratorMontageRequest::STATUS_CANCELLED => 0,
        default => 1,
    };

    $fmtBytes = function (?int $bytes): string {
        if ($bytes === null || $bytes <= 0) {
            return '—';
        }
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        $v = (float) $bytes;
        while ($v >= 1024 && $i < count($units) - 1) {
            $v /= 1024;
            $i++;
        }

        return number_format($v, $i > 0 ? 1 : 0).' '.$units[$i];
    };
@endphp

@section('content')
<div class="w-full max-w-none space-y-8">
    @if(session('success'))
        <div class="rounded-xl bg-emerald-50 border border-emerald-200 px-4 py-3 text-emerald-800 text-sm font-semibold">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="rounded-xl bg-rose-50 border border-rose-200 px-4 py-3 text-rose-800 text-sm font-semibold">{{ session('error') }}</div>
    @endif
    @if($errors->any())
        <div class="rounded-xl border border-red-200 bg-red-50 text-red-800 px-4 py-3 text-sm">{{ $errors->first() }}</div>
    @endif

    <div class="flex flex-wrap items-center gap-2">
        <a href="{{ route('employee.montage-requests.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-sm font-semibold text-slate-800">
            <i class="fas fa-arrow-right"></i> القائمة
        </a>
        @if($montageRequest->employeeTask)
            <a href="{{ route('employee.tasks.show', $montageRequest->employeeTask) }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-cyan-100 hover:bg-cyan-200 text-sm font-semibold text-cyan-900">
                <i class="fas fa-film"></i> مهمة محرر الفيديو
            </a>
        @endif
        @if($montageRequest->moderatorDeliveryTask)
            <a href="{{ route('employee.tasks.show', $montageRequest->moderatorDeliveryTask) }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-emerald-100 hover:bg-emerald-200 text-sm font-semibold text-emerald-900">
                <i class="fas fa-upload"></i> مهمة تسليمك النهائي
            </a>
        @endif
    </div>

    @if($step > 0)
        <div class="rounded-2xl border border-gray-200 bg-white p-5 sm:p-6 shadow-sm">
            <h3 class="text-sm font-bold text-gray-500 uppercase tracking-wide mb-4">مسار عملك على هذا الطلب</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                @foreach([
                    1 => ['label' => 'انتظار/تنفيذ المحرر', 'icon' => 'fa-user-clock'],
                    2 => ['label' => 'مراجعة التسليمات', 'icon' => 'fa-eye'],
                    3 => ['label' => 'تسليمك النهائي', 'icon' => 'fa-paper-plane'],
                    4 => ['label' => 'مكتملة', 'icon' => 'fa-check-circle'],
                ] as $n => $meta)
                    <div class="relative rounded-xl border-2 px-3 py-3 text-center transition
                        {{ $step === $n ? 'border-cyan-500 bg-cyan-50 shadow-md' : ($step > $n ? 'border-emerald-200 bg-emerald-50/50' : 'border-gray-100 bg-gray-50/80 opacity-80') }}">
                        <div class="text-lg mb-1 {{ $step >= $n ? 'text-cyan-700' : 'text-gray-400' }}">
                            <i class="fas {{ $meta['icon'] }}"></i>
                        </div>
                        <p class="text-xs font-bold leading-snug text-gray-800">{{ $meta['label'] }}</p>
                        @if($step > $n)
                            <span class="absolute top-2 left-2 text-emerald-600 text-xs"><i class="fas fa-check"></i></span>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm space-y-4 w-full">
        <div class="flex flex-wrap items-center justify-between gap-2">
            <h2 class="text-xl font-black text-gray-900">{{ $montageRequest->title }}</h2>
            <span class="inline-flex px-3 py-1 rounded-xl text-xs font-bold bg-cyan-50 text-cyan-800 border border-cyan-100">
                {{ $montageRequest->statusLabel() }}
            </span>
        </div>
        <dl class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 text-sm">
            <div class="rounded-xl bg-slate-50 px-4 py-3 border border-slate-100">
                <dt class="text-gray-500 text-xs font-semibold mb-1">محرر الفيديو</dt>
                <dd class="font-bold text-gray-900">{{ $montageRequest->montageEmployee->name ?? '—' }}</dd>
            </div>
            <div class="rounded-xl bg-slate-50 px-4 py-3 border border-slate-100">
                <dt class="text-gray-500 text-xs font-semibold mb-1">حد التسليم</dt>
                <dd class="font-bold {{ $montageRequest->deadline_at && $montageRequest->deadline_at->isPast() && $montageRequest->isOpen() ? 'text-rose-600' : 'text-gray-900' }}">
                    {{ $montageRequest->deadline_at?->format('Y-m-d H:i') }}
                </dd>
            </div>
            <div class="rounded-xl bg-slate-50 px-4 py-3 border border-slate-100">
                <dt class="text-gray-500 text-xs font-semibold mb-1">الأولوية</dt>
                <dd class="font-bold text-gray-900">{{ ModeratorMontageRequest::priorityLabel($montageRequest->priority) }}</dd>
            </div>
            <div class="rounded-xl bg-slate-50 px-4 py-3 border border-slate-100">
                <dt class="text-gray-500 text-xs font-semibold mb-1">تسليم المحرر (أول مرة)</dt>
                <dd class="font-bold text-gray-900">{{ $montageRequest->submitted_at?->format('Y-m-d H:i') ?? '—' }}</dd>
            </div>
        </dl>
        @if($montageRequest->description)
            <div>
                <p class="text-xs font-semibold text-gray-500 mb-1">الوصف</p>
                <p class="text-gray-800 whitespace-pre-wrap text-sm">{{ $montageRequest->description }}</p>
            </div>
        @endif
        <div>
            <p class="text-xs font-semibold text-gray-500 mb-1">متطلبات الفيديو</p>
            <p class="text-gray-800 whitespace-pre-wrap text-sm leading-relaxed">{{ $montageRequest->requirements }}</p>
        </div>
    </div>

    <div class="rounded-2xl border border-gray-200 bg-white shadow-sm overflow-hidden w-full">
        <div class="px-5 py-4 border-b border-gray-100 flex flex-wrap items-center justify-between gap-2 bg-gradient-to-l from-cyan-50 to-sky-50">
            <div>
                <h3 class="text-lg font-black text-gray-900">جدول التسليمات</h3>
                <p class="text-xs text-gray-600 mt-0.5">كل ما رُفع من محرر الفيديو ومنك</p>
            </div>
            <span class="text-xs font-bold px-3 py-1 rounded-full bg-white/80 border border-cyan-100 text-cyan-800">{{ $deliverablesTimeline->count() }} تسليم</span>
        </div>
        <div class="overflow-x-auto">
            @if($deliverablesTimeline->isEmpty())
                <div class="p-10 text-center text-gray-500 text-sm">
                    <i class="fas fa-inbox text-3xl text-gray-300 mb-3 block"></i>
                    لا توجد تسليمات بعد.
                </div>
            @else
                <table class="min-w-[900px] w-full text-sm">
                    <thead class="bg-slate-800 text-white text-xs uppercase tracking-wide">
                        <tr>
                            <th class="text-right px-4 py-3 font-bold">المصدر</th>
                            <th class="text-right px-4 py-3 font-bold">العنوان</th>
                            <th class="text-right px-4 py-3 font-bold">النوع</th>
                            <th class="text-right px-4 py-3 font-bold">معاينة / الوصول</th>
                            <th class="text-right px-4 py-3 font-bold">الحجم</th>
                            <th class="text-right px-4 py-3 font-bold">التاريخ</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($deliverablesTimeline as $row)
                            @php $d = $row['deliverable']; @endphp
                            <tr class="hover:bg-slate-50/80">
                                <td class="px-4 py-3">
                                    <span class="inline-flex px-2 py-0.5 rounded-lg text-xs font-bold {{ $row['source'] === 'editor' ? 'bg-cyan-100 text-cyan-800' : 'bg-emerald-100 text-emerald-800' }}">
                                        {{ $row['source_label'] }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 font-semibold text-gray-900">{{ $d->title }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $d->delivery_type }}</td>
                                <td class="px-4 py-3">
                                    @if($d->delivery_type === 'link' && $d->link_url)
                                        <a href="{{ $d->link_url }}" target="_blank" rel="noopener" class="text-cyan-700 font-semibold hover:underline text-xs">
                                            <i class="fas fa-external-link-alt ml-1"></i>فتح الرابط
                                        </a>
                                    @elseif($d->file_path)
                                        <a href="{{ $d->publicFileUrl() }}" target="_blank" rel="noopener" class="text-violet-700 font-semibold hover:underline text-xs">
                                            <i class="fas fa-download ml-1"></i>{{ $d->file_name ?: 'تحميل' }}
                                        </a>
                                    @else
                                        <span class="text-gray-400 text-xs">—</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-xs font-mono text-gray-700">{{ $fmtBytes($d->file_size) }}</td>
                                <td class="px-4 py-3 text-xs text-gray-600">{{ $d->created_at?->format('Y-m-d H:i') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>

    @if($montageRequest->status === ModeratorMontageRequest::STATUS_SUBMITTED && ! $montageRequest->moderator_delivery_task_id)
        <div class="rounded-2xl border border-emerald-200 bg-emerald-50/50 p-6 shadow-sm space-y-4 w-full">
            <h3 class="font-black text-emerald-950 text-lg">الخطوة التالية: تسليمك النهائي</h3>
            <p class="text-sm text-emerald-900/90">بعد مراجعة جدول التسليمات أعلاه، أنشئ مهمة التسليم النهائي ثم ارفع من «مهامي» وأكمل المهمة.</p>
            <form method="post" action="{{ route('employee.montage-requests.moderator-delivery.store', $montageRequest) }}" class="grid grid-cols-1 md:grid-cols-2 gap-4 max-w-3xl">
                @csrf
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-1">ملاحظات التسليم (اختياري)</label>
                    <textarea name="delivery_notes" rows="2" class="w-full rounded-xl border-gray-200 px-4 py-2">{{ old('delivery_notes') }}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">موعد تسليمك النهائي</label>
                    <input type="date" name="deadline" value="{{ old('deadline') }}" class="w-full rounded-xl border-gray-200 px-4 py-2">
                    <p class="text-xs text-gray-500 mt-1">إن تركتها فارغة يُضبط تلقائياً بعد 3 أيام.</p>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full md:w-auto px-8 py-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-black text-sm shadow-lg">
                        إنشاء مهمة التسليم النهائي
                    </button>
                </div>
            </form>
        </div>
    @endif

    @if($montageRequest->isOpen())
        <form method="post" action="{{ route('employee.montage-requests.cancel', $montageRequest) }}" onsubmit="return confirm('إلغاء طلب الفيديو؟');" class="inline">
            @csrf
            <button type="submit" class="text-sm text-red-600 hover:text-red-800 font-semibold underline">إلغاء الطلب</button>
        </form>
    @endif
</div>
@endsection
