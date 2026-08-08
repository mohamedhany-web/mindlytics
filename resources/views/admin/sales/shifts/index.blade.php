@extends('layouts.admin')

@section('title', 'شيفتات وقنوات المبيعات')
@section('header', 'شيفتات وقنوات المبيعات')

@section('content')
@php
    $modeLabels = $modeLabels ?? config('sales_shifts.segment_modes', []);
    $dayNames = $dayNames ?? config('sales_shifts.day_names', []);
    $channelLabels = $channelLabels ?? [];
    $channelsConfig = config('sales_shifts.channels', []);
@endphp
<div class="space-y-6"
     x-data="shiftSegmentEditor()"
     @edit-shift-segment.window="openEdit($event.detail)">
    @if(session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 text-emerald-800 px-4 py-3 text-sm">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="rounded-xl border border-rose-200 bg-rose-50 text-rose-800 px-4 py-3 text-sm">
            <ul class="list-disc pe-5 space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(! $plan)
        <section class="rounded-2xl bg-white border border-slate-200 shadow-lg p-8 text-center">
            <div class="w-16 h-16 mx-auto rounded-2xl bg-violet-100 text-violet-600 flex items-center justify-center text-2xl mb-4">
                <i class="fas fa-calendar-week"></i>
            </div>
            <h2 class="text-xl font-black text-slate-900">لا توجد خطة شيفتات نشطة</h2>
            <p class="text-sm text-slate-600 mt-2 max-w-lg mx-auto">
                استورد الجدول الافتراضي من <code class="text-xs bg-slate-100 px-1 rounded">sales-shifts.html</code>
                (4 موظفين، توزيع قنوات، 10 ص – 2 ص) ثم عدّل من لوحة الإدارة.
            </p>
            <form method="post" action="{{ route('admin.sales.shifts.import-demo') }}" class="mt-6">
                @csrf
                <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-violet-600 hover:bg-violet-700 text-white font-semibold px-6 py-3 text-sm">
                    <i class="fas fa-file-import"></i> استيراد الجدول الافتراضي
                </button>
            </form>
        </section>
    @else
        <section class="rounded-xl border border-sky-200 bg-sky-50 px-4 py-3 text-sm text-sky-950">
            <p class="font-bold mb-1"><i class="fas fa-info-circle ml-1"></i> توزيع القنوات ≠ حضور المقر</p>
            <ul class="list-disc pe-5 space-y-1 text-sky-900/90 text-xs sm:text-sm">
                <li>يوم «من المقر» في الشارة لا يعني إن كل الفريق لازم ينزل — كل segment له وضع مستقل.</li>
                <li><b>من المقر / عادي</b>: يظهر في تأكيد حضور المقر لمدير المبيعات.</li>
                <li><b>من البيت</b>: يشتغل أونلاين/من البيت — يدخل في توزيع القنوات لكن <b>لا يظهر</b> في حضور المقر (حتى لو باقي اليوم فيه نزول بالليل).</li>
            </ul>
        </section>

        <section class="rounded-2xl bg-white border border-slate-200 shadow-lg p-4">
            <h3 class="text-sm font-black text-slate-900 mb-3">إعدادات الخطة النشطة</h3>
            <form method="post" action="{{ route('admin.sales.shifts.update-plan', $plan) }}" class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-3 items-end">
                @csrf
                @method('PUT')
                <div class="md:col-span-2">
                    <label class="text-xs font-semibold text-slate-600">الاسم</label>
                    <input type="text" name="name" value="{{ old('name', $plan->name) }}" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="text-xs font-semibold text-slate-600">بداية (ساعة)</label>
                    <input type="number" name="work_start_hour" value="{{ $plan->work_start_hour }}" min="0" max="23" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="text-xs font-semibold text-slate-600">نهاية (26=2ص)</label>
                    <input type="number" name="work_end_hour" value="{{ $plan->work_end_hour }}" min="13" max="30" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="text-xs font-semibold text-slate-600">مهلة التدخل (د)</label>
                    <input type="number" name="takeover_grace_minutes" value="{{ $plan->takeover_grace_minutes }}" min="1" max="60" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
                </div>
                <div class="flex items-center gap-2">
                    <label class="flex items-center gap-2 text-sm font-semibold text-slate-700">
                        <input type="checkbox" name="is_active" value="1" @checked($plan->is_active) class="rounded border-slate-300">
                        نشطة
                    </label>
                    <button type="submit" class="rounded-xl bg-sky-600 hover:bg-sky-700 text-white font-semibold px-4 py-2 text-sm">حفظ</button>
                </div>
            </form>
        </section>

        @if($board)
            @include('sales._shift_week_board', [
                'board' => $board,
                'navRoute' => 'admin.sales.shifts.index',
                'title' => 'جدول الشيفتات الأسبوعي',
                'editable' => true,
            ])
            <p class="text-xs text-slate-500 -mt-4 px-1">اضغط على أي شريط شيفت في الجدول لفتح التعديل.</p>
        @endif

        <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
            <div class="px-4 py-3 border-b border-slate-200 bg-slate-50 flex flex-wrap items-center justify-between gap-2">
                <h3 class="text-sm font-black text-slate-900">جدول الشيفتات ({{ $segments->count() }})</h3>
                <span class="text-[11px] text-slate-500">عدّل من هنا أو من الشريط في اللوحة أعلاه</span>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-slate-50 text-xs text-slate-600">
                        <tr>
                            <th class="text-start px-3 py-2 font-bold">اليوم</th>
                            <th class="text-start px-3 py-2 font-bold">الموظف</th>
                            <th class="text-start px-3 py-2 font-bold">الوقت</th>
                            <th class="text-start px-3 py-2 font-bold">الوضع</th>
                            <th class="text-start px-3 py-2 font-bold">القنوات</th>
                            <th class="text-start px-3 py-2 font-bold">إجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($segments as $seg)
                            @php
                                $payload = [
                                    'id' => $seg->id,
                                    'day_of_week' => (int) $seg->day_of_week,
                                    'user_id' => (int) $seg->user_id,
                                    'user_name' => $seg->user?->name,
                                    'start_hour' => (int) $seg->start_hour,
                                    'end_hour' => (int) $seg->end_hour,
                                    'mode' => $seg->mode,
                                    'channels' => $seg->channels ?? [],
                                    'location_badge' => $seg->location_badge,
                                    'sort_order' => (int) $seg->sort_order,
                                ];
                            @endphp
                            <tr class="hover:bg-slate-50/80">
                                <td class="px-3 py-2.5 whitespace-nowrap">{{ $dayNames[$seg->day_of_week] ?? $seg->day_of_week }}</td>
                                <td class="px-3 py-2.5 font-semibold text-slate-900">{{ $seg->user?->name ?? '—' }}</td>
                                <td class="px-3 py-2.5 tabular-nums whitespace-nowrap">
                                    {{ app(\App\Services\SalesShiftScheduleService::class)->formatHourLabel((int) $seg->start_hour) }}
                                    –
                                    {{ app(\App\Services\SalesShiftScheduleService::class)->formatHourLabel((int) $seg->end_hour) }}
                                </td>
                                <td class="px-3 py-2.5">
                                    @if($seg->mode === \App\Models\SalesShiftSegment::MODE_HOME)
                                        <span class="inline-flex rounded-lg bg-amber-50 text-amber-900 border border-amber-200 px-2 py-0.5 text-[11px] font-bold">من البيت</span>
                                    @else
                                        <span class="inline-flex rounded-lg bg-teal-50 text-teal-900 border border-teal-200 px-2 py-0.5 text-[11px] font-bold">من المقر</span>
                                    @endif
                                </td>
                                <td class="px-3 py-2.5 text-xs text-slate-600">{{ $seg->channelsLabel() }}</td>
                                <td class="px-3 py-2.5 whitespace-nowrap">
                                    <button type="button"
                                            @click="openEdit({{ \Illuminate\Support\Js::from($payload) }})"
                                            class="text-sky-700 hover:text-sky-900 font-bold text-xs me-2">
                                        <i class="fas fa-edit"></i> تعديل
                                    </button>
                                    <form method="post" action="{{ route('admin.sales.shifts.segments.destroy', $seg) }}" class="inline"
                                          onsubmit="return confirm('حذف هذا الشيفت؟');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-rose-600 hover:text-rose-800 font-bold text-xs">
                                            <i class="fas fa-trash"></i> حذف
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-slate-400">لا توجد شيفتات بعد.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
            <div class="px-4 py-3 border-b border-slate-200 bg-slate-50">
                <h3 class="text-sm font-black text-slate-900">إضافة شيفت جديد</h3>
            </div>
            <form method="post" action="{{ route('admin.sales.shifts.segments.store', $plan) }}" class="p-4 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3">
                @csrf
                <input type="hidden" name="sales_shift_plan_id" value="{{ $plan->id }}">
                <div>
                    <label class="text-xs font-semibold text-slate-600">اليوم</label>
                    <select name="day_of_week" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
                        @foreach($dayNames as $i => $label)
                            <option value="{{ $i }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-xs font-semibold text-slate-600">الموظف</label>
                    <select name="user_id" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
                        @foreach($salesReps as $rep)
                            <option value="{{ $rep->id }}">{{ $rep->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-xs font-semibold text-slate-600">من – إلى (ساعة)</label>
                    <div class="flex gap-2">
                        <input type="number" name="start_hour" placeholder="10" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" required>
                        <input type="number" name="end_hour" placeholder="18" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" required>
                    </div>
                </div>
                <div>
                    <label class="text-xs font-semibold text-slate-600">الوضع</label>
                    <select name="mode" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
                        @foreach($modeLabels as $k => $label)
                            <option value="{{ $k }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label class="text-xs font-semibold text-slate-600">القنوات</label>
                    <div class="flex flex-wrap gap-2 mt-1">
                        @foreach($channelsConfig as $code => $ch)
                            <label class="inline-flex items-center gap-1 text-xs font-semibold bg-slate-50 border border-slate-200 rounded-lg px-2 py-1">
                                <input type="checkbox" name="channels[]" value="{{ $code }}"> {{ $ch['label'] }}
                            </label>
                        @endforeach
                    </div>
                </div>
                <div>
                    <label class="text-xs font-semibold text-slate-600">شارة الموقع (اختياري)</label>
                    <input type="text" name="location_badge" placeholder="من المقر / من البيت" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-semibold px-4 py-2 text-sm">إضافة</button>
                </div>
            </form>
        </section>

        {{-- Edit modal --}}
        <div x-show="open"
             x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center p-4"
             style="display: none;">
            <div class="absolute inset-0 bg-slate-900/50" @click="close()"></div>
            <div class="relative w-full max-w-lg rounded-2xl bg-white shadow-2xl border border-slate-200 overflow-hidden"
                 @click.stop>
                <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50">
                    <div>
                        <h3 class="font-black text-slate-900">تعديل الشيفت</h3>
                        <p class="text-xs text-slate-500 mt-0.5" x-text="form.user_name || ''"></p>
                    </div>
                    <button type="button" @click="close()" class="w-9 h-9 rounded-xl text-slate-500 hover:bg-slate-200">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <form method="post" :action="updateUrl" class="p-5 space-y-4">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="sales_shift_plan_id" value="{{ $plan->id }}">
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="text-xs font-semibold text-slate-600">اليوم</label>
                            <select name="day_of_week" x-model="form.day_of_week" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
                                @foreach($dayNames as $i => $label)
                                    <option value="{{ $i }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-slate-600">الموظف</label>
                            <select name="user_id" x-model="form.user_id" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
                                @foreach($salesReps as $rep)
                                    <option value="{{ $rep->id }}">{{ $rep->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-slate-600">من (ساعة)</label>
                            <input type="number" name="start_hour" x-model="form.start_hour" min="0" max="30" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" required>
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-slate-600">إلى (ساعة)</label>
                            <input type="number" name="end_hour" x-model="form.end_hour" min="1" max="30" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" required>
                        </div>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-slate-600">الوضع</label>
                        <select name="mode" x-model="form.mode" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
                            @foreach($modeLabels as $k => $label)
                                <option value="{{ $k }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        <p class="text-[11px] text-slate-500 mt-1">«من البيت» = أونلاين — لن يظهر في تأكيد حضور المقر.</p>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-slate-600">القنوات</label>
                        <div class="flex flex-wrap gap-2 mt-1">
                            @foreach($channelsConfig as $code => $ch)
                                <label class="inline-flex items-center gap-1 text-xs font-semibold bg-slate-50 border border-slate-200 rounded-lg px-2 py-1">
                                    <input type="checkbox" name="channels[]" value="{{ $code }}"
                                           :checked="form.channels.includes('{{ $code }}')"
                                           @change="toggleChannel('{{ $code }}', $event.target.checked)">
                                    {{ $ch['label'] }}
                                </label>
                            @endforeach
                        </div>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-slate-600">شارة الموقع</label>
                        <input type="text" name="location_badge" x-model="form.location_badge" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
                    </div>
                    <div class="flex gap-2 pt-1">
                        <button type="submit" class="flex-1 rounded-xl bg-sky-600 hover:bg-sky-700 text-white font-bold px-4 py-2.5 text-sm">
                            حفظ التعديل
                        </button>
                        <button type="button" @click="close()" class="rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                            إلغاء
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>

@if($plan)
<script>
function shiftSegmentEditor() {
    return {
        open: false,
        updateUrl: '',
        form: {
            id: null,
            day_of_week: 0,
            user_id: '',
            user_name: '',
            start_hour: 10,
            end_hour: 18,
            mode: 'normal',
            channels: [],
            location_badge: '',
        },
        openEdit(seg) {
            const data = seg || {};
            this.form = {
                id: data.id,
                day_of_week: String(data.day_of_week ?? 0),
                user_id: String(data.user_id ?? ''),
                user_name: data.user_name || '',
                start_hour: data.start_hour ?? 10,
                end_hour: data.end_hour ?? 18,
                mode: data.mode || 'normal',
                channels: Array.isArray(data.channels) ? data.channels.slice() : [],
                location_badge: data.location_badge || '',
            };
            this.updateUrl = @json(url('/admin/sales/shifts/segments')) + '/' + data.id;
            this.open = true;
            document.body.classList.add('overflow-hidden');
        },
        toggleChannel(code, on) {
            const set = new Set(this.form.channels || []);
            if (on) set.add(code); else set.delete(code);
            this.form.channels = Array.from(set);
        },
        close() {
            this.open = false;
            document.body.classList.remove('overflow-hidden');
        },
    };
}
</script>
@endif
@endsection
