@php
    $pipeline = $pipeline ?? app(\App\Services\SalesPipelineService::class);
    $allowed = $pipeline->allowedNextStages($lead);
    $buckets = $pipeline->journeyBuckets();
    $currentBucket = $pipeline->bucketForStage($lead->stage);
    $bucketKeys = array_keys($buckets);
    $currentBucketIdx = array_search($currentBucket, $bucketKeys, true);
    $hasCourse = (bool) $lead->linkedCourseId();
@endphp
<div class="rounded-2xl border border-teal-200 bg-white shadow-sm overflow-hidden" x-data="pipelineForm(@js($allowed[0] ?? $lead->stage))">
    <div class="px-4 py-3 border-b border-teal-100 bg-teal-50/60 flex flex-wrap items-center justify-between gap-2">
        <div>
            <h3 class="font-black text-slate-900">Lead Status — رحلة العميل</h3>
            <p class="text-xs text-slate-600 mt-0.5">المرحلة: <strong>{{ \App\Models\SalesLead::stageLabel($lead->stage) }}</strong>
                · المسار: <strong>{{ $buckets[$currentBucket] ?? '—' }}</strong>
                @if($lead->contact_attempts)
                    · محاولات: {{ $lead->contact_attempts }}/3
                @endif
            </p>
            <p class="text-[11px] text-teal-800 mt-1 font-semibold">مسار مختصر: يمكن تخطّي الخطوات الدقيقة داخل التواصل/التأهيل — ملاحظات قصيرة إلزامية — قبل الحجز لازم كورس</p>
        </div>
    </div>

    <div class="p-4">
        <ol class="flex flex-wrap gap-2">
            @foreach($buckets as $bKey => $bLabel)
                @php
                    $bIdx = array_search($bKey, $bucketKeys, true);
                    $done = is_int($currentBucketIdx) && is_int($bIdx) && $bIdx < $currentBucketIdx;
                    $current = $bKey === $currentBucket;
                @endphp
                <li @class([
                    'inline-flex items-center gap-2 rounded-xl border px-3 py-2 text-xs font-bold',
                    'border-emerald-300 bg-emerald-50 text-emerald-900' => $done || $current,
                    'border-slate-200 bg-slate-50 text-slate-500' => ! $done && ! $current,
                ])>
                    <span @class([
                        'w-2.5 h-2.5 rounded-full',
                        'bg-emerald-500' => $done || $current,
                        'bg-slate-300' => ! $done && ! $current,
                    ])></span>
                    {{ $bLabel }}
                </li>
            @endforeach
        </ol>
    </div>

    @if($lead->isOpen() || $lead->stage === 'enrollment_completed')
    @unless(!empty($pipelineReadonly))
    <form method="post" action="{{ route('employee.sales.leads.pipeline', $lead) }}" class="p-4 border-t border-slate-100 space-y-3 bg-slate-50/40">
        @csrf
        @if($errors->any())
            <div class="rounded-xl bg-rose-50 border border-rose-200 text-rose-800 px-3 py-2 text-xs">
                <ul class="list-disc list-inside space-y-0.5">
                    @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                </ul>
            </div>
        @endif

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div>
                <label class="block text-xs font-bold text-slate-600 mb-1">الخطوة التالية المسموحة</label>
                @if($allowed === [])
                    <p class="text-sm text-slate-500">لا انتقالات متاحة من هذه المرحلة.</p>
                @else
                    <select name="stage" x-model="stage" required class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm font-semibold">
                        @foreach($allowed as $s)
                            <option value="{{ $s }}">{{ \App\Models\SalesLead::stageLabel($s) }}</option>
                        @endforeach
                    </select>
                @endif
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-600 mb-1">مدة المكالمة / تسجيل</label>
                <div class="flex gap-2">
                    <input type="number" name="duration_seconds" min="0" placeholder="ثواني" class="w-28 rounded-xl border border-slate-200 px-2 py-2 text-sm">
                    <input type="url" name="recording_url" placeholder="رابط التسجيل (اختياري)" class="flex-1 rounded-xl border border-slate-200 px-2 py-2 text-sm">
                </div>
            </div>
        </div>

        <div>
            <label class="block text-xs font-bold text-slate-600 mb-1">ملاحظات الانتقال <span class="text-rose-600">*</span></label>
            <textarea name="notes" rows="2" required minlength="5" class="w-full rounded-xl border border-amber-200 bg-amber-50/40 px-3 py-2 text-sm" placeholder="ماذا حصل؟ وما التالي؟ (5 أحرف على الأقل)">{{ old('notes') }}</textarea>
            @error('notes')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
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
            <div class="sm:col-span-2 rounded-lg border border-slate-200 bg-white px-3 py-2 text-[11px] text-slate-600">
                حقول التأهيل <strong>اختيارية</strong> — املأ المتاح فقط. ملاحظات الانتقال بالأعلى إلزامية.
            </div>
            <div>
                <label class="block text-xs font-bold mb-1">الحالة <span class="text-slate-400 font-normal">(اختياري)</span></label>
                <select name="profile_type" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm">
                    <option value="">—</option>
                    @foreach(\App\Models\SalesLead::PROFILE_TYPES as $k => $lab)
                        <option value="{{ $k }}" @selected($lead->profile_type === $k)>{{ $lab }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold mb-1">السن (مدى) <span class="text-slate-400 font-normal">(اختياري)</span></label>
                <select name="age_range" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm">
                    <option value="">— اختر المدى —</option>
                    @foreach(\App\Models\SalesLead::AGE_RANGES as $k => $lab)
                        <option value="{{ $k }}" @selected(old('age_range', $lead->age_range) === $k)>{{ $lab }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold mb-1">المجال <span class="text-slate-400 font-normal">(اختياري)</span></label>
                <input type="text" name="field_domain" value="{{ $lead->field_domain }}" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-xs font-bold mb-1">مستوى الخبرة <span class="text-slate-400 font-normal">(اختياري)</span></label>
                <input type="text" name="experience_level" value="{{ $lead->experience_level }}" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm">
            </div>
            <div class="sm:col-span-2">
                <label class="block text-xs font-bold mb-1">لماذا يريد الكورس؟ <span class="text-slate-400 font-normal">(اختياري)</span></label>
                <textarea name="course_motivation" rows="2" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm">{{ $lead->course_motivation }}</textarea>
            </div>
            <div>
                <label class="block text-xs font-bold mb-1">متى يريد البدء؟ <span class="text-slate-400 font-normal">(اختياري)</span></label>
                <input type="text" name="start_preference" value="{{ $lead->start_preference }}" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-xs font-bold mb-1">هل يستطيع الدفع؟ <span class="text-slate-400 font-normal">(اختياري)</span></label>
                <select name="can_pay" class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm">
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

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 rounded-xl border border-teal-200 bg-teal-50/40 p-3"
             x-show="stage && !['lost','dormant','enrollment_completed'].includes(stage)" x-cloak>
            <div class="sm:col-span-2">
                <p class="text-xs font-black text-teal-900">حركة إلزامية — Status + Next Action + موعد</p>
                <p class="text-[11px] text-teal-800/80">ممنوع Lead مفتوح بدون إجراء تالي وموعد متابعة.</p>
            </div>
            <div>
                <label class="block text-xs font-bold mb-1">موعد المتابعة *</label>
                <input type="datetime-local" name="next_follow_up_at"
                       value="{{ old('next_follow_up_at', $lead->next_follow_up_at && $lead->next_follow_up_at->isFuture() ? $lead->next_follow_up_at->format('Y-m-d\TH:i') : now()->addDay()->setTime(10, 0)->format('Y-m-d\TH:i')) }}"
                       class="w-full rounded-xl border border-teal-200 bg-white px-3 py-2 text-sm">
            </div>
            <div>
                <label class="block text-xs font-bold mb-1">الإجراء التالي (Next Action) *</label>
                <select name="follow_up_channel" class="w-full rounded-xl border border-teal-200 bg-white px-3 py-2 text-sm">
                    <option value="">— اختر —</option>
                    @foreach(\App\Models\SalesLead::FOLLOW_UP_CHANNELS as $k => $lab)
                        <option value="{{ $k }}" @selected(old('follow_up_channel', $lead->follow_up_channel) === $k)>{{ $lab }}</option>
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

        <div class="rounded-xl border border-violet-200 bg-violet-50/50 p-3 space-y-2"
             x-show="['payment_pending','payment_received','enrollment_completed'].includes(stage)" x-cloak>
            <p class="text-xs font-black text-violet-950">ربط كورس / دبلومة قبل الحجز <span class="text-rose-600">*</span></p>
            @if($hasCourse)
                <p class="text-xs text-emerald-800 font-semibold">مرتبط حالياً: {{ $lead->linkedCourseTitle() }} ({{ $lead->linkedCourseTypeLabel() }})</p>
            @else
                <p class="text-xs text-rose-700 font-semibold">غير مربوط — اختر كورساً أو دبلومة من الكتالوج بالأسفل.</p>
            @endif
            @include('sales._course_picker', [
                'lead' => $lead,
                'coursesCatalogUrl' => route('employee.sales.courses.index'),
                'inputClass' => 'w-full rounded-xl border border-violet-200 bg-white px-3 py-2 text-sm',
                'labelClass' => 'block text-xs font-bold text-violet-900 mb-1',
            ])
            @error('course_ref_id')<p class="text-xs text-rose-600">{{ $message }}</p>@enderror
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

        <button type="submit" @disabled($allowed === []) class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-teal-600 hover:bg-teal-700 disabled:opacity-50 text-white text-sm font-bold">
            <i class="fas fa-route"></i> تحديث للمرحلة التالية
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
