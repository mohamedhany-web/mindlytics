@php
    $isVideo = $task->isVideoEditing();
    $rows = $task->deliverables->sortBy('created_at')->values();
@endphp
<div class="rounded-2xl border-2 border-slate-200 bg-white shadow-sm overflow-hidden mb-6" id="deliverables-quick-table">
    <div class="px-4 py-3 bg-gradient-to-l from-slate-50 to-white border-b border-slate-100 flex flex-wrap items-center justify-between gap-2">
        <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
            @if($isVideo)
                <span class="w-9 h-9 rounded-lg bg-violet-100 text-violet-600 flex items-center justify-center"><i class="fas fa-table"></i></span>
            @else
                <span class="w-9 h-9 rounded-lg bg-sky-100 text-sky-600 flex items-center justify-center"><i class="fas fa-table"></i></span>
            @endif
            جدول التسليمات
        </h2>
        <a href="#deliverables-section" class="inline-flex items-center gap-1.5 text-sm font-semibold text-blue-600 hover:text-blue-800">
            <i class="fas fa-arrow-down"></i>
            إضافة أو تعديل تسليم
        </a>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-50 text-slate-600">
                <tr>
                    <th class="text-right px-3 py-2 font-semibold whitespace-nowrap">#</th>
                    <th class="text-right px-3 py-2 font-semibold">العنوان</th>
                    <th class="text-right px-3 py-2 font-semibold whitespace-nowrap">الحالة</th>
                    @if($isVideo)
                        <th class="text-right px-3 py-2 font-semibold whitespace-nowrap">ممن استلمته</th>
                        <th class="text-right px-3 py-2 font-semibold whitespace-nowrap">قبل</th>
                        <th class="text-right px-3 py-2 font-semibold whitespace-nowrap">بعد</th>
                        <th class="text-right px-3 py-2 font-semibold">رابط الفيديو</th>
                    @else
                        <th class="text-right px-3 py-2 font-semibold whitespace-nowrap">النوع</th>
                        <th class="text-right px-3 py-2 font-semibold">المحتوى / المعاينة</th>
                    @endif
                    <th class="text-right px-3 py-2 font-semibold whitespace-nowrap">التاريخ</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($rows as $index => $d)
                    <tr class="hover:bg-slate-50/80 align-top">
                        <td class="px-3 py-2 font-mono text-xs text-gray-500">{{ $index + 1 }}</td>
                        <td class="px-3 py-2 font-medium text-gray-900">{{ $d->title ?: ('تسليم ' . ($index + 1)) }}</td>
                        <td class="px-3 py-2 whitespace-nowrap">
                            <span class="inline-flex px-2 py-0.5 rounded-md text-xs font-semibold
                                @if($d->status === 'approved') bg-green-100 text-green-800
                                @elseif($d->status === 'rejected') bg-red-100 text-red-800
                                @elseif($d->status === 'submitted') bg-blue-100 text-blue-800
                                @else bg-gray-100 text-gray-700
                                @endif">
                                @if($d->status === 'approved') معتمد
                                @elseif($d->status === 'rejected') مرفوض
                                @elseif($d->status === 'submitted') مقدم
                                @else معلق
                                @endif
                            </span>
                        </td>
                        @if($isVideo)
                            <td class="px-3 py-2 text-gray-800">{{ $d->received_from ?: '—' }}</td>
                            <td class="px-3 py-2 text-gray-800 whitespace-nowrap">{{ $d->duration_before ?: '—' }}</td>
                            <td class="px-3 py-2 text-gray-800 whitespace-nowrap">{{ $d->duration_after ?: '—' }}</td>
                            <td class="px-3 py-2">
                                @if($d->link_url)
                                    <a href="{{ $d->link_url }}" target="_blank" rel="noopener" class="text-violet-600 hover:text-violet-800 font-medium break-all max-w-[14rem] inline-block">{{ \Illuminate\Support\Str::limit($d->link_url, 42) }}</a>
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>
                        @else
                            <td class="px-3 py-2 whitespace-nowrap">
                                @if($d->delivery_type === 'link') رابط
                                @elseif($d->delivery_type === 'image') صورة
                                @else ملف
                                @endif
                            </td>
                            <td class="px-3 py-2">
                                @if($d->delivery_type === 'link' && $d->link_url)
                                    <a href="{{ $d->link_url }}" target="_blank" rel="noopener" class="text-blue-600 hover:text-blue-800 break-all max-w-[14rem] inline-block">{{ \Illuminate\Support\Str::limit($d->link_url, 42) }}</a>
                                @elseif($d->delivery_type === 'image' && $d->publicFileUrl())
                                    <a href="{{ $d->publicFileUrl() }}" target="_blank" rel="noopener" class="inline-block">
                                        <img src="{{ $d->publicFileUrl() }}" alt="" class="max-h-14 rounded border border-gray-200 object-cover">
                                    </a>
                                @elseif($d->publicFileUrl())
                                    <a href="{{ $d->publicFileUrl() }}" target="_blank" rel="noopener" class="text-sky-600 hover:text-sky-800 font-medium"><i class="fas fa-file-download ml-1"></i>{{ \Illuminate\Support\Str::limit($d->file_name ?? 'ملف', 24) }}</a>
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>
                        @endif
                        <td class="px-3 py-2 text-gray-500 whitespace-nowrap text-xs">{{ $d->created_at->format('Y-m-d H:i') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ $isVideo ? 8 : 6 }}" class="px-4 py-8 text-center text-gray-500">
                            لا توجد تسليمات بعد. استخدم قسم «التسليمات» أدناه لإضافة أول تسليم.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
