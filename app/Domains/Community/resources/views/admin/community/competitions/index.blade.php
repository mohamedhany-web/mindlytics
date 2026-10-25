@extends('layouts.admin')

@section('title', __('admin.community_competitions'))
@section('header', __('admin.community_competitions'))

@section('content')
<div class="w-full">
    <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
        <h2 class="text-xl font-black text-slate-900">قائمة المسابقات</h2>
        <a href="{{ route('admin.community.competitions.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-cyan-600 text-white font-bold hover:bg-cyan-700 transition-colors">
            <i class="fas fa-plus"></i>
            <span>إضافة مسابقة</span>
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-right">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="px-4 py-3 text-sm font-bold text-slate-700">العنوان</th>
                        <th class="px-4 py-3 text-sm font-bold text-slate-700">البداية</th>
                        <th class="px-4 py-3 text-sm font-bold text-slate-700">النهاية</th>
                        <th class="px-4 py-3 text-sm font-bold text-slate-700">الحالة</th>
                        <th class="px-4 py-3 text-sm font-bold text-slate-700 w-40">إجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($competitions as $competition)
                        <tr class="hover:bg-slate-50/50">
                            <td class="px-4 py-3 font-semibold text-slate-900">{{ $competition->title }}</td>
                            <td class="px-4 py-3 text-sm text-slate-600">{{ $competition->start_at?->translatedFormat('Y-m-d') ?? '—' }}</td>
                            <td class="px-4 py-3 text-sm text-slate-600">{{ $competition->end_at?->translatedFormat('Y-m-d') ?? '—' }}</td>
                            <td class="px-4 py-3">
                                @if($competition->is_active)
                                    <span class="px-2 py-1 rounded-lg bg-green-100 text-green-700 text-xs font-bold">نشط</span>
                                @else
                                    <span class="px-2 py-1 rounded-lg bg-slate-100 text-slate-600 text-xs font-bold">معطّل</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('admin.community.competitions.edit', $competition) }}" class="p-2 rounded-lg bg-slate-100 text-slate-600 hover:bg-slate-200" title="تعديل"><i class="fas fa-edit"></i></a>
                                    <form action="{{ route('admin.community.competitions.destroy', $competition) }}" method="POST" class="inline" onsubmit="return confirm('حذف هذه المسابقة؟');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 rounded-lg bg-red-50 text-red-600 hover:bg-red-100" title="حذف"><i class="fas fa-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-12 text-center text-slate-500">لا توجد مسابقات. <a href="{{ route('admin.community.competitions.create') }}" class="text-cyan-600 font-bold hover:underline">إضافة مسابقة</a></td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($competitions->hasPages())
            <div class="px-4 py-3 border-t border-slate-200">{{ $competitions->links() }}</div>
        @endif
    </div>
</div>
@endsection
