@extends('layouts.admin')

@section('title', 'الاجتماعات والورش')

@section('content')
<div class="p-6 lg:p-8">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-black text-slate-900 flex items-center gap-2">
                <i class="fas fa-people-arrows text-blue-600"></i>
                <span>الاجتماعات / الورش</span>
            </h1>
            <p class="text-sm text-slate-500 mt-1">
                إدارة ورش العمل وصفحات الحجز، ومتابعة الطلاب المسجلين وتحميل بياناتهم.
            </p>
        </div>
        <a href="{{ route('admin.workshops.create') }}"
           class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 px-5 py-3 text-sm font-semibold text-white shadow-lg hover:shadow-xl transition-all duration-200">
            <i class="fas fa-plus"></i>
            <span>إنشاء ورشة جديدة</span>
        </a>
    </div>

    @if(session('success'))
        <div class="mb-4 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center gap-2">
            <i class="fas fa-check-circle"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <table class="min-w-full divide-y divide-slate-200">
            <thead class="bg-slate-50">
                <tr>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">#</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">العنوان</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">التاريخ</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">المقاعد</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">الحالة</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">التحكم</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-slate-100">
                @forelse($workshops as $ws)
                    @php
                        $total = $ws->max_seats ?: null;
                        $registered = $ws->registrations()->count();
                        $remaining = $ws->remaining_seats;
                    @endphp
                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <td class="px-4 py-3 text-sm text-slate-500">{{ $ws->id }}</td>
                        <td class="px-4 py-3 text-sm">
                            <div class="font-semibold text-slate-900">{{ $ws->title }}</div>
                            <div class="text-xs text-slate-500 mt-0.5">
                                رابط الحجز:
                                <a href="{{ route('public.workshops.show', $ws->slug) }}" target="_blank" class="text-blue-600 hover:underline">
                                    {{ route('public.workshops.show', $ws->slug) }}
                                </a>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-sm text-slate-700">
                            @if($ws->starts_at)
                                <div>{{ $ws->starts_at->format('Y-m-d H:i') }}</div>
                                @if($ws->ends_at)
                                    <div class="text-xs text-slate-500">حتى {{ $ws->ends_at->format('Y-m-d H:i') }}</div>
                                @endif
                            @else
                                <span class="text-xs text-slate-400">غير محدد</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-sm text-slate-700">
                            @if($total)
                                <div class="font-semibold">{{ $registered }} / {{ $total }}</div>
                                <div class="text-xs text-slate-500">
                                    متبقي: {{ $remaining }}
                                </div>
                            @else
                                <span class="text-xs text-slate-400">غير محدود</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-sm">
                            @if($ws->is_active)
                                <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    نشطة
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">
                                    غير نشطة
                                </span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-sm">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('admin.workshops.show', $ws) }}" class="inline-flex items-center gap-1 px-2 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold">
                                    <i class="fas fa-eye"></i>
                                    عرض
                                </a>
                                <a href="{{ route('admin.workshops.edit', $ws) }}" class="inline-flex items-center gap-1 px-2 py-1.5 rounded-lg bg-amber-100 hover:bg-amber-200 text-amber-800 text-xs font-semibold">
                                    <i class="fas fa-edit"></i>
                                    تعديل
                                </a>
                                <form action="{{ route('admin.workshops.destroy', $ws) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذه الورشة وجميع الحجوزات المرتبطة بها؟');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center gap-1 px-2 py-1.5 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-semibold">
                                        <i class="fas fa-trash"></i>
                                        حذف
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-10 text-center text-slate-500 text-sm">
                            لا توجد ورش عمل حالياً. يمكنك إنشاء ورشة جديدة من الزر أعلى الصفحة.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="px-4 py-3 border-t border-slate-100">
            {{ $workshops->links() }}
        </div>
    </div>
</div>
@endsection

