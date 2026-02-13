@extends('layouts.admin')
@section('title', 'محافظ المنصة - التقارير المحاسبية')
@section('header', 'محافظ المنصة')
@section('content')
<div class="w-full space-y-6">
    <nav class="text-sm text-slate-500 mb-2">
        <a href="{{ route('admin.accounting.reports') }}" class="text-sky-600 hover:text-sky-700">التقارير المحاسبية</a>
        <span class="mx-1">/</span>
        <span class="text-slate-700">محافظ المنصة</span>
    </nav>
    <section class="rounded-3xl bg-white/95 backdrop-blur border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-5 py-6 sm:px-8 border-b border-slate-200">
            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-4">
                <h2 class="text-xl font-bold text-slate-900">محافظ المنصة</h2>
                <a href="{{ route('admin.accounting.reports.export', array_merge(request()->query(), ['type' => 'wallets'])) }}" class="inline-flex items-center gap-2 rounded-2xl bg-emerald-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-emerald-700">
                    <i class="fas fa-file-excel"></i> تصدير Excel
                </a>
            </div>
            <p class="text-sm text-slate-500">إجمالي المحافظ: {{ $stats['wallet_stats']['total_wallets'] }} — إجمالي الأرصدة: {{ number_format($stats['wallet_stats']['total_balance'], 2) }} ج.م</p>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200 text-right text-sm">
                <thead class="bg-slate-50">
                    <tr class="text-xs font-semibold uppercase text-slate-500">
                        <th class="px-4 py-3">اسم المحفظة</th>
                        <th class="px-4 py-3">النوع</th>
                        <th class="px-4 py-3">رقم الحساب</th>
                        <th class="px-4 py-3">الرصيد</th>
                        <th class="px-4 py-3">المعلق</th>
                        <th class="px-4 py-3">معاملات</th>
                        <th class="px-4 py-3">نشطة</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($items as $w)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3"><a href="{{ route('admin.wallets.show', $w) }}" class="font-semibold text-sky-600 hover:text-sky-700">{{ $w->name ?? '—' }}</a></td>
                        <td class="px-4 py-3">{{ \App\Models\Wallet::typeLabel($w->type ?? '') }}</td>
                        <td class="px-4 py-3 font-mono">{{ $w->account_number ?? '—' }}</td>
                        <td class="px-4 py-3 font-semibold text-emerald-600">{{ number_format($w->balance, 2) }} ج.م</td>
                        <td class="px-4 py-3">{{ number_format($w->pending_balance ?? 0, 2) }} ج.م</td>
                        <td class="px-4 py-3">{{ $w->transactions_count ?? 0 }}</td>
                        <td class="px-4 py-3"><span class="rounded-full px-2 py-0.5 text-xs font-semibold {{ $w->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">{{ $w->is_active ? 'نعم' : 'لا' }}</span></td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="px-4 py-8 text-center text-slate-500">لا توجد محافظ.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($items->hasPages())
        <div class="px-5 py-4 border-t border-slate-200">{{ $items->links() }}</div>
        @endif
    </section>
</div>
@endsection
