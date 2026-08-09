@extends('layouts.employee')

@section('title', 'عميل محتمل جديد')
@section('header', 'عميل محتمل جديد')

@push('styles')
<style>
    .lead-form-panel {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
    }
    .lead-form-panel input,
    .lead-form-panel select,
    .lead-form-panel textarea {
        border: 1px solid #cbd5e1;
        border-radius: 8px;
    }
    .lead-form-panel input:focus,
    .lead-form-panel select:focus,
    .lead-form-panel textarea:focus {
        outline: none;
        border-color: #64748b;
        box-shadow: 0 0 0 2px rgba(100, 116, 139, 0.15);
    }
</style>
@endpush

@section('content')
@php
    $defaultSource = old('source', 'call');
    $defaultPriority = old('priority', 'normal');
    $defaultFollow = old('follow_preset', 'tomorrow');
@endphp

<div class="space-y-4" x-data="fastLeadCreate({
    followPreset: @json($defaultFollow),
    followChannel: @json(old('follow_up_channel', 'call')),
    customFollow: @json(old('next_follow_up_at', '')),
    showDetails: {{ old('email') || old('company') || old('notes') ? 'true' : 'false' }},
})">

    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h2 class="text-xl font-bold text-slate-900">تسجيل عميل جديد</h2>
            <p class="text-sm text-slate-500 mt-0.5">Status + Next Action + موعد متابعة إلزامي · Ctrl+Enter للحفظ</p>
        </div>
        <a href="{{ route('employee.sales.leads.index') }}" class="text-sm text-slate-600 hover:text-slate-900">
            <i class="fas fa-arrow-right ml-1"></i> قائمة العملاء
        </a>
    </div>

    @if($errors->any())
        <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
            <ul class="list-disc list-inside space-y-0.5">
                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    <form method="post" action="{{ route('employee.sales.leads.store') }}"
          class="grid grid-cols-1 xl:grid-cols-12 gap-6"
          @keydown.ctrl.enter.prevent="$refs.primarySubmit.click()">
        @csrf
        <input type="hidden" name="stage" value="new_lead">
        <input type="hidden" name="follow_preset" :value="followPreset">
        <input type="hidden" name="next_follow_up_at" :value="resolvedFollowUp()">
        <input type="hidden" name="follow_up_channel" :value="followChannel">

        <div class="xl:col-span-9 lead-form-panel p-5 sm:p-6 space-y-5">
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
            <div class="md:col-span-2 xl:col-span-3">
                <label class="block text-sm font-medium text-slate-700 mb-1">اسم العميل <span class="text-red-600">*</span></label>
                <input type="text" name="name" required autofocus value="{{ old('name') }}"
                       placeholder="مثال: أحمد محمد"
                       class="w-full px-3 py-2.5 text-base">
                @error('name')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">الهاتف</label>
                <input type="tel" name="phone" inputmode="tel" value="{{ old('phone') }}"
                       placeholder="01xxxxxxxxx" class="w-full px-3 py-2.5">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">اهتمام العميل <span class="text-red-600">*</span></label>
                <select name="interest_type_id" required class="w-full px-3 py-2.5 text-sm bg-white">
                    <option value="">— اختر —</option>
                    @foreach($interestTypes ?? \App\Models\SalesInterestType::active()->ordered()->get() as $itype)
                        <option value="{{ $itype->id }}" @selected(old('interest_type_id') == $itype->id)>{{ $itype->name_ar }}</option>
                    @endforeach
                </select>
                @error('interest_type_id')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">تفاصيل (اختياري)</label>
                <input type="text" name="interest" value="{{ old('interest') }}"
                       placeholder="مثال: كورس Python" class="w-full px-3 py-2.5 text-sm">
            </div>
        </div>

        <div class="border-t border-slate-100 pt-5 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">المصدر</label>
                <select name="source" class="w-full px-3 py-2.5 text-sm bg-white">
                    @foreach(\App\Models\SalesLead::SOURCES as $k => $label)
                        <option value="{{ $k }}" @selected(old('source', $defaultSource) === $k)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">الأولوية</label>
                <select name="priority" class="w-full px-3 py-2.5 text-sm bg-white">
                    @foreach(\App\Models\SalesLead::PRIORITIES as $k => $label)
                        <option value="{{ $k }}" @selected(old('priority', $defaultPriority) === $k)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">موعد المتابعة <span class="text-red-600">*</span></label>
                <select x-model="followPreset" class="w-full px-3 py-2.5 text-sm bg-white">
                    <option value="today">اليوم 17:00</option>
                    <option value="tomorrow">غداً 10:00</option>
                    <option value="3days">بعد 3 أيام</option>
                    <option value="week">بعد أسبوع</option>
                    <option value="custom">موعد مخصص</option>
                </select>
                @error('next_follow_up_at')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Next Action <span class="text-red-600">*</span></label>
                <select x-model="followChannel" class="w-full px-3 py-2.5 text-sm bg-white">
                    @foreach(\App\Models\SalesLead::FOLLOW_UP_CHANNELS as $k => $label)
                        <option value="{{ $k }}" @selected(old('follow_up_channel', 'call') === $k)>{{ $label }}</option>
                    @endforeach
                </select>
                @error('follow_up_channel')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">المجموعة <span class="text-slate-400 font-normal">(اختياري)</span></label>
                <select name="sales_lead_group_id" class="w-full px-3 py-2.5 text-sm bg-white">
                    <option value="">— بدون مجموعة —</option>
                    @foreach($groups ?? [] as $group)
                        <option value="{{ $group->id }}" @selected(old('sales_lead_group_id', $preselectedGroupId ?? '') == $group->id)>
                            {{ $group->name }}@if($group->is_admin_managed) (إدارة) @endif
                        </option>
                    @endforeach
                </select>
                @error('sales_lead_group_id')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            @if(($groups ?? collect())->isEmpty())
                <div class="flex items-end">
                    <a href="{{ route('employee.sales.groups.create') }}" class="text-sm text-slate-600 hover:text-slate-900 underline">+ إنشاء مجموعة أولاً</a>
                </div>
            @endif
        </div>

        <div x-show="followPreset === 'custom'" x-cloak>
            <label class="block text-sm font-medium text-slate-700 mb-1">الموعد المخصص <span class="text-red-600">*</span></label>
            <input type="datetime-local" x-model="customFollow" class="w-full max-w-xs px-3 py-2.5 text-sm">
        </div>

        <div class="border-t border-slate-100 pt-5 space-y-4">
            <p class="text-sm font-semibold text-slate-800">الكورس والقيمة</p>
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">قيمة متوقعة (ج.م)</label>
                    <input type="number" step="0.01" min="0" name="expected_value" value="{{ old('expected_value') }}" class="w-full px-3 py-2.5 text-sm border border-slate-300 rounded-lg">
                    @error('expected_value')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                @include('sales._course_picker', [
                    'lead' => null,
                    'coursesCatalogUrl' => route('employee.sales.courses.index'),
                    'inputClass' => 'w-full px-3 py-2.5 text-sm border border-slate-300 rounded-lg',
                    'labelClass' => 'block text-sm font-medium text-slate-700 mb-1',
                ])
            </div>
            <p class="text-xs text-slate-500">مسجّل = كورسات المنصة · أونلاين = مجموعات أونلاين · أوفلاين = حضور فعلي — والسعر يُملأ تلقائياً من بيانات الكورس.</p>
        </div>

        <div class="border-t border-slate-100 pt-4">
            <button type="button" @click="showDetails = !showDetails"
                    class="text-sm font-medium text-slate-700 flex items-center gap-2">
                <i class="fas fa-chevron-down text-xs text-slate-400" :class="showDetails && 'rotate-180'"></i>
                تفاصيل إضافية (اختياري)
            </button>
            <div x-show="showDetails" x-cloak class="mt-4 grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">البريد</label>
                    <input type="email" name="email" value="{{ old('email') }}" class="w-full px-3 py-2.5 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">الشركة</label>
                    <input type="text" name="company" value="{{ old('company') }}" class="w-full px-3 py-2.5 text-sm">
                </div>
                <div class="md:col-span-2 xl:col-span-4">
                    <label class="block text-sm font-medium text-slate-700 mb-1">ملاحظات</label>
                    <textarea name="notes" rows="2" class="w-full px-3 py-2.5 text-sm">{{ old('notes') }}</textarea>
                </div>
            </div>
        </div>

        <div class="flex flex-wrap gap-2 pt-2 border-t border-slate-100">
            <button type="submit" name="save_action" value="show" x-ref="primarySubmit"
                    class="px-5 py-2.5 rounded-lg bg-slate-800 hover:bg-slate-900 text-white text-sm font-semibold">
                حفظ وعرض
            </button>
            <button type="submit" name="save_action" value="another"
                    class="px-5 py-2.5 rounded-lg border border-slate-300 bg-white hover:bg-slate-50 text-slate-800 text-sm font-semibold">
                حفظ وإضافة آخر
            </button>
            <a href="{{ route('employee.sales.leads.index') }}"
               class="px-5 py-2.5 rounded-lg text-slate-600 text-sm hover:text-slate-900">
                إلغاء
            </a>
        </div>
        </div>

        <aside class="xl:col-span-3 space-y-4">
            <div class="lead-form-panel p-4 text-sm text-slate-600 space-y-2">
                <p class="font-semibold text-slate-800">تسجيل سريع</p>
                <p><kbd class="px-1 py-0.5 bg-slate-100 rounded text-xs">Ctrl</kbd>+<kbd class="px-1 py-0.5 bg-slate-100 rounded text-xs">Enter</kbd> للحفظ</p>
                <p>المرحلة تُسجّل «جديد» تلقائياً (Status)</p>
                <p class="text-teal-800 font-medium">موعد متابعة + Next Action إلزامي — ممنوع Lead سايب</p>
                <p>«حفظ وإضافة آخر» لتسجيل عدة عملاء</p>
                <p class="pt-2 border-t border-slate-100">اختر <strong>مجموعة</strong> لتنظيم العملاء</p>
                <a href="{{ route('employee.sales.groups.index') }}" class="inline-block text-slate-800 font-medium hover:underline">إدارة المجموعات</a>
            </div>
            <div class="lead-form-panel p-4 text-sm">
                <p class="text-slate-500">مسؤول المبيعات</p>
                <p class="font-semibold text-slate-900 mt-1">{{ auth()->user()->name }}</p>
            </div>
        </aside>
    </form>
