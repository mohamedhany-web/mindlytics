@extends('layouts.admin')

@section('title', 'خصومات الموظفين - Mindlytics')
@section('header', 'خصومات الموظفين')

@section('content')
<div class="p-3 sm:p-4 md:p-6 space-y-4 sm:space-y-6" style="background: #f8fafc; min-height: 100vh;">
    @if(session('success'))
        <div class="rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 px-5 py-4 flex items-center gap-3 shadow-sm">
            <i class="fas fa-check-circle text-emerald-600 text-xl"></i>
            <span class="font-semibold">{{ session('success') }}</span>
        </div>
    @endif

    <!-- الهيدر -->
    <section class="rounded-2xl bg-white/95 backdrop-blur border-2 border-slate-200/50 shadow-xl overflow-hidden">
        <div class="px-5 py-6 sm:px-8 lg:px-12 border-b border-slate-200 bg-gradient-to-r from-slate-50 to-white flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-rose-500 to-red-600 flex items-center justify-center text-white shadow-lg">
                    <i class="fas fa-minus-circle text-xl"></i>
                </div>
                <div>
                    <h2 class="text-2xl sm:text-3xl font-black text-slate-900">خصومات الموظفين</h2>
                    <p class="text-sm text-slate-600 mt-1">إدارة خصومات الرواتب (ضريبة، تأمين، قرض، غرامة، أخرى)</p>
                </div>
            </div>
            <a href="{{ route('admin.employee-deductions.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-rose-600 to-red-500 hover:from-rose-700 hover:to-red-600 px-5 py-3 text-sm font-semibold text-white shadow-lg hover:shadow-xl transition-all duration-200">
                <i class="fas fa-plus"></i>
                إضافة خصم جديد
            </a>
        </div>
    </section>

    <!-- إحصائيات -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="rounded-2xl bg-white border-2 border-slate-200/50 p-5 shadow-lg">
            <p class="text-sm font-bold text-slate-600 mb-1">إجمالي الخصومات</p>
            <p class="text-2xl font-black text-slate-900">{{ $stats['total'] }}</p>
        </div>
        <div class="rounded-2xl bg-amber-50 border-2 border-amber-200/50 p-5 shadow-lg">
            <p class="text-sm font-bold text-amber-800 mb-1">معلقة</p>
            <p class="text-2xl font-black text-amber-700">{{ $stats['pending'] }}</p>
        </div>
        <div class="rounded-2xl bg-emerald-50 border-2 border-emerald-200/50 p-5 shadow-lg">
            <p class="text-sm font-bold text-emerald-800 mb-1">مطبقة</p>
            <p class="text-2xl font-black text-emerald-700">{{ $stats['applied'] }}</p>
        </div>
        <div class="rounded-2xl bg-rose-50 border-2 border-rose-200/50 p-5 shadow-lg">
            <p class="text-sm font-bold text-rose-800 mb-1">إجمالي المبالغ المطبقة</p>
            <p class="text-2xl font-black text-rose-700">{{ number_format($stats['total_amount'], 2) }} ج.م</p>
        </div>
    </div>

    <!-- فلترة وبحث -->
    <section class="rounded-2xl bg-white border-2 border-slate-200/50 shadow-xl overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-200">
            <h3 class="text-lg font-bold text-slate-900 flex items-center gap-2"><i class="fas fa-filter text-rose-600"></i> البحث والفلترة</h3>
        </div>
        <div class="p-5">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">البحث</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="رقم الخصم، العنوان، الموظف..." class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">الموظف</label>
                    <select name="employee_id" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">
                        <option value="">جميع الموظفين</option>
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}" {{ request('employee_id') == $emp->id ? 'selected' : '' }}>{{ $emp->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">الحالة</label>
                    <select name="status" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">
                        <option value="">الكل</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>معلقة</option>
                        <option value="applied" {{ request('status') == 'applied' ? 'selected' : '' }}>مطبقة</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>ملغاة</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">نوع الخصم</label>
                    <select name="type" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">
                        <option value="">الكل</option>
                        <option value="tax" {{ request('type') == 'tax' ? 'selected' : '' }}>ضريبة</option>
                        <option value="insurance" {{ request('type') == 'insurance' ? 'selected' : '' }}>تأمين</option>
                        <option value="loan" {{ request('type') == 'loan' ? 'selected' : '' }}>قرض</option>
                        <option value="penalty" {{ request('type') == 'penalty' ? 'selected' : '' }}>غرامة</option>
                        <option value="other" {{ request('type') == 'other' ? 'selected' : '' }}>أخرى</option>
                    </select>
                </div>
                <div class="md:col-span-2 lg:col-span-4 flex gap-2">
                    <button type="submit" class="px-4 py-2.5 bg-rose-600 hover:bg-rose-700 text-white rounded-xl font-semibold text-sm"><i class="fas fa-search ml-1"></i> بحث</button>
                    <a href="{{ route('admin.employee-deductions.index') }}" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-semibold text-sm">إعادة تعيين</a>
                </div>
            </form>
        </div>
    </section>

    <!-- الجدول -->
    <section class="rounded-2xl bg-white border-2 border-slate-200/50 shadow-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase">رقم الخصم</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase">الموظف</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase">العنوان</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase">النوع</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase">المبلغ</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase">التاريخ</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase">الحالة</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase">إجراءات</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-slate-200">
                    @forelse($deductions as $d)
                        <tr class="hover:bg-slate-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-slate-900">{{ $d->deduction_number }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900">{{ $d->employee->name ?? '—' }}</td>
                            <td class="px-6 py-4 text-sm text-slate-900">{{ Str::limit($d->title, 40) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $typeLabels = ['tax' => 'ضريبة', 'insurance' => 'تأمين', 'loan' => 'قرض', 'penalty' => 'غرامة', 'other' => 'أخرى'];
                                @endphp
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-slate-100 text-slate-800">{{ $typeLabels[$d->type] ?? $d->type }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-rose-600">{{ number_format($d->amount, 2) }} ج.م</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900">{{ $d->deduction_date->format('Y-m-d') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($d->status === 'pending')
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-amber-100 text-amber-800">معلقة</span>
                                @elseif($d->status === 'applied')
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-emerald-100 text-emerald-800">مطبقة</span>
                                @else
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-slate-100 text-slate-800">ملغاة</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <a href="{{ route('admin.employee-deductions.show', $d) }}" class="text-rose-600 hover:text-rose-800 font-medium ml-2">عرض</a>
                                <a href="{{ route('admin.employee-deductions.edit', $d) }}" class="text-sky-600 hover:text-sky-800 font-medium ml-2">تعديل</a>
                                <form action="{{ route('admin.employee-deductions.destroy', $d) }}" method="POST" class="inline" onsubmit="return confirm('هل تريد حذف هذا الخصم؟');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800 font-medium">حذف</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-slate-500">لا توجد خصومات. <a href="{{ route('admin.employee-deductions.create') }}" class="text-rose-600 hover:underline font-medium">إضافة خصم جديد</a></td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($deductions->hasPages())
            <div class="px-6 py-4 border-t border-slate-200">
                {{ $deductions->withQueryString()->links() }}
            </div>
        @endif
    </section>
</div>
@endsection
