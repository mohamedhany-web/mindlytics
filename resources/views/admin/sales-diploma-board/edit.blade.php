@extends('layouts.admin')

@section('title', 'تعديل '.$entry->name)
@section('header', 'تعديل توصيف الدبلومة')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="rounded-3xl bg-white border border-slate-200 shadow-lg p-6 sm:p-8">
        <div class="mb-6 flex flex-wrap items-start justify-between gap-3">
            <div>
                <a href="{{ route('admin.sales-diploma-board.index') }}" class="text-sm text-slate-500 hover:text-slate-800"><i class="fas fa-arrow-right ml-1"></i> رجوع للقائمة</a>
                <h1 class="text-2xl font-bold text-slate-900 mt-2">{{ $entry->name }}</h1>
                <p class="text-sm text-slate-500 mt-1">
                    <i class="fas fa-eye text-slate-400 ml-1"></i>
                    {{ number_format($entry->visits_count) }} زيارة مسجّلة
                </p>
            </div>
            @if($entry->landingUrl())
                <a href="{{ $entry->landingUrl() }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-emerald-50 text-emerald-800 border border-emerald-200 text-sm font-semibold">
                    <i class="fas fa-external-link-alt"></i> معاينة Landing
                </a>
            @endif
        </div>

        @if(session('success'))
            <div class="mb-4 p-4 bg-emerald-50 border border-emerald-200 rounded-xl text-emerald-800">{{ session('success') }}</div>
        @endif

        @if($errors->any())
            <div class="mb-4 p-4 bg-rose-50 border border-rose-200 rounded-xl text-rose-800 text-sm">
                <ul class="list-disc pr-5 space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.sales-diploma-board.update', $entry) }}" class="space-y-4">
            @csrf
            @method('PUT')
            @include('admin.sales-diploma-board._form', ['entry' => $entry, 'paths' => $paths])
            <div class="pt-4 flex flex-wrap gap-3">
                <button type="submit" class="px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold">حفظ التعديلات</button>
            </div>
        </form>
    </div>

    <div class="rounded-3xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-200 flex items-center justify-between gap-3">
            <h2 class="text-lg font-bold text-slate-900">زوار الصفحة</h2>
            <span class="text-xs font-semibold text-slate-500">آخر 50 زيارة</span>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 text-slate-600">
                    <tr>
                        <th class="px-4 py-3 text-right font-bold">الوقت</th>
                        <th class="px-4 py-3 text-right font-bold">IP</th>
                        <th class="px-4 py-3 text-right font-bold">المستخدم</th>
                        <th class="px-4 py-3 text-right font-bold">المصدر (Referer)</th>
                        <th class="px-4 py-3 text-right font-bold">المتصفح</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($recentVisits as $visit)
                        <tr class="hover:bg-slate-50/80">
                            <td class="px-4 py-3 text-slate-700 whitespace-nowrap">{{ optional($visit->created_at)->format('Y-m-d H:i') }}</td>
                            <td class="px-4 py-3 font-mono text-xs text-slate-600" dir="ltr">{{ $visit->ip_address ?: '—' }}</td>
                            <td class="px-4 py-3 text-slate-700">
                                @if($visit->user)
                                    {{ $visit->user->name }}
                                    <span class="block text-xs text-slate-400" dir="ltr">{{ $visit->user->email }}</span>
                                @else
                                    <span class="text-slate-400">زائر</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-xs text-slate-500 max-w-xs truncate" dir="ltr" title="{{ $visit->referer }}">{{ $visit->referer ?: '—' }}</td>
                            <td class="px-4 py-3 text-xs text-slate-500 max-w-xs truncate" title="{{ $visit->user_agent }}">{{ \Illuminate\Support\Str::limit($visit->user_agent, 60) ?: '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-10 text-center text-slate-500">لا زيارات بعد — انشر الصفحة وشارك الرابط.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
