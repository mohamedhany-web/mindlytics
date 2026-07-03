@extends('layouts.admin')

@section('title', 'طلب — ' . $inquiry->full_name)
@section('header', 'تفاصيل الطلب')

@section('content')
@include('admin.investment._styles')

<div class="p-3 sm:p-4 md:p-6 space-y-4 sm:space-y-6" style="background: #f8fafc; min-height: 100vh;">
    @include('admin.investment._alerts')
    @include('admin.investment._nav', ['active' => 'inquiries'])

    @include('admin.investment._header', [
        'title' => $inquiry->full_name,
        'subtitle' => $inquiry->investorTypeLabel() . ' · ' . ($inquiry->plan?->title ?? 'بدون خطة'),
        'icon' => 'fas fa-user-tie',
        'actions' => '<a href="' . route('admin.investment.inquiries.index') . '" class="' . $invBtnSecondary . '"><i class="fas fa-arrow-right"></i> العودة للقائمة</a>',
    ])

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2">
            <section class="{{ $invSectionClass }}">
                @include('admin.investment._section-head', ['icon' => 'fas fa-id-card', 'title' => 'بيانات المستثمر'])
                <div class="p-6">
                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                        <div><dt class="text-slate-500 mb-1">البريد</dt><dd class="font-semibold dir-ltr text-right">{{ $inquiry->email }}</dd></div>
                        <div><dt class="text-slate-500 mb-1">الهاتف</dt><dd class="font-semibold dir-ltr text-right">{{ $inquiry->phone }}</dd></div>
                        <div><dt class="text-slate-500 mb-1">نوع المستثمر</dt><dd class="font-medium">{{ $inquiry->investorTypeLabel() }}</dd></div>
                        <div><dt class="text-slate-500 mb-1">الشركة</dt><dd class="font-medium">{{ $inquiry->company_name ?: '—' }}</dd></div>
                        <div><dt class="text-slate-500 mb-1">الخطة</dt><dd class="font-medium">{{ $inquiry->plan?->title ?? '—' }}</dd></div>
                        <div><dt class="text-slate-500 mb-1">المبلغ المقترح</dt><dd class="font-mono font-bold">{{ $inquiry->proposed_amount ? number_format($inquiry->proposed_amount, 0) . ' ' . $inquiry->currency : '—' }}</dd></div>
                        <div><dt class="text-slate-500 mb-1">تاريخ التقديم</dt><dd>{{ $inquiry->created_at?->format('Y-m-d H:i') }}</dd></div>
                        <div><dt class="text-slate-500 mb-1">IP</dt><dd class="dir-ltr text-right text-xs">{{ $inquiry->ip_address ?? '—' }}</dd></div>
                    </dl>
                    @if($inquiry->experience_notes)
                        <h3 class="font-bold text-slate-900 mt-6 mb-2">الخبرة / الخلفية</h3>
                        <p class="text-slate-700 whitespace-pre-wrap">{{ $inquiry->experience_notes }}</p>
                    @endif
                    @if($inquiry->message)
                        <h3 class="font-bold text-slate-900 mt-6 mb-2">رسالة المستثمر</h3>
                        <p class="text-slate-700 whitespace-pre-wrap">{{ $inquiry->message }}</p>
                    @endif
                </div>
            </section>
        </div>
        <div>
            <section class="{{ $invSectionClass }}">
                @include('admin.investment._section-head', ['icon' => 'fas fa-tasks', 'title' => 'تحديث الحالة'])
                <form method="POST" action="{{ route('admin.investment.inquiries.update', $inquiry) }}" class="p-6 space-y-4">
                    @csrf @method('PUT')
                    <div>
                        <label class="{{ $invLabelClass }}">الحالة</label>
                        <select name="status" class="{{ $invSelectClass }}">
                            @foreach($statusLabels as $val => $lbl)
                                <option value="{{ $val }}" @selected($inquiry->status === $val)>{{ $lbl }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="{{ $invLabelClass }}">ملاحظات داخلية</label>
                        <textarea name="admin_notes" rows="5" class="{{ $invTextareaClass }}">{{ old('admin_notes', $inquiry->admin_notes) }}</textarea>
                    </div>
                    @if($inquiry->reviewer)
                        <p class="text-xs text-slate-500">آخر مراجعة: {{ $inquiry->reviewer->name }} — {{ $inquiry->reviewed_at?->format('Y-m-d H:i') }}</p>
                    @endif
                    <button type="submit" class="{{ $invBtnPrimary }} w-full justify-center"><i class="fas fa-save"></i> حفظ</button>
                </form>
                <form method="POST" action="{{ route('admin.investment.inquiries.destroy', $inquiry) }}" class="px-6 pb-6" onsubmit="return confirm('حذف هذا الطلب؟');">
                    @csrf @method('DELETE')
                    <button type="submit" class="w-full text-sm text-rose-700 border border-rose-200 rounded-xl py-2.5 hover:bg-rose-50 bg-white">حذف الطلب</button>
                </form>
            </section>
        </div>
    </div>
</div>
@endsection
