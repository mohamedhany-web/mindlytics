@extends('layouts.admin')

@section('title', 'شيفتات وقنوات المبيعات')
@section('header', 'شيفتات وقنوات المبيعات')

@section('content')
<div class="space-y-6">
    @if(session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 text-emerald-800 px-4 py-3 text-sm">{{ session('success') }}</div>
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
            ])
        @endif

        <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
            <div class="px-4 py-3 border-b border-slate-200 bg-slate-50">
                <h3 class="text-sm font-black text-slate-900">إضافة segment</h3>
            </div>
            <form method="post" action="{{ route('admin.sales.shifts.segments.store', $plan) }}" class="p-4 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3">
                @csrf
                <input type="hidden" name="sales_shift_plan_id" value="{{ $plan->id }}">
                <div>
                    <label class="text-xs font-semibold text-slate-600">اليوم</label>
                    <select name="day_of_week" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
                        @foreach(config('sales_shifts.day_names') as $i => $label)
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
                        @foreach(config('sales_shifts.segment_modes') as $k => $label)
                            <option value="{{ $k }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label class="text-xs font-semibold text-slate-600">القنوات</label>
                    <div class="flex flex-wrap gap-2 mt-1">
                        @foreach(config('sales_shifts.channels') as $code => $ch)
                            <label class="inline-flex items-center gap-1 text-xs font-semibold bg-slate-50 border border-slate-200 rounded-lg px-2 py-1">
                                <input type="checkbox" name="channels[]" value="{{ $code }}"> {{ $ch['label'] }}
                            </label>
                        @endforeach
                    </div>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-semibold px-4 py-2 text-sm">إضافة</button>
                </div>
            </form>
        </section>
    @endif
</div>
@endsection
