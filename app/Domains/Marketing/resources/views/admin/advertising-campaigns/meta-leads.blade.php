@extends('layouts.admin')

@section('title', 'ليدز الحملة')
@section('header', 'ليدز حملة Meta')

@section('content')
@php
    $filters = $filters ?? ['q' => '', 'source_type' => '', 'import_batch' => ''];
    $stats = $stats ?? ['total' => 0, 'forms' => 0, 'inbox' => 0, 'unassigned' => 0];
@endphp
<div class="space-y-6">
    @if(session('success'))
        <div class="rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-900 px-4 py-3 text-sm font-medium">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="rounded-xl bg-rose-50 border border-rose-200 text-rose-900 px-4 py-3 text-sm font-medium">{{ session('error') }}</div>
    @endif

    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
        <div class="flex flex-col xl:flex-row xl:items-start xl:justify-between gap-4">
            <div>
                <nav class="text-sm text-gray-500 mb-1">
                    <a href="{{ route('admin.advertising-campaigns.index') }}" class="hover:text-blue-600">الحملات الإعلانية</a>
                    <span class="mx-2">/</span>
                    <span class="text-gray-700 font-semibold">ليدز الحملة</span>
                </nav>
                <h1 class="text-2xl font-bold text-gray-900">{{ $campaignName }}</h1>
                <p class="text-xs font-mono text-slate-400 mt-1" dir="ltr">{{ $metaCampaignId }}</p>
                <p class="text-sm text-gray-600 mt-2">السحب يتم في الخلفية عبر الطابور، والنتائج تُحفظ هنا للتصفح والتصدير.</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <form method="POST" action="{{ route('admin.advertising-campaigns.meta.sync-leads', $metaCampaignId) }}"
                      onsubmit="return confirm('جدولة سحب ليدز هذه الحملة في الخلفية؟')">
                    @csrf
                    <button type="submit"
                            class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-sky-600 hover:bg-sky-700 text-white text-sm font-bold">
                        <i class="fas fa-cloud-download-alt"></i> سحب جديد (Queue)
                    </button>
                </form>
                <a href="{{ route('admin.advertising-campaigns.meta.leads.export', array_merge(['metaCampaign' => $metaCampaignId], request()->query())) }}"
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold">
                    <i class="fas fa-file-excel"></i> تصدير Excel
                </a>
                <a href="{{ route('admin.meta-ads.campaigns.show', $metaCampaignId) }}"
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-800 border border-blue-100 text-sm font-bold">
                    <i class="fas fa-eye"></i> تفاصيل ميتا
                </a>
                @if($local)
                    <a href="{{ route('admin.advertising-campaigns.edit', $local) }}"
                       class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-indigo-50 hover:bg-indigo-100 text-indigo-800 border border-indigo-100 text-sm font-bold">
                        <i class="fas fa-cog"></i> إعدادات محلية
                    </a>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mt-6">
            <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
                <p class="text-[11px] text-slate-500 font-semibold">إجمالي الليدز</p>
                <p class="text-2xl font-black text-slate-900 mt-1">{{ number_format($stats['total']) }}</p>
            </div>
            <div class="rounded-xl border border-teal-200 bg-teal-50 px-4 py-3">
                <p class="text-[11px] text-teal-700 font-semibold">Lead Forms</p>
                <p class="text-2xl font-black text-teal-900 mt-1">{{ number_format($stats['forms']) }}</p>
            </div>
            <div class="rounded-xl border border-sky-200 bg-sky-50 px-4 py-3">
                <p class="text-[11px] text-sky-700 font-semibold">محادثات إعلان</p>
                <p class="text-2xl font-black text-sky-900 mt-1">{{ number_format($stats['inbox']) }}</p>
            </div>
            <div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3">
                <p class="text-[11px] text-amber-700 font-semibold">بدون إسناد</p>
                <p class="text-2xl font-black text-amber-900 mt-1">{{ number_format($stats['unassigned']) }}</p>
            </div>
        </div>
    </div>

    @if($latestRun)
        <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-5">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                <div>
                    <h2 class="text-sm font-black text-slate-800">آخر عملية سحب</h2>
                    <p class="text-xs text-slate-500 mt-1">
                        بواسطة {{ $latestRun->requester->name ?? '—' }}
                        · {{ optional($latestRun->created_at)->timezone(config('app.timezone'))->format('Y-m-d H:i') }}
                    </p>
                </div>
                <span class="inline-flex self-start px-3 py-1 rounded-full text-xs font-bold {{ $latestRun->statusBadgeClass() }}">
                    {{ $latestRun->statusLabel() }}
                </span>
            </div>
            <p class="text-sm text-slate-700 mt-3 leading-relaxed">{{ $latestRun->message ?: ($latestRun->error ?: '—') }}</p>
            <div class="mt-3 flex flex-wrap gap-3 text-xs text-slate-600">
                <span>جديد: <strong>{{ $latestRun->created_count }}</strong></span>
                <span>محدّث: <strong>{{ $latestRun->updated_count }}</strong></span>
                <span>محادثات: <strong>{{ $latestRun->conversations_linked }}</strong></span>
                <span>تخطي: <strong>{{ $latestRun->skipped_count }}</strong></span>
                @if($latestRun->import_batch)
                    <span class="font-mono" dir="ltr">batch: {{ $latestRun->import_batch }}</span>
                @endif
            </div>
            @if(in_array($latestRun->status, ['queued', 'running'], true))
                <p class="mt-3 text-xs text-amber-700 font-semibold">
                    <i class="fas fa-spinner fa-spin ml-1"></i>
                    السحب ما زال شغال في الخلفية — حدّث الصفحة بعد قليل.
                </p>
                <script>setTimeout(function () { window.location.reload(); }, 12000);</script>
            @endif
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-5">
        <form method="GET" class="grid md:grid-cols-4 gap-3 items-end">
            <div class="md:col-span-2">
                <label class="block text-xs font-semibold text-slate-600 mb-1">بحث</label>
                <input type="text" name="q" value="{{ $filters['q'] }}" placeholder="اسم / هاتف / بريد / Lead ID / Ad ID"
                       class="w-full rounded-lg border-gray-300 text-sm focus:ring-teal-500 focus:border-teal-500">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">النوع</label>
                <select name="source_type" class="w-full rounded-lg border-gray-300 text-sm focus:ring-teal-500 focus:border-teal-500">
                    <option value="">الكل</option>
                    <option value="form" @selected($filters['source_type'] === 'form')>Lead Form</option>
                    <option value="inbox" @selected($filters['source_type'] === 'inbox')>محادثة إعلان</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">دفعة الاستيراد</label>
                <select name="import_batch" class="w-full rounded-lg border-gray-300 text-sm focus:ring-teal-500 focus:border-teal-500">
                    <option value="">الكل</option>
                    @foreach($importBatches as $batch)
                        <option value="{{ $batch }}" @selected($filters['import_batch'] === $batch)>{{ $batch }}</option>
                    @endforeach
                </select>
            </div>
            <div class="md:col-span-4 flex flex-wrap gap-2">
                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-slate-800 text-white text-sm font-bold">
                    <i class="fas fa-filter"></i> تطبيق
                </button>
                <a href="{{ route('admin.advertising-campaigns.meta.leads', $metaCampaignId) }}"
                   class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-gray-100 text-gray-700 text-sm font-bold border border-gray-200">
                    مسح الفلاتر
                </a>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 text-slate-600 text-xs">
                <tr>
                    <th class="px-4 py-3 text-right font-bold">العميل</th>
                    <th class="px-4 py-3 text-right font-bold">الهاتف / البريد</th>
                    <th class="px-4 py-3 text-right font-bold">النوع</th>
                    <th class="px-4 py-3 text-right font-bold">المرحلة</th>
                    <th class="px-4 py-3 text-right font-bold">مسند إلى</th>
                    <th class="px-4 py-3 text-right font-bold">Meta IDs</th>
                    <th class="px-4 py-3 text-right font-bold">التاريخ</th>
                </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                @forelse($leads as $lead)
                    <tr class="hover:bg-slate-50/80">
                        <td class="px-4 py-3">
                            <p class="font-bold text-slate-900">{{ $lead->name }}</p>
                            <p class="text-[11px] text-slate-400 font-mono" dir="ltr">#{{ $lead->id }}</p>
                        </td>
                        <td class="px-4 py-3">
                            <p class="font-semibold text-slate-800" dir="ltr">{{ $lead->phone }}</p>
                            <p class="text-xs text-slate-500">{{ $lead->email ?: '—' }}</p>
                        </td>
                        <td class="px-4 py-3">
                            @if($lead->meta_leadgen_id)
                                <span class="inline-flex px-2 py-1 rounded-full text-[11px] font-bold bg-teal-100 text-teal-800">Lead Form</span>
                            @else
                                <span class="inline-flex px-2 py-1 rounded-full text-[11px] font-bold bg-sky-100 text-sky-800">محادثة</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-slate-700">{{ \App\Domains\Sales\Models\SalesLead::stageLabel($lead->stage) }}</td>
                        <td class="px-4 py-3 text-slate-700">{{ $lead->assignee->name ?? '—' }}</td>
                        <td class="px-4 py-3 text-[11px] font-mono text-slate-500" dir="ltr">
                            <div>lead: {{ $lead->meta_leadgen_id ?: '—' }}</div>
                            <div>ad: {{ $lead->meta_ad_id ?: '—' }}</div>
                            <div>batch: {{ $lead->import_batch ?: '—' }}</div>
                        </td>
                        <td class="px-4 py-3 text-xs text-slate-600 whitespace-nowrap">
                            {{ optional($lead->created_at)->timezone(config('app.timezone'))->format('Y-m-d H:i') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-10 text-center text-slate-500">
                            لا توجد ليدز مسحوبة لهذه الحملة بعد. اضغط «سحب جديد» لبدء العملية في الخلفية.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
        @if($leads->hasPages())
            <div class="px-4 py-3 border-t border-slate-100">
                {{ $leads->links() }}
            </div>
        @endif
    </div>

    @if($runs->isNotEmpty())
        <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100">
                <h2 class="text-sm font-black text-slate-800">سجل عمليات السحب</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-slate-50 text-xs text-slate-600">
                    <tr>
                        <th class="px-4 py-3 text-right">الحالة</th>
                        <th class="px-4 py-3 text-right">النتيجة</th>
                        <th class="px-4 py-3 text-right">بواسطة</th>
                        <th class="px-4 py-3 text-right">الوقت</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                    @foreach($runs as $run)
                        <tr>
                            <td class="px-4 py-3">
                                <span class="inline-flex px-2.5 py-1 rounded-full text-[11px] font-bold {{ $run->statusBadgeClass() }}">
                                    {{ $run->statusLabel() }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-xs text-slate-700 leading-relaxed">
                                {{ $run->message ?: ($run->error ?: '—') }}
                                <div class="mt-1 text-slate-500">
                                    جديد {{ $run->created_count }} · محدّث {{ $run->updated_count }} · محادثات {{ $run->conversations_linked }}
                                </div>
                            </td>
                            <td class="px-4 py-3 text-slate-700">{{ $run->requester->name ?? '—' }}</td>
                            <td class="px-4 py-3 text-xs text-slate-600 whitespace-nowrap">
                                {{ optional($run->created_at)->timezone(config('app.timezone'))->format('Y-m-d H:i') }}
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
@endsection
