@extends('layouts.admin')

@section('title', __('admin.community_datasets'))
@section('header', __('admin.community_datasets'))

@section('content')
<div class="w-full">
    <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
        <h2 class="text-xl font-black text-slate-900">قائمة مجموعات البيانات</h2>
        <a href="{{ route('admin.community.datasets.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-blue-600 text-white font-bold hover:bg-blue-700 transition-colors">
            <i class="fas fa-plus"></i>
            <span>إضافة مجموعة بيانات</span>
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-right">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="px-4 py-3 text-sm font-bold text-slate-700">العنوان</th>
                        <th class="px-4 py-3 text-sm font-bold text-slate-700">الرابط / الملف</th>
                        <th class="px-4 py-3 text-sm font-bold text-slate-700">الحجم</th>
                        <th class="px-4 py-3 text-sm font-bold text-slate-700">الحالة</th>
                        <th class="px-4 py-3 text-sm font-bold text-slate-700 w-40">إجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($datasets as $dataset)
                        <tr class="hover:bg-slate-50/50">
                            <td class="px-4 py-3 font-semibold text-slate-900">{{ $dataset->title }}</td>
                            <td class="px-4 py-3 text-sm text-slate-600">
                                @if($dataset->file_url)
                                    <a href="{{ $dataset->file_url }}" target="_blank" rel="noopener" class="text-cyan-600 hover:underline truncate block max-w-xs">رابط</a>
                                @else
                                    —
                                @endif
                            </td>
                            <td class="px-4 py-3 text-sm text-slate-600">{{ $dataset->file_size ?? '—' }}</td>
                            <td class="px-4 py-3">
                                @if($dataset->is_active)
                                    <span class="px-2 py-1 rounded-lg bg-green-100 text-green-700 text-xs font-bold">نشط</span>
                                @else
                                    <span class="px-2 py-1 rounded-lg bg-slate-100 text-slate-600 text-xs font-bold">معطّل</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('admin.community.datasets.edit', $dataset) }}" class="p-2 rounded-lg bg-slate-100 text-slate-600 hover:bg-slate-200" title="تعديل"><i class="fas fa-edit"></i></a>
                                    <form action="{{ route('admin.community.datasets.destroy', $dataset) }}" method="POST" class="inline" onsubmit="return confirm('حذف مجموعة البيانات هذه؟');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 rounded-lg bg-red-50 text-red-600 hover:bg-red-100" title="حذف"><i class="fas fa-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-12 text-center text-slate-500">لا توجد مجموعات بيانات. <a href="{{ route('admin.community.datasets.create') }}" class="text-cyan-600 font-bold hover:underline">إضافة مجموعة بيانات</a></td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($datasets->hasPages())
            <div class="px-4 py-3 border-t border-slate-200">{{ $datasets->links() }}</div>
        @endif
    </div>
</div>
@endsection
