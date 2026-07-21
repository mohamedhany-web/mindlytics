@extends('layouts.admin')

@section('title', 'تحويل بيانات موظف — المبيعات')
@section('header', 'المبيعات — تحويل بيانات موظف')

@section('content')
@php
    $inputClass = 'w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm focus:ring-2 focus:ring-violet-500 focus:border-violet-500';
    $scope = $scope ?? 'all';
    $groupId = $groupId ?? null;
    $groups = $groups ?? collect();

    $summaryCards = $fromRep && $stats ? [
        ['label' => 'عملاء محتملون', 'value' => number_format($stats['leads_total'] ?? 0), 'icon' => 'fas fa-user-tag', 'bg' => 'bg-emerald-100', 'text' => 'text-emerald-600', 'description' => ($stats['scope'] ?? 'all') === 'group' ? 'ضمن المجموعة' : 'كل الـ Leads المسندة'],
        ['label' => 'أنشطة CRM', 'value' => number_format($stats['activities_total'] ?? 0), 'icon' => 'fas fa-tasks', 'bg' => 'bg-sky-100', 'text' => 'text-sky-600', 'description' => ($stats['scope'] ?? 'all') === 'group' ? 'أنشطة عملاء المجموعة' : 'سجل الأنشطة'],
        ['label' => 'سجل المراقبة', 'value' => number_format($stats['audit_total'] ?? 0), 'icon' => 'fas fa-history', 'bg' => 'bg-violet-100', 'text' => 'text-violet-600', 'description' => ($stats['scope'] ?? 'all') === 'group' ? 'لا يُنقل مع المجموعة' : 'Audit logs'],
        ['label' => 'أهداف KPI', 'value' => number_format($stats['kpi_targets_total'] ?? 0), 'icon' => 'fas fa-bullseye', 'bg' => 'bg-amber-100', 'text' => 'text-amber-600', 'description' => ($stats['scope'] ?? 'all') === 'group' ? 'لا تُنقل مع المجموعة' : 'إن وُجدت'],
    ] : [];
@endphp

