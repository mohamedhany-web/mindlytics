@php
    $pipeline = $pipeline ?? app(\App\Services\SalesPipelineService::class);
    $allowed = $pipeline->allowedNextStages($lead);
    $buckets = $pipeline->journeyBuckets();
    $currentBucket = $pipeline->bucketForStage($lead->stage);
    $bucketKeys = array_keys($buckets);
    $currentBucketIdx = array_search($currentBucket, $bucketKeys, true);
    $hasCourse = (bool) $lead->linkedCourseId();
    $exitStages = ['lost', 'dormant'];
    $forwardStages = array_values(array_filter($allowed, fn ($s) => ! in_array($s, $exitStages, true)));
    $closeStages = array_values(array_filter($allowed, fn ($s) => in_array($s, $exitStages, true)));
    $defaultStage = $forwardStages[0] ?? ($allowed[0] ?? $lead->stage);
    $input = 'w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500';
    $inputReq = 'w-full rounded-lg border border-amber-200 bg-amber-50/40 px-3 py-2 text-sm focus:ring-2 focus:ring-teal-500/20 focus:border-teal-500';
@endphp
@once
<style>
    .pipeline-wrap { background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden; }
    .pipeline-wrap .panel-card-head { background: #f8fafc; border-bottom: 1px solid #e2e8f0; }
    [x-cloak] { display: none !important; }
</style>
@endonce
<div class="pipeline-wrap panel-card overflow-hidden" x-data="pipelineForm(@js($defaultStage))">
    <div class="panel-card-head px-4 sm:px-5 py-3 flex flex-wrap items-center justify-between gap-2">
        <div class="min-w-0">
            <h3 class="font-bold text-slate-900">رحلة العميل</h3>
            <p class="text-xs text-slate-500 mt-0.5">
                الآن: <strong class="text-teal-800">{{ \App\Models\SalesLead::stageLabel($lead->stage) }}</strong>
                @if($lead->contact_attempts)
                    · محاولات {{ $lead->contact_attempts }}/3
                @endif
            </p>
        </div>
    </div>

    <div class="px-3 sm:px-5 py-4 border-b border-slate-100">
        <ol class="grid grid-cols-4 sm:grid-cols-7 gap-1.5">
            @foreach($buckets as $bKey => $bLabel)
                @php
                    $bIdx = array_search($bKey, $bucketKeys, true);
                    $done = is_int($currentBucketIdx) && is_int($bIdx) && $bIdx < $currentBucketIdx;
                    $current = $bKey === $currentBucket;
                    $isLost = $bKey === 'lost';
                @endphp
                <li class="min-w-0">
                    <div @class([
                        'flex flex-col items-center text-center rounded-xl border px-1.5 py-2.5',
                        'border-emerald-300 bg-emerald-50' => $done && ! $isLost,
                        'border-teal-500 bg-teal-600 text-white shadow-sm' => $current && ! $isLost,
                        'border-rose-300 bg-rose-50' => $current && $isLost,
                        'border-slate-200 bg-slate-50' => ! $done && ! $current,
                    ])>
                        <span @class([
                            'w-6 h-6 rounded-full text-[11px] font-black flex items-center justify-center mb-1',
                            'bg-emerald-500 text-white' => $done && ! $isLost,
                            'bg-white text-teal-700' => $current && ! $isLost,
                            'bg-rose-500 text-white' => $current && $isLost,
                            'bg-slate-200 text-slate-500' => ! $done && ! $current,
                        ])>
                            @if($done && ! $current)
                                <i class="fas fa-check text-[10px]"></i>
                            @else
                                {{ $loop->iteration }}
                            @endif
                        </span>
                        <span @class([
                            'text-[11px] sm:text-xs font-bold leading-tight',
                            'text-white' => $current && ! $isLost,
                            'text-emerald-800' => $done && ! $current,
                            'text-rose-800' => $current && $isLost,
                            'text-slate-500' => ! $done && ! $current,
                        ])>{{ $bLabel }}</span>
                    </div>
                </li>
            @endforeach
        </ol>
    </div>

    @if(($lead->isOpen() || $lead->stage === 'enrollment_completed') && empty($pipelineReadonly))
    <form method="post" action="{{ route('employee.sales.leads.pipeline', $lead) }}" class="p-4 sm:p-5 space-y-4">
        @csrf
        @if($errors->any())
            <div class="rounded-lg bg-rose-50 border border-rose-200 text-rose-800 px-3 py-2 text-xs">
                <ul class="list-disc list-inside space-y-0.5">
                    @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                </ul>
            </div>
        @endif

        @if($allowed === [])
            <p class="text-sm text-slate-500">لا انتقالات متاحة من هذه المرحلة.</p>
        @else
            <div>
                <p class="text-xs font-bold text-slate-600 mb-2">اختر الخطوة التالية</p>
                <input type="hidden" name="stage" value="{{ $defaultStage }}" x-model="stage">
                <div class="flex flex-wrap gap-2">
                    @foreach($forwardStages as $s)
                        <button type="button" @click="stage = '{{ $s }}'"
                                :class="stage === '{{ $s }}'
                                    ? 'bg-slate-800 text-white border-slate-800'
                                    : 'bg-white text-slate-700 border-slate-200 hover:border-slate-400'"
                                class="inline-flex items-center px-3 py-2 rounded-lg border text-sm font-semibold transition-colors">
                            {{ \App\Models\SalesLead::stageLabel($s) }}
                        </button>
                    @endforeach
                    @foreach($closeStages as $s)
                        <button type="button" @click="stage = '{{ $s }}'"
                                :class="stage === '{{ $s }}'
                                    ? 'bg-rose-600 text-white border-rose-600'
                                    : 'bg-white text-rose-700 border-rose-200 hover:border-rose-400'"
                                class="inline-flex items-center px-3 py-2 rounded-lg border text-sm font-semibold transition-colors">
                            {{ \App\Models\SalesLead::stageLabel($s) }}
                        </button>
                    @endforeach
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold text-slate-600 mb-1">ماذا حصل؟ <span class="text-rose-600">*</span></label>
                <textarea name="notes" rows="3" required minlength="8" class="{{ $inputReq }}" placeholder="ماذا حصل؟ وما الخطوة التالية؟ (8 أحرف على الأقل)">{{ old('notes') }}</textarea>
                @error('notes')<p class="text-xs text-rose-600 mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="space-y-3" x-show="stage && !['lost','dormant','enrollment_completed'].includes(stage)" x-cloak>
                <p class="text-xs font-bold text-slate-600">المتابعة التالية</p>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">الموعد *</label>
                    <input type="datetime-local" name="next_follow_up_at"
                           value="{{ old('next_follow_up_at', $lead->next_follow_up_at && $lead->next_follow_up_at->isFuture() ? $lead->next_follow_up_at->format('Y-m-d\TH:i') : now()->addDay()->setTime(10, 0)->format('Y-m-d\TH:i')) }}"
                           class="{{ $input }}">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1">الإجراء *</label>
                    <select name="follow_up_channel" class="{{ $input }}">
                        <option value="">— اختر —</option>
                        @foreach(\App\Models\SalesLead::FOLLOW_UP_CHANNELS as $k => $lab)
                            <option value="{{ $k }}" @selected(old('follow_up_channel', $lead->follow_up_channel) === $k)>{{ $lab }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3" x-show="['first_contact','connected','no_answer'].includes(stage)" x-cloak>
            <div>
                <label class="block text-xs font-bold text-slate-600 mb-1">مدة المكالمة (ثواني)</label>
                <input type="number" name="duration_seconds" min="0" placeholder="اختياري" class="{{ $input }}">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-600 mb-1">رابط التسجيل</label>
                <input type="url" name="recording_url" placeholder="اختياري" class="{{ $input }}">
            </div>
        </div>

        <div x-show="stage === 'first_contact'" x-cloak>
            <label class="block text-xs font-bold text-slate-600 mb-1">هل تم الرد؟ *</label>
            <select name="call_answered" class="{{ $inputReq }} max-w-xs">
                <option value="1">تم الرد</option>
                <option value="0">لم يرد → No Answer</option>
            </select>
        </div>

        <div x-show="stage === 'connected'" x-cloak>
            <label class="block text-xs font-bold text-slate-600 mb-1">نتيجة الاتصال *</label>
            <select name="connected_disposition" class="{{ $inputReq }} max-w-md">
                <option value="">—</option>
                @foreach(\App\Models\SalesLead::CONNECTED_DISPOSITIONS as $k => $lab)
                    <option value="{{ $k }}">{{ $lab }}</option>
                @endforeach
            </select>
        </div>

        <div x-show="stage === 'qualification'" x-cloak>
            <details class="rounded-lg border border-slate-200 bg-slate-50/60 px-4 py-3">
                <summary class="text-xs font-bold text-slate-700 cursor-pointer">حقول التأهيل (اختياري)</summary>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mt-3">
                    <div>
                        <label class="block text-xs font-semibold mb-1">الحالة</label>
                        <select name="profile_type" class="{{ $input }}">
                            <option value="">—</option>
                            @foreach(\App\Models\SalesLead::PROFILE_TYPES as $k => $lab)
                                <option value="{{ $k }}" @selected($lead->profile_type === $k)>{{ $lab }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold mb-1">السن</label>
                        <select name="age_range" class="{{ $input }}">
                            <option value="">—</option>
                            @foreach(\App\Models\SalesLead::AGE_RANGES as $k => $lab)
                                <option value="{{ $k }}" @selected(old('age_range', $lead->age_range) === $k)>{{ $lab }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold mb-1">المجال</label>
                        <input type="text" name="field_domain" value="{{ $lead->field_domain }}" class="{{ $input }}">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold mb-1">مستوى الخبرة</label>
                        <input type="text" name="experience_level" value="{{ $lead->experience_level }}" class="{{ $input }}">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold mb-1">لماذا يريد الكورس؟</label>
                        <textarea name="course_motivation" rows="2" class="{{ $input }}">{{ $lead->course_motivation }}</textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold mb-1">متى يريد البدء؟</label>
                        <input type="text" name="start_preference" value="{{ $lead->start_preference }}" class="{{ $input }}">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold mb-1">هل يستطيع الدفع؟</label>
                        <select name="can_pay" class="{{ $input }}">
                            <option value="">—</option>
                            <option value="1" @selected($lead->can_pay === true)>نعم</option>
                            <option value="0" @selected($lead->can_pay === false)>لا</option>
                        </select>
                    </div>
                </div>
            </details>
        </div>

        <div x-show="stage === 'interested'" x-cloak>
            <label class="block text-xs font-bold mb-1">نسبة الاهتمام *</label>
            <select name="interest_pct" class="{{ $inputReq }} max-w-xs">
                <option value="">—</option>
                @foreach(\App\Models\SalesLead::INTEREST_PCTS as $p)
                    <option value="{{ $p }}" @selected((int) $lead->interest_pct === $p)>{{ $p }}%</option>
                @endforeach
            </select>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3" x-show="stage === 'objection'" x-cloak>
            <div>
                <label class="block text-xs font-bold mb-1">سبب الاعتراض *</label>
                <select name="objection_reason" class="{{ $inputReq }}">
                    <option value="">—</option>
                    @foreach(\App\Models\SalesLead::OBJECTION_REASONS as $k => $lab)
                        <option value="{{ $k }}">{{ $lab }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold mb-1">ملاحظة</label>
                <input type="text" name="objection_notes" class="{{ $input }}">
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3" x-show="stage === 'offer_sent'" x-cloak>
            <div>
                <label class="block text-xs font-bold mb-1">السعر *</label>
                <input type="number" step="0.01" name="offer_price" value="{{ $lead->offer_price }}" class="{{ $inputReq }}">
            </div>
            <div>
                <label class="block text-xs font-bold mb-1">الخصم</label>
                <input type="text" name="offer_discount" value="{{ $lead->offer_discount }}" class="{{ $input }}">
            </div>
            <div>
                <label class="block text-xs font-bold mb-1">خطة التقسيط</label>
                <input type="text" name="offer_installment_plan" value="{{ $lead->offer_installment_plan }}" class="{{ $input }}">
            </div>
            <div>
                <label class="block text-xs font-bold mb-1">ملاحظات العرض</label>
                <input type="text" name="offer_notes" class="{{ $input }}">
            </div>
        </div>

        <div class="rounded-lg border border-violet-200 bg-violet-50/50 p-3 space-y-2"
             x-show="['payment_pending','payment_received','enrollment_completed'].includes(stage)" x-cloak>
            <p class="text-xs font-bold text-violet-950">ربط كورس قبل الحجز <span class="text-rose-600">*</span></p>
            @if($hasCourse)
                <p class="text-xs text-emerald-800 font-semibold">مرتبط: {{ $lead->linkedCourseTitle() }} ({{ $lead->linkedCourseTypeLabel() }})</p>
            @else
                <p class="text-xs text-rose-700 font-semibold">غير مربوط — اختر كورساً أو دبلومة.</p>
            @endif
            @include('sales._course_picker', [
                'lead' => $lead,
                'coursesCatalogUrl' => route('employee.sales.courses.index'),
                'inputClass' => $input,
                'labelClass' => 'block text-xs font-bold text-violet-900 mb-1',
            ])
            @error('course_ref_id')<p class="text-xs text-rose-600">{{ $message }}</p>@enderror
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3" x-show="stage === 'payment_pending'" x-cloak>
            <div>
                <label class="block text-xs font-bold mb-1">طريقة الدفع *</label>
                <select name="payment_method" class="{{ $inputReq }}">
                    <option value="">—</option>
                    @foreach(\App\Models\SalesLead::PAYMENT_METHODS as $k => $lab)
                        <option value="{{ $k }}">{{ $lab }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold mb-1">القيمة *</label>
                <input type="number" step="0.01" name="payment_amount" class="{{ $inputReq }}">
            </div>
            <div>
                <label class="block text-xs font-bold mb-1">تاريخ الاستحقاق *</label>
                <input type="datetime-local" name="payment_due_at" class="{{ $inputReq }}">
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3" x-show="stage === 'payment_received'" x-cloak>
            <div>
                <label class="block text-xs font-bold mb-1">رقم العملية *</label>
                <input type="text" name="payment_txn_ref" class="{{ $inputReq }}">
            </div>
            <div>
                <label class="block text-xs font-bold mb-1">المبلغ *</label>
                <input type="number" step="0.01" name="payment_amount" class="{{ $inputReq }}">
            </div>
            <div>
                <label class="block text-xs font-bold mb-1">تاريخ الدفع *</label>
                <input type="datetime-local" name="paid_at" class="{{ $inputReq }}">
            </div>
        </div>

        <div x-show="stage === 'enrollment_completed'" x-cloak>
            <label class="block text-xs font-bold mb-1">قيمة الصفقة *</label>
            <input type="number" step="0.01" name="expected_value" value="{{ $lead->expected_value }}" class="{{ $inputReq }} max-w-xs">
        </div>

        <div x-show="stage === 'lost'" x-cloak>
            <label class="block text-xs font-bold mb-1">سبب الخسارة *</label>
            <select name="lost_reason" class="{{ $inputReq }} max-w-md">
                <option value="">—</option>
                @foreach(\App\Models\SalesLead::LOSS_REASONS as $k => $lab)
                    <option value="{{ $k }}">{{ $lab }}</option>
                @endforeach
            </select>
        </div>

        <div class="flex flex-wrap items-center gap-3 pt-1">
            <button type="submit" @disabled($allowed === []) class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg bg-slate-800 hover:bg-slate-900 disabled:opacity-50 text-white text-sm font-bold">
                <i class="fas fa-arrow-left"></i> نقل للمرحلة المختارة
            </button>
            <p class="text-xs text-slate-500">تظهر الحقول المطلوبة حسب الخطوة فقط.</p>
        </div>
    </form>
    @endif
</div>
<script>
function pipelineForm(initial) {
    return { stage: initial || 'first_contact' };
}
</script>
