@extends('community::portal.layouts.app')

@section('title', 'نماذجي')
@section('content')
<div class="w-full space-y-6">
    @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-100 border border-emerald-300 text-emerald-800">{{ session('success') }}</div>
    @endif

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <h1 class="text-2xl font-black text-slate-900">نماذجي (Model Zoo)</h1>
        <a href="{{ route('community.contributor.models.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-amber-600 text-white font-bold hover:bg-amber-700 transition-colors shadow-md">
            <i class="fas fa-plus"></i>
            <span>إضافة نموذج جديد</span>
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        @if($models->isEmpty())
            <div class="p-12 text-center text-slate-500">
                <div class="w-16 h-16 rounded-2xl bg-amber-50 text-amber-500 flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-brain text-3xl"></i>
                </div>
                <p>لا توجد نماذج مرفوعة حتى الآن.</p>
                <p class="text-sm mt-1">ارفع نموذجك المدرب مع شرح المنهجية وربطه بمجموعة بيانات (إن وجدت). التخزين على Cloudflare.</p>
                <a href="{{ route('community.contributor.models.create') }}" class="inline-flex items-center gap-2 mt-4 px-5 py-2.5 rounded-xl bg-amber-600 text-white font-bold hover:bg-amber-700">إضافة نموذج</a>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-right">
                    <thead class="bg-slate-50 border-b border-slate-200">
                        <tr>
                            <th class="py-3 px-4 text-sm font-bold text-slate-700">العنوان</th>
                            <th class="py-3 px-4 text-sm font-bold text-slate-700">الداتاسيت</th>
                            <th class="py-3 px-4 text-sm font-bold text-slate-700">التاريخ</th>
                            <th class="py-3 px-4 text-sm font-bold text-slate-700">الحالة</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($models as $m)
                        <tr class="hover:bg-slate-50/50">
                            <td class="py-3 px-4 font-medium text-slate-900">{{ $m->title }}</td>
                            <td class="py-3 px-4 text-slate-600 text-sm">
                                @if($m->dataset)
                                    {{ Str::limit($m->dataset->title, 25) }}
                                @else
                                    —
                                @endif
                            </td>
                            <td class="py-3 px-4 text-slate-600 text-sm">{{ $m->created_at->format('Y-m-d') }}</td>
                            <td class="py-3 px-4">
                                @if($m->status === \App\Domains\Community\Models\CommunityModel::STATUS_PENDING)
                                    <span class="inline-flex px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-700">قيد المراجعة</span>
                                @elseif($m->status === \App\Domains\Community\Models\CommunityModel::STATUS_APPROVED)
                                    <span class="inline-flex px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-700">معتمد</span>
                                    <a href="{{ route('community.models.show', $m) }}" class="mr-2 text-cyan-600 text-xs font-bold hover:underline">عرض</a>
                                @else
                                    <span class="inline-flex px-3 py-1 rounded-full text-xs font-bold bg-red-100 text-red-700">مرفوض</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-4 py-3 border-t border-slate-200">
                {{ $models->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
