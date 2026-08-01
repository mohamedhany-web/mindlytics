@php
    $pipeline = $pipeline ?? app(\App\Services\SalesPipelineService::class);
    $suggested = $pipeline->suggestedNextStages($lead);
    $currentIdx = $lead->journeyIndex();
    $stages = \App\Models\SalesLead::STAGES;
@endphp
<div class="rounded-2xl border border-teal-200 bg-white shadow-sm overflow-hidden" x-data="pipelineForm(@js($suggested[0] ?? $lead->stage))">
    <div class="px-4 py-3 border-b border-teal-100 bg-teal-50/60 flex flex-wrap items-center justify-between gap-2">
        <div>
            <h3 class="font-black text-slate-900">Lead Status — رحلة العميل</h3>
            <p class="text-xs text-slate-600 mt-0.5">المرحلة الحالية: <strong>{{ \App\Models\SalesLead::stageLabel($lead->stage) }}</strong>
                @if($lead->contact_attempts)
                    · محاولات: {{ $lead->contact_attempts }}/3
                @endif
                @if($lead->next_attempt_due_at)
                    · المحاولة التالية: {{ $lead->next_attempt_due_at->format('Y-m-d H:i') }}
                @endif
            </p>
        </div>
    </div>

    <div class="p-4 overflow-x-auto">
        <ol class="flex gap-1 min-w-max">
            @foreach($stages as $key => $label)
                @php
                    $idx = array_search($key, array_keys($stages), true);
                    $done = $idx < $currentIdx;
                    $current = $key === $lead->stage;
                @endphp
                <li class="flex items-center gap-1">
                    <span class="inline-flex flex-col items-center w-[4.5rem] text-center">
                        <span @class([
                            'w-3 h-3 rounded-full border-2',
                            'bg-emerald-500 border-emerald-600' => $done || $current,
                            'bg-white border-slate-300' => ! $done && ! $current,
                        ])></span>
                        <span @class([
                            'text-[9px] mt-1 leading-tight font-semibold',
                            'text-emerald-800' => $current,
                            'text-slate-500' => ! $current,
                        ])>{{ $label }}</span>
                    </span>
                    @if(! $loop->last)
                        <span class="w-3 h-0.5 bg-slate-200 mb-4"></span>
                    @endif
                </li>
            @endforeach
        </ol>
    </div>

    @if($lead->isOpen() || $lead->stage === 'enrollment_completed')
    @unless(!empty($pipelineReadonly))
    <form method="post" action="{{ route('employee.sales.leads.pipeline', $lead) }}" class="p-4 border-t border-slate-100 space-y-3 bg-slate-50/40">
        @csrf
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div>
                <label class="block text-xs font-bold text-slate-600 mb-1">الانتقال إلى</label>
                <select name="stage" x-model="stage" required class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm font-semibold">
                    @foreach($suggested as $s)
                        <option value="{{ $s }}">{{ \App\Models\SalesLead::stageLabel($s) }}</option>
                    @endforeach
                    @foreach($stages as $k => $lab)
                        @if(! in_array($k, $suggested, true) && $k !== $lead->stage)
                            <option value="{{ $k }}">{{ $lab }}</option>
                        @endif
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-600 mb-1">ملاحظات / مدة المكالمة</label>
                <div class="flex gap-2">
                    <input type="number" name="duration_seconds" min="0" placeholder="ثواني" class="w-28 rounded-xl border border-slate-200 px-2 py-2 text-sm">
                    <input type="url" name="recording_url" placeholder="رابط التسجيل (اختياري)" class="flex-1 rounded-xl border border-slate-200 px-2 py-2 text-sm">
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3" x-show="stage === 'first_contact'" x-cloak>
            <div>
                <label class="block text-xs font-bold text-slate-600 mb-1">هل تم الرد؟ *</label>
                <select name="call_answered" class="w-full rounded-xl border border-amber-200 bg-amber-50/50 px-3 py-2 text-sm">
                    <option value="1">تم الرد</option>
                    <option value="0">لم يرد → No Answer</option>
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3" x-show="stage === 'connected'" x-cloak>
            <div>
                <label class="block text-xs font-bold text-slate-600 mb-1">نتيجة الاتصال *</label>
                <select name="connected_disposition" class="w-full rounded-xl border border-amber-200 bg-amber-50/50 px-3 py-2 text-sm">
                    <option value="">—</option>
                    @foreach(\App\Models\SalesLead::CONNECTED_DISPOSITIONS as $k => $lab)
                        <option value="{{ $k }}">{{ $lab }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3" x-show="stage === 'qualification'" x-cloak>
            <div>
                <label class="block text-xs font-bold mb-1">الحالة *</label>
                <select name="profile_type" class="w-full rounded-xl border border-amber-200 bg-amber-50/50 px-3 py-2 text-sm">
                    <option value="">—</option>
                    @foreach(\App\Models\SalesLead::PROFILE_TYPES as $k => $lab)
                        <option value="{{ $k }}" @selected($lead->profile_type === $k)>{{ $lab }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold mb-1">السن *</label>
                <input type="number" name="age" value="{{ $lead->age }}" class="w-full rounded-xl border border-amber-200 bg-amber-50/50 px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-xs font-bold mb-1">المجال *</label>
                <input type="text" name="field_domain" value="{{ $lead->field_domain }}" class="w-full rounded-xl border border-amber-200 bg-amber-50/50 px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-xs font-bold mb-1">مستوى الخبرة *</label>
                <input type="text" name="experience_level" value="{{ $lead->experience_level }}" class="w-full rounded-xl border border-amber-200 bg-amber-50/50 px-3 py-2 text-sm">
            </div>
            <div class="sm:col-span-2">
                <label class="block text-xs font-bold mb-1">لماذا يريد الكورس؟ *</label>
                <textarea name="course_motivation" rows="2" class="w-full rounded-xl border border-amber-200 bg-amber-50/50 px-3 py-2 text-sm">{{ $lead->course_motivation }}</textarea>
            </div>
            <div>
                <label class="block text-xs font-bold mb-1">متى يريد البدء؟ *</label>
                <input type="text" name="start_preference" value="{{ $lead->start_preference }}" class="w-full rounded-xl border border-amber-200 bg-amber-50/50 px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-xs font-bold mb-1">هل يستطيع الدفع؟ *</label>
                <select name="can_pay" class="w-full rounded-xl border border-amber-200 bg-amber-50/50 px-3 py-2 text-sm">
                    <option value="">—</option>
                    <option value="1" @selected($lead->can_pay === true)>نعم</option>
                    <option value="0" @selected($lead->can_pay === false)>لا</option>
                </select>
            </div>
        </div>

        <div x-show="stage === 'interested'" x-cloak>
            <label class="block text-xs font-bold mb-1">نسبة الاهتمام *</label>
            <select name="interest_pct" class="w-full rounded-xl border border-amber-200 bg-amber-50/50 px-3 py-2 text-sm">
                <option value="">—</option>
                @foreach(\App\Models\SalesLead::INTEREST_PCTS as $p)
                    <option value="{{ $p }}" @selected((int)$lead->interest_pct === $p)>{{ $p }}%</option>
                @endforeach
            </select>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3" x-show="stage === 'objection'" x-cloak>
            <div>
                <label class="block text-xs font-bold mb-1">سبب الاعتراض *</label>
                <select name="objection_reason" class="w-full rounded-xl border border-amber-200 bg-amber-50/50 px-3 py-2 text-sm">
                    <option value="">—</option>
                    @foreach(\App\Models\SalesLead::OBJECTION_REASONS as $k => $lab)
                        <option value="{{ $k }}">{{ $lab }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold mb-1">Notes</label>
                <input type="text" name="objection_notes" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3" x-show="stage === 'follow_up_scheduled'" x-cloak>
            <div>
                <label class="block text-xs font-bold mb-1">موعد المتابعة *</label>
                <input type="datetime-local" name="next_follow_up_at" class="w-full rounded-xl border border-amber-200 bg-amber-50/50 px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-xs font-bold mb-1">القناة *</label>
                <select name="follow_up_channel" class="w-full rounded-xl border border-amber-200 bg-amber-50/50 px-3 py-2 text-sm">
                    <option value="">—</option>
                    @foreach(\App\Models\SalesLead::FOLLOW_UP_CHANNELS as $k => $lab)
                        <option value="{{ $k }}">{{ $lab }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3" x-show="stage === 'offer_sent'" x-cloak>
            <div>
                <label class="block text-xs font-bold mb-1">السعر *</label>
                <input type="number" step="0.01" name="offer_price" value="{{ $lead->offer_price }}" class="w-full rounded-xl border border-amber-200 bg-amber-50/50 px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-xs font-bold mb-1">الخصم</label>
                <input type="text" name="offer_discount" value="{{ $lead->offer_discount }}" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-xs font-bold mb-1">خطة التقسيط</label>
                <input type="text" name="offer_installment_plan" value="{{ $lead->offer_installment_plan }}" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-xs font-bold mb-1">ملاحظات العرض</label>
                <input type="text" name="offer_notes" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm">
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3" x-show="stage === 'payment_pending'" x-cloak>
            <div>
                <label class="block text-xs font-bold mb-1">طريقة الدفع *</label>
                <select name="payment_method" class="w-full rounded-xl border border-amber-200 bg-amber-50/50 px-3 py-2 text-sm">
                    <option value="">—</option>
                    @foreach(\App\Models\SalesLead::PAYMENT_METHODS as $k => $lab)
                        <option value="{{ $k }}">{{ $lab }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold mb-1">القيمة *</label>
                <input type="number" step="0.01" name="payment_amount" class="w-full rounded-xl border border-amber-200 bg-amber-50/50 px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-xs font-bold mb-1">تاريخ الاستحقاق *</label>
                <input type="datetime-local" name="payment_due_at" class="w-full rounded-xl border border-amber-200 bg-amber-50/50 px-3 py-2 text-sm">
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3" x-show="stage === 'payment_received'" x-cloak>
            <div>
                <label class="block text-xs font-bold mb-1">رقم العملية *</label>
                <input type="text" name="payment_txn_ref" class="w-full rounded-xl border border-amber-200 bg-amber-50/50 px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-xs font-bold mb-1">المبلغ *</label>
                <input type="number" step="0.01" name="payment_amount" class="w-full rounded-xl border border-amber-200 bg-amber-50/50 px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-xs font-bold mb-1">تاريخ الدفع *</label>
                <input type="datetime-local" name="paid_at" class="w-full rounded-xl border border-amber-200 bg-amber-50/50 px-3 py-2 text-sm">
            </div>
        </div>

        <div x-show="stage === 'enrollment_completed'" x-cloak>
            <label class="block text-xs font-bold mb-1">قيمة الصفقة *</label>
            <input type="number" step="0.01" name="expected_value" value="{{ $lead->expected_value }}" class="w-full rounded-xl border border-amber-200 bg-amber-50/50 px-3 py-2 text-sm">
        </div>

        <div x-show="stage === 'lost'" x-cloak>
            <label class="block text-xs font-bold mb-1">سبب الخسارة *</label>
            <select name="lost_reason" class="w-full rounded-xl border border-rose-200 bg-rose-50/50 px-3 py-2 text-sm">
                <option value="">—</option>
                @foreach(\App\Models\SalesLead::LOSS_REASONS as $k => $lab)
                    <option value="{{ $k }}">{{ $lab }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-xs font-bold text-slate-600 mb-1">ملاحظات الانتقال</label>
            <textarea name="notes" rows="2" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm" placeholder="اختياري"></textarea>
        </div>

        <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-teal-600 hover:bg-teal-700 text-white text-sm font-bold">
            <i class="fas fa-route"></i> تحديث المرحلة
        </button>
    </form>
    @endunless
    @endif
</div>
<script>
function pipelineForm(initial) {
    return { stage: initial || 'first_contact' };
}
</script>
