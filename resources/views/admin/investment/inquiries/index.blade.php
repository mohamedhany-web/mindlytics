@extends('layouts.admin')

@section('title', 'طلبات المستثمرين')
@section('header', 'طلبات المستثمرين')

@section('content')
@include('admin.investment._styles')

<div class="p-3 sm:p-4 md:p-6 space-y-4 sm:space-y-6" style="background: #f8fafc; min-height: 100vh;">
    @include('admin.investment._alerts')

    @include('admin.investment._header', [
        'title' => 'طلبات المستثمرين',
        'subtitle' => 'متابعة ومراجعة طلبات الدخول في الاستثمار',
        'icon' => 'fas fa-handshake',
    ])

    @include('admin.investment._nav', ['active' => 'inquiries'])

    <section class="{{ $invSectionClass }}">
        @include('admin.investment._section-head', [
            'icon' => 'fas fa-filter',
            'title' => 'البحث والفلترة',
            'subtitle' => 'ابحث بالاسم أو البريد أو الهاتف',
        ])
        <div class="px-6 py-5">
            <form method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                <div class="sm:col-span-2">
                    <label class="{{ $invLabelClass }}"><i class="fas fa-search text-amber-600 text-sm"></i> البحث</label>
                    <input type="search" name="search" value="{{ request('search') }}" placeholder="اسم، بريد، هاتف..." class="{{ $invInputClass }}">
                </div>
                <div>
                    <label class="{{ $invLabelClass }}"><i class="fas fa-flag text-amber-600 text-sm"></i> الحالة</label>
                    <select name="status" class="{{ $invSelectClass }}">
                        <option value="">كل الحالات</option>
                        @foreach($statusLabels as $val => $lbl)
                            <option value="{{ $val }}" @selected(request('status') === $val)>{{ $lbl }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="{{ $invLabelClass }}"><i class="fas fa-layer-group text-amber-600 text-sm"></i> الخطة</label>
                    <select name="plan_id" class="{{ $invSelectClass }}">
                        <option value="">كل الخطط</option>
                        @foreach($plans as $p)
                            <option value="{{ $p->id }}" @selected(request('plan_id') == $p->id)>{{ $p->title }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit" class="flex-1 {{ $invBtnPrimary }}"><i class="fas fa-search"></i><span>بحث</span></button>
                    @if(request()->anyFilled(['search', 'status', 'plan_id']))
                        <a href="{{ route('admin.investment.inquiries.index') }}" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-semibold"><i class="fas fa-times"></i></a>
                    @endif
                </div>
            </form>
        </div>
    </section>

    <section class="{{ $invSectionClass }}">
        @include('admin.investment._section-head', [
            'icon' => 'fas fa-inbox',
            'title' => 'قائمة الطلبات',
            'subtitle' => '<span class="font-bold text-amber-600">' . $inquiries->total() . '</span> طلب',
        ])
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr class="text-xs font-semibold uppercase tracking-widest text-slate-700">
                        <th class="px-6 py-4 text-right">المستثمر</th>
                        <th class="px-6 py-4 text-right">النوع</th>
                        <th class="px-6 py-4 text-right">الخطة</th>
                        <th class="px-6 py-4 text-center">المبلغ</th>
                        <th class="px-6 py-4 text-center">الحالة</th>
                        <th class="px-6 py-4 text-center">التاريخ</th>
                        <th class="px-6 py-4 text-center">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white text-sm">
                    @forelse($inquiries as $inq)
                        <tr class="inv-table-row">
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-900">{{ $inq->full_name }}</div>
                                <div class="text-xs text-slate-500">{{ $inq->email }} · {{ $inq->phone }}</div>
                            </td>
                            <td class="px-6 py-4">{{ $inq->investorTypeLabel() }}</td>
                            <td class="px-6 py-4">{{ $inq->plan?->title ?? '—' }}</td>
                            <td class="px-6 py-4 text-center font-mono tabular-nums">{{ $inq->proposed_amount ? number_format($inq->proposed_amount, 0) . ' ' . $inq->currency : '—' }}</td>
                            <td class="px-6 py-4 text-center"><span class="px-2 py-0.5 rounded-full text-xs bg-amber-100 text-amber-900">{{ $inq->statusLabel() }}</span></td>
                            <td class="px-6 py-4 text-center text-xs text-slate-500">{{ $inq->created_at?->format('Y-m-d H:i') }}</td>
                            <td class="px-6 py-4 text-center">
                                <a href="{{ route('admin.investment.inquiries.show', $inq) }}" class="w-9 h-9 inline-flex items-center justify-center bg-slate-50 hover:bg-slate-100 text-slate-600 rounded-lg shadow-sm" title="عرض"><i class="fas fa-eye text-sm"></i></a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-6 py-16 text-center text-slate-500 font-medium">لا توجد طلبات</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($inquiries->hasPages())<div class="px-6 py-4 border-t border-slate-200 bg-slate-50">{{ $inquiries->links() }}</div>@endif
    </section>
</div>
@endsection
