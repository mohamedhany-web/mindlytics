@extends('community::portal.layouts.app')

@section('title', $dataset->title)

@section('content')
<div class="w-full max-w-6xl mx-auto">
    {{-- رأس الصفحة --}}
    <div class="mb-6">
        <a href="{{ route('community.datasets.index') }}" class="inline-flex items-center gap-2 text-slate-600 hover:text-slate-900 text-sm font-semibold mb-4">
            <i class="fas fa-arrow-right"></i>
            <span>العودة لمجموعات البيانات</span>
        </a>
        <h1 class="text-2xl sm:text-3xl font-black text-slate-900 mb-2">{{ $dataset->title }}</h1>
        <div class="flex flex-wrap items-center gap-2 mt-2">
            @if($dataset->category)
                <a href="{{ route('community.datasets.index', ['category' => $dataset->category]) }}" class="inline-flex px-3 py-1 rounded-lg text-sm font-bold bg-slate-100 text-slate-600 hover:bg-slate-200">{{ $dataset->category_label }}</a>
            @endif
            @if($dataset->file_size)
                <span class="text-slate-500 text-sm">الحجم: {{ $dataset->file_size }}</span>
            @endif
        </div>
    </div>

    {{-- الوصف --}}
    @if($dataset->description)
        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm mb-6">
            <h2 class="text-lg font-black text-slate-900 mb-3 flex items-center gap-2">
                <i class="fas fa-align-right text-blue-600"></i>
                وصف مجموعة البيانات
            </h2>
            <div class="text-slate-600 leading-relaxed whitespace-pre-line">{{ $dataset->description }}</div>
        </div>
    @endif

    {{-- رابط التحميل إن وُجد --}}
    @if($dataset->file_url)
        <div class="mb-6">
            <a href="{{ $dataset->file_url }}" target="_blank" rel="noopener" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-blue-600 text-white font-bold hover:bg-blue-700 transition-colors shadow-md">
                <i class="fas fa-external-link-alt"></i>
                <span>فتح رابط التحميل</span>
            </a>
        </div>
    @endif

    @if($dataset->file_path)
        <div class="mb-2">
            <a href="{{ route('community.datasets.download', $dataset) }}" class="inline-flex items-center gap-2 text-blue-600 hover:text-blue-700 font-bold text-sm">
                <i class="fas fa-download"></i>
                <span>تحميل الملف</span>
            </a>
        </div>
    @endif

    {{-- معاينة البيانات (جدول مع اسكرول) --}}
    @if(!empty($previewHeaders) || !empty($previewRows))
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-4 py-3 border-b border-slate-200 bg-slate-50 flex items-center justify-between flex-wrap gap-2">
                <h2 class="text-lg font-black text-slate-900 flex items-center gap-2">
                    <i class="fas fa-table text-blue-600"></i>
                    معاينة البيانات
                </h2>
                <span class="text-slate-500 text-sm">أول {{ count($previewRows) }} صف</span>
            </div>
            <div class="overflow-auto max-h-[70vh] border-b border-slate-100">
                <table class="w-full min-w-full border-collapse text-right">
                    <thead class="sticky top-0 z-10 bg-slate-100 border-b-2 border-slate-200">
                        <tr>
                            @foreach($previewHeaders as $cell)
                                <th class="px-4 py-3 text-sm font-bold text-slate-800 whitespace-nowrap border-l border-slate-200">{{ e($cell) }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($previewRows as $row)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                @foreach($previewHeaders as $i => $header)
                                    <td class="px-4 py-2.5 text-sm text-slate-700 whitespace-nowrap border-l border-slate-100">{{ e($row[$i] ?? '') }}</td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @else
        @if($dataset->file_url)
            <p class="text-slate-500 text-sm mt-4">لا يمكن معاينة المحتوى لهذا الرابط. استخدم زر «فتح رابط التحميل» أعلاه.</p>
        @elseif($dataset->file_path)
            <p class="text-slate-500 text-sm mt-4">تعذر قراءة معاينة الملف أو الملف غير مدعوم (يدعم: Excel و CSV). يمكنك تحميل الملف أعلاه.</p>
        @else
            <p class="text-slate-500 text-sm mt-4">لا يوجد ملف مرفق لهذه المجموعة حالياً.</p>
        @endif
    @endif
</div>
@endsection
