@extends('layouts.admin')

@section('title', 'تخصصات موظفي السيلز')
@section('header', 'المبيعات — التخصصات')

@section('content')
<div class="space-y-6">
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-4 py-4 bg-slate-50 border-b border-slate-200 flex flex-col sm:flex-row sm:justify-between gap-3">
            <div>
                <h2 class="text-xl font-black text-slate-900">تخصصات موظفي السيلز</h2>
                <p class="text-xs text-slate-600">اربط كل موظف بأنواع الاهتمام التي يتقنها لتسهيل التوزيع على المدير.</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('admin.sales.interest-types.index') }}" class="px-3 py-2 text-sm font-semibold rounded-xl border">أنواع الاهتمام</a>
                <a href="{{ route('admin.sales.distribution.index') }}" class="px-3 py-2 text-sm font-semibold rounded-xl bg-emerald-600 text-white">لوحة التوزيع</a>
            </div>
        </div>
    </section>

    @forelse($reps as $rep)
        @php $selected = $rep->salesInterestTypes->pluck('id')->all(); @endphp
        <section class="rounded-2xl bg-white border border-slate-200 shadow p-4">
            <form method="post" action="{{ route('admin.sales.specialties.update', $rep) }}">
                @csrf @method('PUT')
                <div class="flex flex-col lg:flex-row lg:items-center gap-4">
                    <div class="min-w-[180px]">
                        <p class="font-black text-slate-900">{{ $rep->name }}</p>
                        <p class="text-xs text-slate-500">{{ $rep->email }}</p>
                    </div>
                    <div class="flex-1 flex flex-wrap gap-2">
                        @foreach($types as $type)
                            <label class="inline-flex items-center gap-2 rounded-xl border px-3 py-2 text-sm cursor-pointer hover:bg-slate-50"
                                   style="border-color: {{ in_array($type->id, $selected) ? $type->color : '#e2e8f0' }}">
                                <input type="checkbox" name="interest_type_ids[]" value="{{ $type->id }}" @checked(in_array($type->id, $selected))>
                                <span class="w-2.5 h-2.5 rounded-full" style="background:{{ $type->color }}"></span>
                                {{ $type->name_ar }}
                            </label>
                        @endforeach
                    </div>
                    <button class="rounded-xl bg-slate-900 text-white px-4 py-2 text-sm font-semibold whitespace-nowrap">حفظ</button>
                </div>
            </form>
        </section>
    @empty
        <div class="rounded-2xl border bg-white p-8 text-center text-slate-500">لا يوجد موظفو مبيعات نشطون.</div>
    @endforelse
</div>
@endsection
