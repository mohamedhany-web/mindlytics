@php $s = $settings; @endphp
<form method="POST" action="{{ $formAction }}" class="space-y-6">
    @csrf
    @if(($method ?? 'POST') === 'PUT')
        @method('PUT')
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <label class="flex items-center gap-3 rounded-xl border border-slate-200 p-4 bg-white">
            <input type="checkbox" name="enabled" value="1" class="rounded" @checked($s['enabled'] ?? true)>
            <span class="text-sm font-semibold text-slate-800">تفعيل التقرير اليومي الإلزامي</span>
        </label>
        <label class="flex items-center gap-3 rounded-xl border border-slate-200 p-4 bg-white">
            <input type="checkbox" name="work_days_only" value="1" class="rounded" @checked($s['work_days_only'] ?? true)>
            <span class="text-sm font-semibold text-slate-800">أيام العمل فقط (حسب يوم إجازة كل موظف + الإجازات المعتمدة)</span>
        </label>
        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-2">آخر موعد للتسليم (ساعة)</label>
            <input type="time" name="deadline_time" value="{{ $s['deadline_time'] ?? '23:59' }}" required class="w-full rounded-xl border-2 border-slate-200 px-4 py-3 text-sm">
        </div>
        <div>
            <label class="block text-sm font-semibold text-slate-700 mb-2">هدف KPI — نسبة التسليم الشهرية %</label>
            <input type="number" name="kpi_submission_target_pct" min="50" max="100" step="1" value="{{ $s['kpi_submission_target_pct'] ?? 95 }}" required class="w-full rounded-xl border-2 border-slate-200 px-4 py-3 text-sm">
        </div>
    </div>

    <div class="rounded-2xl border-2 border-rose-200 bg-rose-50/50 p-5 space-y-4">
        <h3 class="font-bold text-rose-900 flex items-center gap-2"><i class="fas fa-gavel"></i> الخصم التلقائي (يُسجّل في خصومات الموظفين)</h3>
        <label class="flex items-center gap-3">
            <input type="checkbox" name="penalty_enabled" value="1" class="rounded" @checked($s['penalty_enabled'] ?? true)>
            <span class="text-sm font-semibold">تفعيل الخصم عند عدم التسليم</span>
        </label>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">مبلغ الخصم (ج.م)</label>
                <input type="number" name="penalty_amount" step="0.01" min="0.01" value="{{ $s['penalty_amount'] ?? 50 }}" required class="w-full rounded-xl border-2 border-slate-200 px-4 py-3 text-sm">
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">حالة الخصم عند الإنشاء</label>
                <select name="penalty_status" class="w-full rounded-xl border-2 border-slate-200 px-4 py-3 text-sm">
                    @foreach(['pending' => 'معلّق', 'applied' => 'مطبّق', 'cancelled' => 'ملغى'] as $val => $label)
                        <option value="{{ $val }}" @selected(($s['penalty_status'] ?? 'pending') === $val)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-semibold text-slate-700 mb-2">عنوان الخصم</label>
                <input type="text" name="penalty_title" value="{{ $s['penalty_title'] ?? '' }}" required class="w-full rounded-xl border-2 border-slate-200 px-4 py-3 text-sm">
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-semibold text-slate-700 mb-2">وصف الخصم</label>
                <textarea name="penalty_description" rows="2" class="w-full rounded-xl border-2 border-slate-200 px-4 py-3 text-sm">{{ $s['penalty_description'] ?? '' }}</textarea>
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-2">نوع الخصم</label>
                <select name="penalty_type" class="w-full rounded-xl border-2 border-slate-200 px-4 py-3 text-sm">
                    @foreach(['penalty' => 'غرامة', 'other' => 'أخرى', 'tax' => 'ضريبة', 'insurance' => 'تأمين', 'loan' => 'قرض'] as $val => $label)
                        <option value="{{ $val }}" @selected(($s['penalty_type'] ?? 'penalty') === $val)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-6 py-3 text-sm">
        <i class="fas fa-save"></i> حفظ الإعدادات
    </button>
</form>
