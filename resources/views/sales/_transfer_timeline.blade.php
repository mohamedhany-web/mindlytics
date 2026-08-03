{{-- Shared transfer / ownership timeline --}}
@php
    $transfers = $lead->relationLoaded('transfers')
        ? $lead->transfers->sortBy('created_at')
        : $lead->transfers()->with(['fromUser', 'toUser', 'transferredBy'])->orderBy('created_at')->get();
@endphp
<section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden {{ $wrapperClass ?? '' }}">
    <div class="px-4 py-3 border-b border-slate-200 bg-slate-50">
        <h3 class="text-base font-black text-slate-900 flex items-center gap-2">
            <i class="fas fa-route text-sky-600"></i>
            التسجيل والتحويلات
        </h3>
        <p class="text-xs text-slate-600 mt-0.5">من سجّل العميل، ومن حُوِّل إليه عبر الزمن.</p>
    </div>
    <div class="p-4 space-y-3">
        <div class="flex gap-3 items-start">
            <div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center flex-shrink-0">
                <i class="fas fa-user-plus text-xs"></i>
            </div>
            <div>
                <p class="text-sm font-bold text-slate-900">تسجيل العميل</p>
                <p class="text-xs text-slate-600">
                    سجّله: <strong>{{ $lead->creator?->name ?? '—' }}</strong>
                    · {{ $lead->created_at?->format('Y-m-d H:i') }}
                </p>
                @if($lead->interestType)
                    <p class="text-[11px] text-slate-500 mt-0.5">الاهتمام: {{ $lead->interestType->name_ar }}</p>
                @endif
            </div>
        </div>

        @forelse($transfers as $tr)
            <div class="flex gap-3 items-start border-t border-slate-100 pt-3">
                <div class="w-8 h-8 rounded-full bg-sky-100 text-sky-700 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-exchange-alt text-xs"></i>
                </div>
                <div>
                    <p class="text-sm font-bold text-slate-900">
                        {{ $tr->fromUser?->name ?? 'غير معيّن' }}
                        <i class="fas fa-arrow-left mx-1 text-slate-400 text-[10px]"></i>
                        {{ $tr->toUser?->name ?? '—' }}
                    </p>
                    <p class="text-xs text-slate-600">
                        بواسطة: <strong>{{ $tr->transferredBy?->name ?? '—' }}</strong>
                        · {{ $tr->created_at?->format('Y-m-d H:i') }}
                        @if($tr->source)
                            · <span class="font-mono text-[10px]">{{ $tr->source }}</span>
                        @endif
                    </p>
                    @if($tr->reason)
                        <p class="text-[11px] text-slate-500 mt-0.5">{{ $tr->reason }}</p>
                    @endif
                </div>
            </div>
        @empty
            <p class="text-xs text-slate-500 border-t border-slate-100 pt-3">لا توجد تحويلات بعد — العميل ما زال عند المعيَّن الحالي: <strong>{{ $lead->assignee?->name ?? '—' }}</strong></p>
        @endforelse
    </div>
</section>