<div class="space-y-6" x-data="{
    scope: @js(old('scope', $scope)),
    groupId: @js((string) old('group_id', $groupId ?? '')),
}">
    @if(session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 text-emerald-800 px-4 py-3 text-sm font-semibold">
            <i class="fas fa-check-circle ml-1"></i>{{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="rounded-xl border border-rose-200 bg-rose-50 text-rose-800 px-4 py-3 text-sm font-semibold">
            <i class="fas fa-exclamation-circle ml-1"></i>{{ session('error') }}
        </div>
    @endif

    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-4 py-4 bg-slate-50 border-b border-slate-200 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-violet-500 to-violet-600 flex items-center justify-center text-white shadow-md">
                    <i class="fas fa-exchange-alt"></i>
                </div>
                <div>
                    <h2 class="text-xl font-black text-slate-900">تحويل بيانات موظف مبيعات</h2>
                    <p class="text-xs text-slate-600">نقل كل البيانات أو بيانات مجموعة معيّنة من موظف إلى موظف أو أكثر مع التوزيع بالتناوب.</p>
                </div>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.sales.groups.index') }}" class="inline-flex items-center gap-2 px-3 py-2 text-sm font-semibold text-slate-700 rounded-xl border border-slate-300 hover:bg-white">
                    <i class="fas fa-layer-group text-teal-600"></i>
                    المجموعات
                </a>
                <a href="{{ route('admin.sales.leads.index') }}" class="inline-flex items-center gap-2 px-3 py-2 text-sm font-semibold text-slate-700 rounded-xl border border-slate-300 hover:bg-white">
                    <i class="fas fa-user-tag text-emerald-600"></i>
                    العملاء المحتملون
                </a>
            </div>
        </div>

        @if($fromRep && !empty($summaryCards))
            <div class="px-4 pt-4 pb-2">
                <p class="text-xs text-slate-600 mb-3">
                    ملخص بيانات: <strong>{{ $fromRep->name }}</strong>
                    @if(($stats['scope'] ?? 'all') === 'group' && $selectedGroup)
                        · المجموعة: <strong>{{ $selectedGroup->name }}</strong>
                    @else
                        · النطاق: <strong>كل البيانات</strong>
                        · بدون مجموعة: <strong>{{ number_format($stats['ungrouped_leads'] ?? 0) }}</strong>
                    @endif
                    · Won معتمد: <strong>{{ number_format($stats['won_confirmed_total'] ?? 0) }}</strong>
                </p>
            </div>
            <div class="grid grid-cols-2 xl:grid-cols-4 gap-3 p-4 pt-0">
                @foreach($summaryCards as $card)
                    <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                        <div class="flex items-center justify-between gap-2">
                            <div class="min-w-0">
                                <p class="text-xs font-semibold text-slate-600 truncate">{{ $card['label'] }}</p>
                                <p class="text-xl font-black text-slate-900 truncate tabular-nums">{{ $card['value'] }}</p>
                            </div>
                            <div class="w-9 h-9 rounded-lg {{ $card['bg'] }} flex items-center justify-center {{ $card['text'] }} flex-shrink-0">
                                <i class="{{ $card['icon'] }} text-sm"></i>
                            </div>
                        </div>
                        <p class="text-[11px] text-slate-500 mt-1 truncate">{{ $card['description'] }}</p>
                    </div>
                @endforeach
            </div>
        @endif
    </section>

    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-4 py-3 border-b border-slate-200 bg-slate-50">
            <h3 class="text-base font-black text-slate-900 flex items-center gap-2">
                <i class="fas fa-search text-sky-600"></i>
                معاينة بيانات الموظف
            </h3>
            <p class="text-xs text-slate-600 mt-0.5">اختر الموظف والنطاق لعرض ما سيتم تحويله.</p>
        </div>
        <div class="p-4">
            <form method="get" action="{{ route('admin.sales.transfer.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-3 items-end"
                  x-data="{ previewScope: @js($scope), previewGroup: @js((string) ($groupId ?? '')) }">
                <div class="md:col-span-1">
                    <label class="block text-xs font-semibold text-slate-700 mb-1">موظف المصدر (من)</label>
                    <select name="from_user_id" class="{{ $inputClass }}" required>
                        <option value="">— اختر موظفاً —</option>
                        @foreach($salesReps as $rep)
                            <option value="{{ $rep->id }}" @selected((string) $fromId === (string) $rep->id)>{{ $rep->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">نطاق المعاينة</label>
                    <select name="scope" x-model="previewScope" class="{{ $inputClass }}">
                        <option value="all">كل البيانات</option>
                        <option value="group">مجموعة معيّنة</option>
                    </select>
                </div>
                <div x-show="previewScope === 'group'" x-cloak>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">المجموعة</label>
                    <select name="group_id" x-model="previewGroup" class="{{ $inputClass }}" :disabled="previewScope !== 'group'">
                        <option value="">— اختر مجموعة —</option>
                        @foreach($groups as $g)
                            <option value="{{ $g->id }}">{{ $g->name }} ({{ number_format($g->leads_for_rep_count) }} عميل)</option>
                        @endforeach
                    </select>
                    @if($fromRep && $groups->isEmpty())
                        <p class="text-[11px] text-amber-700 mt-1">لا توجد مجموعات مرتبطة بهذا الموظف.</p>
                    @endif
                </div>
                <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-xl bg-sky-600 hover:bg-sky-700 px-4 py-2.5 text-sm font-semibold text-white">
                    <i class="fas fa-search"></i>
                    عرض الملخص
                </button>
            </form>
        </div>
    </section>

    @if($fromRep && $stats)
        <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
            <div class="px-4 py-3 border-b border-slate-200 bg-slate-50 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                <div>
                    <h3 class="text-base font-black text-slate-900">تفصيل المراحل — {{ $fromRep->name }}</h3>
                    <p class="text-xs text-slate-600">
                        @if(($stats['scope'] ?? 'all') === 'group' && $selectedGroup)
                            توزيع Leads المجموعة «{{ $selectedGroup->name }}» حسب مرحلة الصفقة.
                        @else
                            توزيع كل Leads حسب مرحلة الصفقة.
                        @endif
                    </p>
                </div>
                @if(session('transfer_summary'))
                    <span class="text-xs font-semibold text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-lg border border-emerald-200">آخر تحويل مسجّل</span>
                @endif
            </div>

            @if(session('transfer_summary'))
                @php
                    $s = session('transfer_summary');
                    $perRep = $s['per_rep'] ?? [];
                    $repNames = ($salesReps ?? collect())->keyBy('id');
                @endphp
                <div class="px-4 pt-4">
                    <div class="rounded-xl border border-emerald-200 bg-emerald-50/60 p-4 text-sm text-emerald-900">
                        <p class="font-bold mb-2"><i class="fas fa-check-double ml-1"></i> ملخص آخر تحويل</p>
                        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 text-xs">
                            <div><span class="text-emerald-700">Leads:</span> <strong class="tabular-nums">{{ (int) ($s['leads_assigned'] ?? 0) }}</strong></div>
                            <div><span class="text-emerald-700">Activities:</span> <strong class="tabular-nums">{{ (int) ($s['activities'] ?? 0) }}</strong></div>
                            <div><span class="text-emerald-700">Audit:</span> <strong class="tabular-nums">{{ (int) ($s['audit_logs'] ?? 0) }}</strong></div>
                            <div><span class="text-emerald-700">KPI moved:</span> <strong class="tabular-nums">{{ (int) ($s['kpi_targets_moved'] ?? 0) }}</strong></div>
                            <div><span class="text-emerald-700">KPI conflicts:</span> <strong class="tabular-nums">{{ (int) ($s['kpi_targets_conflicts'] ?? 0) }}</strong></div>
                            <div><span class="text-emerald-700">Won confirmed:</span> <strong class="tabular-nums">{{ (int) ($s['leads_won_confirmed_by'] ?? 0) }}</strong></div>
                        </div>
                        @if(!empty($perRep))
                            <div class="mt-3 pt-3 border-t border-emerald-200">
                                <p class="text-xs font-bold text-emerald-800 mb-2">التوزيع لكل موظف</p>
                                <div class="flex flex-wrap gap-2">
                                    @foreach($perRep as $uid => $row)
                                        <span class="inline-flex items-center gap-2 rounded-lg bg-white border border-emerald-200 px-2.5 py-1 text-[11px] font-semibold text-emerald-900">
                                            {{ $repNames[$uid]->name ?? ('#'.$uid) }}
                                            <span class="tabular-nums text-emerald-700">{{ (int) ($row['leads'] ?? 0) }} عميل</span>
                                            <span class="tabular-nums text-slate-500">{{ (int) ($row['activities'] ?? 0) }} نشاط</span>
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <div class="p-4 grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-7 gap-3">
                @foreach(\App\Models\SalesLead::STAGES as $k => $label)
                    @php $c = (int) (($stats['leads_by_stage'][$k] ?? 0)); @endphp
                    <div class="rounded-xl border border-slate-200 bg-slate-50/60 p-3 text-center sm:text-right">
                        <p class="text-[11px] font-semibold text-slate-500 truncate">{{ $label }}</p>
                        <p class="text-xl font-black text-slate-900 tabular-nums mt-1">{{ number_format($c) }}</p>
                    </div>
                @endforeach
            </div>

            @if($groups->isNotEmpty() && ($stats['scope'] ?? 'all') === 'all')
                <div class="px-4 pb-4">
                    <div class="rounded-xl border border-teal-100 bg-teal-50/40 p-4">
                        <p class="text-xs font-bold text-teal-900 mb-2"><i class="fas fa-layer-group ml-1"></i> مجموعات الموظف (يمكن تحويل واحدة منها فقط)</p>
                        <div class="flex flex-wrap gap-2">
                            @foreach($groups as $g)
                                <a href="{{ route('admin.sales.transfer.index', ['from_user_id' => $fromId, 'scope' => 'group', 'group_id' => $g->id]) }}"
                                   class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-white border border-teal-200 text-xs font-semibold text-teal-800 hover:bg-teal-50">
                                    {{ $g->name }}
                                    <span class="tabular-nums text-teal-600">{{ number_format($g->leads_for_rep_count) }}</span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </section>
    @endif

    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-4 py-3 border-b border-slate-200 bg-slate-50">
            <h3 class="text-base font-black text-slate-900 flex items-center gap-2">
                <i class="fas fa-random text-violet-600"></i>
                تنفيذ التحويل
            </h3>
            <p class="text-xs text-slate-600 mt-0.5">يمكنك اختيار عدة موظفين — تتوزع الـ Leads عليهم بالتناوب (Round-robin)، سواء لكل البيانات أو لمجموعة.</p>
        </div>

        <form method="post" action="{{ route('admin.sales.transfer.store') }}" class="p-4 sm:p-6 space-y-5"
              x-data="{
                  scope: @js(old('scope', $scope)),
                  groupId: @js((string) old('group_id', $groupId ?? '')),
                  selectedTo: @js(array_map('strval', old('to_user_ids', []))),
                  leadsTotal: {{ (int) ($stats['leads_total'] ?? 0) }},
                  selectAll() {
                      this.selectedTo = [
                          @foreach($salesReps as $rep)
                              @if((string)$rep->id !== (string)$fromId)
                                  '{{ $rep->id }}',
                              @endif
                          @endforeach
                      ].filter(Boolean);
                  },
                  clearAll() { this.selectedTo = []; },
                  shareFor(index) {
                      const n = this.selectedTo.length;
                      if (n === 0 || index < 0) return 0;
                      const base = Math.floor(this.leadsTotal / n);
                      const rem = this.leadsTotal % n;
                      return base + (index < rem ? 1 : 0);
                  }
              }">
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                <div class="rounded-xl border border-slate-200 bg-slate-50/50 p-4">
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">
                        <i class="fas fa-arrow-right text-rose-500 ml-0.5"></i>
                        من (موظف مبيعات)
                    </label>
                    <select name="from_user_id" required class="{{ $inputClass }}"
                            onchange="window.location='{{ route('admin.sales.transfer.index') }}?from_user_id='+this.value">
                        <option value="">— اختر —</option>
                        @foreach($salesReps as $rep)
                            <option value="{{ $rep->id }}" @selected(old('from_user_id', $fromId) == $rep->id)>{{ $rep->name }}</option>
                        @endforeach
                    </select>
                    @error('from_user_id')<p class="text-rose-600 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="rounded-xl border border-slate-200 bg-slate-50/50 p-4">
                    <div class="flex flex-wrap items-center justify-between gap-2 mb-2">
                        <label class="block text-xs font-semibold text-slate-700">
                            <i class="fas fa-users text-emerald-500 ml-0.5"></i>
                            إلى (موظف أو أكثر)
                        </label>
                        <div class="flex gap-2 text-[11px]">
                            <button type="button" @click="selectAll()" class="text-emerald-700 font-semibold hover:underline">تحديد الكل</button>
                            <button type="button" @click="clearAll()" class="text-slate-500 font-semibold hover:underline">مسح</button>
                        </div>
                    </div>
                    <div class="max-h-56 overflow-y-auto rounded-xl border border-slate-200 bg-white divide-y divide-slate-100">
                        @foreach($salesReps as $rep)
                            @if((string) $rep->id === (string) $fromId)
                                @continue
                            @endif
                            <label class="flex items-center gap-3 px-3 py-2.5 cursor-pointer hover:bg-emerald-50/50"
                                   :class="selectedTo.includes('{{ $rep->id }}') && 'bg-emerald-50'">
                                <input type="checkbox"
                                       name="to_user_ids[]"
                                       value="{{ $rep->id }}"
                                       class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500"
                                       x-model="selectedTo">
                                <span class="text-sm font-semibold text-slate-800 flex-1">{{ $rep->name }}</span>
                                <span class="text-[11px] tabular-nums text-emerald-700 font-bold"
                                      x-show="selectedTo.includes('{{ $rep->id }}')"
                                      x-text="'≈ ' + shareFor(selectedTo.indexOf('{{ $rep->id }}')) + ' عميل'"></span>
                            </label>
                        @endforeach
                    </div>
                    <p class="text-[11px] text-slate-500 mt-2">
                        المحدد: <strong x-text="selectedTo.length"></strong> موظف
                        <span x-show="selectedTo.length > 1"> · التوزيع بالتناوب على العملاء المحتملين</span>
                    </p>
                    @error('to_user_ids')<p class="text-rose-600 text-xs mt-1">{{ $message }}</p>@enderror
                    @error('to_user_ids.*')<p class="text-rose-600 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="rounded-xl border border-slate-200 bg-white p-4 space-y-3">
                <p class="text-xs font-bold text-slate-800">ماذا تريد تحويله؟</p>
                <div class="grid sm:grid-cols-2 gap-3">
                    <label class="flex items-start gap-3 rounded-xl border p-4 cursor-pointer transition-colors"
                           :class="scope === 'all' ? 'border-violet-300 bg-violet-50/60' : 'border-slate-200 hover:border-slate-300'">
                        <input type="radio" name="scope" value="all" class="mt-1 text-violet-600" x-model="scope">
                        <span>
                            <span class="block text-sm font-bold text-slate-900">كل البيانات</span>
                            <span class="block text-[11px] text-slate-500 mt-1">كل الـ Leads + الأنشطة. سجل المراقبة وأهداف KPI تذهب لأول موظف محدد.</span>
                        </span>
                    </label>
                    <label class="flex items-start gap-3 rounded-xl border p-4 cursor-pointer transition-colors"
                           :class="scope === 'group' ? 'border-teal-300 bg-teal-50/60' : 'border-slate-200 hover:border-slate-300'">
                        <input type="radio" name="scope" value="group" class="mt-1 text-teal-600" x-model="scope">
                        <span>
                            <span class="block text-sm font-bold text-slate-900">مجموعة معيّنة فقط</span>
                            <span class="block text-[11px] text-slate-500 mt-1">عملاء المجموعة وأنشطتهم فقط — يُوزَّعون على الموظفين المحددين ويُضافون لأعضاء المجموعة.</span>
                        </span>
                    </label>
                </div>

                <div x-show="scope === 'group'" x-cloak class="pt-1">
                    <label class="block text-xs font-semibold text-slate-700 mb-1">اختر المجموعة *</label>
                    <select name="group_id" x-model="groupId" class="{{ $inputClass }}"
                            :required="scope === 'group'"
                            :disabled="scope !== 'group'">
                        <option value="">— اختر مجموعة —</option>
                        @foreach($groups as $g)
                            <option value="{{ $g->id }}">{{ $g->name }} — {{ number_format($g->leads_for_rep_count) }} عميل</option>
                        @endforeach
                    </select>
                    @error('group_id')<p class="text-rose-600 text-xs mt-1">{{ $message }}</p>@enderror
                    @if($fromRep && $groups->isEmpty())
                        <p class="text-xs text-amber-700 mt-2 bg-amber-50 border border-amber-200 rounded-lg px-3 py-2">
                            لا توجد مجموعات لهذا الموظف — أنشئ مجموعة من
                            <a href="{{ route('admin.sales.groups.index') }}" class="font-bold underline">مجموعات العملاء</a>
                            أو حوّل كل البيانات.
                        </p>
                    @elseif(! $fromRep)
                        <p class="text-[11px] text-slate-500 mt-1">اختر موظف المصدر أولاً لتحميل مجموعاته.</p>
                    @endif
                </div>
            </div>

            <div class="rounded-xl border border-amber-200 bg-amber-50/70 p-4">
                <p class="text-sm font-bold text-amber-900 mb-1">
                    <i class="fas fa-exclamation-triangle ml-1"></i>
                    تنبيه مهم
                </p>
                <p class="text-sm text-amber-900/90 leading-relaxed" x-show="scope === 'all'">
                    سيتم <strong>توزيع</strong> كل بيانات المبيعات للموظف المصدر على الموظفين المحددين بالتناوب. لا يمكن التراجع تلقائياً.
                </p>
                <p class="text-sm text-amber-900/90 leading-relaxed" x-show="scope === 'group'" x-cloak>
                    سيتم توزيع عملاء المجموعة المحددة وأنشطتهم على الموظفين المحددين، وإضافتهم لأعضاء المجموعة.
                    باقي بيانات الموظف المصدر تبقى كما هي.
                </p>
                <label class="mt-3 flex items-start gap-3 rounded-xl border-2 border-amber-300 bg-white px-3 py-3 text-sm font-semibold text-amber-950 cursor-pointer">
                    <input type="checkbox" name="confirm" value="1" required
                           class="rounded border-amber-400 mt-0.5 text-amber-600 focus:ring-amber-400 w-4 h-4"
                           @checked(old('confirm'))>
                    <span>
                        <span class="block" x-text="scope === 'group' ? 'أؤكد توزيع بيانات المجموعة على الموظفين المحددين' : 'أؤكد توزيع جميع بيانات الموظف على الموظفين المحددين'"></span>
                        <span class="block text-[11px] font-normal text-amber-800/80 mt-1">مطلوب قبل التنفيذ — لن يعمل التحويل بدون التأكيد.</span>
                    </span>
                </label>
                @error('confirm')<p class="text-rose-600 text-xs mt-2 font-semibold">{{ $message }}</p>@enderror
            </div>

            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pt-2 border-t border-slate-100">
                <p class="text-xs text-slate-500">
                    <i class="fas fa-shield-alt text-violet-600 ml-0.5"></i>
                    يُسجَّل التحويل في سجل مراقبة المبيعات.
                </p>
                <button type="submit"
                        class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-violet-600 hover:bg-violet-700 text-white rounded-xl text-sm font-semibold disabled:opacity-50"
                        :disabled="selectedTo.length === 0">
                    <i class="fas fa-random"></i>
                    <span x-text="selectedTo.length > 1 ? 'توزيع البيانات الآن' : 'تحويل البيانات الآن'"></span>
                </button>
            </div>
        </form>
    </section>
</div>
@endsection
