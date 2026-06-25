@extends('layouts.admin')

@section('title', 'أكواد خصم الورش - Mindlytics')
@section('header', 'أكواد خصم الورش')

@section('content')
<div class="p-3 sm:p-4 md:p-6 space-y-4 sm:space-y-6" style="background:#f8fafc;min-height:100vh;">
    @include('admin.marketing._flash')

    @include('admin.marketing._tabs', ['active' => 'promo'])

    <section class="rounded-2xl bg-white/95 border-2 border-slate-200/50 shadow-xl overflow-hidden">
        <div class="px-5 py-6 sm:px-8 border-b border-slate-200 bg-gradient-to-r from-violet-50 to-white flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-violet-500 to-purple-600 flex items-center justify-center text-white shadow-lg">
                    <i class="fas fa-ticket-alt text-xl"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-black text-slate-900">أكواد خصم الورش</h2>
                    <p class="text-sm text-slate-600 mt-1">كود مرتبط بورشة — الطالب يفعّله عند التسجيل ويحصل على خصم على الكورسات</p>
                </div>
            </div>
            <a href="{{ route('admin.workshop-promo-codes.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-violet-600 to-purple-600 px-5 py-3 text-sm font-semibold text-white shadow-lg hover:shadow-xl transition-all">
                <i class="fas fa-plus"></i> كود جديد
            </a>
        </div>
    </section>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach([
            ['label' => 'إجمالي الأكواد', 'value' => $stats['total'], 'icon' => 'fa-tags', 'color' => 'violet'],
            ['label' => 'أكواد نشطة', 'value' => $stats['active'], 'icon' => 'fa-bolt', 'color' => 'emerald'],
            ['label' => 'تفعيلات', 'value' => $stats['activations'], 'icon' => 'fa-user-check', 'color' => 'sky'],
            ['label' => 'استُخدمت', 'value' => $stats['used'], 'icon' => 'fa-shopping-cart', 'color' => 'amber'],
        ] as $card)
            <div class="rounded-2xl bg-white border border-slate-200 p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-slate-500 mb-1">{{ $card['label'] }}</p>
                        <p class="text-3xl font-black text-slate-900">{{ number_format($card['value']) }}</p>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-{{ $card['color'] }}-100 text-{{ $card['color'] }}-600 flex items-center justify-center">
                        <i class="fas {{ $card['icon'] }}"></i>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
        <form method="GET" class="p-4 border-b border-slate-100 flex flex-wrap gap-3 items-end">
            <div class="flex-1 min-w-[200px]">
                <label class="text-xs font-semibold text-slate-500">بحث</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="كود أو عنوان..."
                       class="w-full mt-1 rounded-lg border border-slate-200 px-3 py-2 text-sm">
            </div>
            <div>
                <label class="text-xs font-semibold text-slate-500">الحالة</label>
                <select name="status" class="mt-1 rounded-lg border border-slate-200 px-3 py-2 text-sm">
                    <option value="">الكل</option>
                    <option value="active" @selected(request('status') === 'active')>نشط</option>
                    <option value="expired" @selected(request('status') === 'expired')>منتهي</option>
                </select>
            </div>
            <div>
                <label class="text-xs font-semibold text-slate-500">الورشة</label>
                <select name="workshop_id" class="mt-1 rounded-lg border border-slate-200 px-3 py-2 text-sm">
                    <option value="">الكل</option>
                    @foreach($workshops as $ws)
                        <option value="{{ $ws->id }}" @selected((string) request('workshop_id') === (string) $ws->id)>{{ $ws->title }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="px-4 py-2 bg-slate-800 text-white rounded-lg text-sm font-semibold">تصفية</button>
        </form>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 text-slate-600">
                    <tr>
                        <th class="px-4 py-3 text-right font-bold">الكود</th>
                        <th class="px-4 py-3 text-right font-bold">الورشة</th>
                        <th class="px-4 py-3 text-right font-bold">الخصم</th>
                        <th class="px-4 py-3 text-right font-bold">التفعيلات</th>
                        <th class="px-4 py-3 text-right font-bold">ينتهي</th>
                        <th class="px-4 py-3 text-right font-bold">الحالة</th>
                        <th class="px-4 py-3 text-center font-bold">إجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($promoCodes as $promo)
                        <tr class="hover:bg-slate-50/80">
                            <td class="px-4 py-3">
                                <div class="font-mono font-bold text-violet-700">{{ $promo->code }}</div>
                                <div class="text-xs text-slate-500">{{ $promo->title }}</div>
                            </td>
                            <td class="px-4 py-3 text-slate-700">{{ $promo->workshop?->title ?? '—' }}</td>
                            <td class="px-4 py-3 font-semibold">{{ $promo->discountLabel() }}</td>
                            <td class="px-4 py-3">
                                <span class="font-bold">{{ $promo->activations_count }}</span>
                                @if($promo->max_activations)
                                    <span class="text-slate-400">/ {{ $promo->max_activations }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-slate-600">{{ $promo->expiryLabel() }}</td>
                            <td class="px-4 py-3">
                                @if($promo->isValid())
                                    <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700">نشط</span>
                                @else
                                    <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-bold bg-slate-100 text-slate-600">منتهي</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-center gap-1">
                                    <a href="{{ route('admin.workshop-promo-codes.show', $promo) }}" class="w-8 h-8 flex items-center justify-center rounded-lg bg-emerald-50 text-emerald-600 hover:bg-emerald-100" title="عرض"><i class="fas fa-eye text-xs"></i></a>
                                    <a href="{{ route('admin.workshop-promo-codes.edit', $promo) }}" class="w-8 h-8 flex items-center justify-center rounded-lg bg-amber-50 text-amber-600 hover:bg-amber-100" title="تعديل"><i class="fas fa-edit text-xs"></i></a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-16 text-center text-slate-500">
                                <i class="fas fa-ticket-alt text-4xl text-slate-300 mb-3 block"></i>
                                لا توجد أكواد — أنشئ كوداً مرتبطاً بورشة
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($promoCodes->hasPages())
            <div class="px-4 py-3 border-t border-slate-100">{{ $promoCodes->links() }}</div>
        @endif
    </div>
</div>
@endsection