</div>

@push('scripts')
<script>
function fastLeadCreate(config) {
    const pad = (n) => String(n).padStart(2, '0');
    const fmtLocal = (d) => `${d.getFullYear()}-${pad(d.getMonth()+1)}-${pad(d.getDate())}T${pad(d.getHours())}:${pad(d.getMinutes())}`;
    const presets = {
        today: () => { const d = new Date(); d.setHours(17, 0, 0, 0); return fmtLocal(d); },
        tomorrow: () => { const d = new Date(); d.setDate(d.getDate() + 1); d.setHours(10, 0, 0, 0); return fmtLocal(d); },
        '3days': () => { const d = new Date(); d.setDate(d.getDate() + 3); d.setHours(10, 0, 0, 0); return fmtLocal(d); },
        week: () => { const d = new Date(); d.setDate(d.getDate() + 7); d.setHours(10, 0, 0, 0); return fmtLocal(d); },
        custom: () => config.customFollow || '',
    };
    return {
        followPreset: config.followPreset === 'none' ? 'tomorrow' : (config.followPreset || 'tomorrow'),
        followChannel: config.followChannel || 'call',
        customFollow: config.customFollow || '',
        showDetails: !!config.showDetails,
        resolvedFollowUp() {
            const fn = presets[this.followPreset];
            return fn ? fn() : presets.tomorrow();
        },
    };
}
</script>
@endpush
@endsection
