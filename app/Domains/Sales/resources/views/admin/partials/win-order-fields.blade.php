{{-- ربط الصفقة بالطلب المدفوع (docs/SALES_INTEGRITY.md) --}}
@if(\App\Domains\Sales\Services\SalesLeadOrderLinker::ready())
    @if($lead->order_id && $lead->order)
        @php($netPaid = app(\App\Domains\Sales\Services\SalesLeadOrderLinker::class)->netPaid($lead->order))
        <div class="rounded-lg border border-emerald-300 bg-white px-2 py-1.5 text-[11px] text-emerald-800">
            <i class="fas fa-receipt"></i>
            طلب مدفوع
            @if(Route::has('admin.orders.show'))
                <a href="{{ route('admin.orders.show', $lead->order) }}" class="font-bold underline">#{{ $lead->order->id }}</a>
            @else
                <span class="font-bold">#{{ $lead->order->id }}</span>
            @endif
            — المدفوع فعلاً: <span class="font-bold tabular-nums">{{ number_format($netPaid, 2) }} ج.م</span>
            @if($netPaid < (float) $lead->order->amount)
                <span class="text-rose-700">(بعد استرداد)</span>
            @endif
        </div>
    @else
        <div class="rounded-lg border border-amber-300 bg-amber-50 px-2 py-1.5 text-[11px] text-amber-900 space-y-1.5">
            <p><i class="fas fa-triangle-exclamation"></i> لا يوجد طلب مدفوع مرتبط — اربطها برقم الطلب، أو اكتب سبب الاعتماد اليدوي.</p>
            <input type="number" min="1" name="order_id" class="{{ $inputClass }} !py-1.5 text-xs" placeholder="رقم الطلب (اختياري)">
            <input type="text" name="override_reason" maxlength="2000" class="{{ $inputClass }} !py-1.5 text-xs" placeholder="سبب الاعتماد بدون طلب (دفع كاش بالمقر مثلاً)">
        </div>
    @endif
@endif
