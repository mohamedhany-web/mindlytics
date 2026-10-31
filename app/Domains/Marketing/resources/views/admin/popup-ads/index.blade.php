@extends('layouts.admin')
@section('title', 'الإعلانات المنبثقة - الصفحة الرئيسية')
@section('header', 'الإعلانات المنبثقة (الصفحة الرئيسية)')
@section('content')
<div class="w-full space-y-6">
    <div class="rounded-3xl bg-white/95 backdrop-blur border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-5 py-6 sm:px-8 border-b border-slate-200 flex flex-wrap items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">الإعلانات المنبثقة</h1>
                <p class="text-slate-500 mt-1">إعلان نصي يظهر كـ Pop-up على الصفحة الرئيسية. حدد المدة وعدد مرات الظهور لكل زائر.</p>
            </div>
            <a href="{{ route('admin.popup-ads.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-sky-500 to-blue-600 hover:from-sky-600 hover:to-blue-700 text-white rounded-xl font-semibold shadow-lg shadow-sky-500/30 transition-all">
                <i class="fas fa-plus"></i>
                <span>إعلان جديد</span>
            </a>
        </div>
        <div class="p-5 sm:p-8">
            @if(session('success'))
                <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 rounded-xl text-emerald-800">{{ session('success') }}</div>
            @endif
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-right text-sm">
                    <thead class="bg-slate-50">
                        <tr class="text-xs font-semibold uppercase text-slate-500">
                            <th class="px-4 py-3">العنوان</th>
                            <th class="px-4 py-3">مقتطف النص</th>
                            <th class="px-4 py-3">الفترة</th>
                            <th class="px-4 py-3">عدد الظهور/زائر</th>
                            <th class="px-4 py-3">الحالة</th>
                            <th class="px-4 py-3">إجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @forelse($ads as $ad)
                        <tr class="hover:bg-slate-50">
                            <td class="px-4 py-3 font-medium text-slate-800">{{ $ad->title }}</td>
                            <td class="px-4 py-3 text-slate-600 max-w-[200px] truncate">{{ Str::limit(strip_tags($ad->body ?? ''), 50) }}</td>
                            <td class="px-4 py-3 text-slate-600">{{ $ad->starts_at->format('Y-m-d') }} ← → {{ $ad->ends_at->format('Y-m-d') }}</td>
                            <td class="px-4 py-3">{{ $ad->max_views_per_visitor }}</td>
                            <td class="px-4 py-3">
                                @if($ad->is_active && $ad->starts_at <= now() && $ad->ends_at >= now())
                                    <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-semibold bg-emerald-100 text-emerald-700"><i class="fas fa-eye"></i> يعرض الآن</span>
                                @elseif($ad->is_active)
                                    <span class="rounded-full px-2.5 py-0.5 text-xs font-semibold bg-sky-100 text-sky-700">نشط</span>
                                @else
                                    <span class="rounded-full px-2.5 py-0.5 text-xs font-semibold bg-slate-100 text-slate-600">معطل</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <a href="{{ route('admin.popup-ads.edit', $ad) }}" class="text-sky-600 hover:text-sky-700 font-medium ml-2">تعديل</a>
                                <form action="{{ route('admin.popup-ads.destroy', $ad) }}" method="POST" class="inline" onsubmit="return confirm('حذف هذا الإعلان؟');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-rose-600 hover:text-rose-700 font-medium">حذف</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center text-slate-500">
                                <i class="fas fa-bullhorn text-4xl text-slate-300 mb-3 block"></i>
                                <p>لا توجد إعلانات. <a href="{{ route('admin.popup-ads.create') }}" class="text-sky-600 hover:underline">أضف إعلاناً جديداً</a></p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $ads->links() }}</div>
        </div>
    </div>
</div>
@endsection
