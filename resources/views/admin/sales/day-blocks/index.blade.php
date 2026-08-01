@extends('layouts.admin')
@section('title', 'جدول يوم السيلز (Blocks)')
@section('header', 'جدول يوم السيلز — Blocks')
@section('content')
<div class="w-full space-y-6">
    <div class="rounded-3xl bg-white/95 border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-5 py-6 sm:px-8 border-b border-slate-200">
            <h1 class="text-2xl font-bold text-slate-900">جدول الإنتاجية اليومي</h1>
            <p class="text-slate-500 mt-1">Blocks تظهر لموظفي السيلز ومديريهم — Call / Follow-up / Closing / Report.</p>
        </div>
        <div class="p-5 sm:p-8 space-y-6">
            @if(session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-xl text-emerald-800">{{ session('success') }}</div>
            @endif
            @if($errors->any())
                <div class="p-4 bg-rose-50 border border-rose-200 rounded-xl text-rose-800 text-sm">
                    <ul class="list-disc list-inside">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                </div>
            @endif

            <form method="post" action="{{ route('admin.sales.day-blocks.store') }}" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3 p-4 rounded-2xl border border-dashed border-slate-300 bg-slate-50">
                @csrf
                <input type="text" name="name" placeholder="اسم البلوك" required class="rounded-xl border-slate-200 text-sm">
                <input type="text" name="code" placeholder="code (call_block_1)" required class="rounded-xl border-slate-200 text-sm">
                <input type="time" name="start_time" required class="rounded-xl border-slate-200 text-sm">
                <input type="time" name="end_time" required class="rounded-xl border-slate-200 text-sm">
                <select name="activity_type" required class="rounded-xl border-slate-200 text-sm">
                    @foreach(\App\Models\SalesDayBlock::ACTIVITY_TYPES as $k => $label)
                        <option value="{{ $k }}">{{ $label }}</option>
                    @endforeach
                </select>
                <input type="text" name="goal_text" placeholder="هدف البلوك" class="rounded-xl border-slate-200 text-sm md:col-span-2">
                <input type="number" name="sort_order" value="100" class="rounded-xl border-slate-200 text-sm" placeholder="ترتيب">
                <label class="inline-flex items-center gap-2 text-sm"><input type="checkbox" name="is_active" value="1" checked class="rounded"> نشط</label>
                <button type="submit" class="rounded-xl bg-sky-600 text-white font-bold text-sm px-4 py-2">إضافة بلوك</button>
            </form>

            <div class="space-y-3">
                @forelse($blocks as $block)
                    <div class="rounded-2xl border border-slate-200 bg-white p-4">
                        <form method="post" action="{{ route('admin.sales.day-blocks.update', $block) }}" class="grid grid-cols-1 lg:grid-cols-12 gap-3 items-end">
                            @csrf
                            @method('PUT')
                            <div class="lg:col-span-1">
                                <label class="block text-[11px] font-semibold text-slate-500 mb-1">ترتيب</label>
                                <input type="number" name="sort_order" value="{{ $block->sort_order }}" class="w-full rounded-lg border-slate-200 text-sm">
                            </div>
                            <div class="lg:col-span-2">
                                <label class="block text-[11px] font-semibold text-slate-500 mb-1">الاسم / code</label>
                                <input type="text" name="name" value="{{ $block->name }}" class="w-full rounded-lg border-slate-200 text-sm font-semibold">
                                <input type="text" name="code" value="{{ $block->code }}" class="w-full rounded-lg border-slate-200 text-xs mt-1">
                            </div>
                            <div class="lg:col-span-2">
                                <label class="block text-[11px] font-semibold text-slate-500 mb-1">من — إلى</label>
                                <div class="flex gap-1">
                                    <input type="time" name="start_time" value="{{ $block->startTimeHm() }}" class="w-full rounded-lg border-slate-200 text-sm">
                                    <input type="time" name="end_time" value="{{ $block->endTimeHm() }}" class="w-full rounded-lg border-slate-200 text-sm">
                                </div>
                            </div>
                            <div class="lg:col-span-2">
                                <label class="block text-[11px] font-semibold text-slate-500 mb-1">النوع</label>
                                <select name="activity_type" class="w-full rounded-lg border-slate-200 text-sm">
                                    @foreach(\App\Models\SalesDayBlock::ACTIVITY_TYPES as $k => $label)
                                        <option value="{{ $k }}" @selected($block->activity_type === $k)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="lg:col-span-3">
                                <label class="block text-[11px] font-semibold text-slate-500 mb-1">الهدف</label>
                                <input type="text" name="goal_text" value="{{ $block->goal_text }}" class="w-full rounded-lg border-slate-200 text-sm">
                            </div>
                            <div class="lg:col-span-2 flex flex-wrap items-center gap-2">
                                <label class="text-xs inline-flex items-center gap-1"><input type="checkbox" name="is_active" value="1" @checked($block->is_active) class="rounded"> نشط</label>
                                <button class="px-3 py-1.5 rounded-lg bg-slate-800 text-white text-xs font-bold">حفظ</button>
                            </div>
                        </form>
                        <form method="post" action="{{ route('admin.sales.day-blocks.destroy', $block) }}" onsubmit="return confirm('حذف البلوك؟');" class="mt-2">
                            @csrf
                            @method('DELETE')
                            <button class="px-3 py-1.5 rounded-lg text-rose-600 text-xs font-bold hover:bg-rose-50">حذف</button>
                        </form>
                    </div>
                @empty
                    <p class="text-sm text-slate-500 text-center py-8">لا توجد بلوكات بعد — أضف من النموذج أعلاه أو شغّل الـ migration.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
