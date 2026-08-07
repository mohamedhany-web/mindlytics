@extends('layouts.admin')

@section('title', 'تفاصيل التسجيل')
@section('header', 'تفاصيل التسجيل')

@section('content')
<div class="space-y-6">
    <!-- الهيدر والعودة -->
    <div class="flex items-center justify-between">
        <div>
            <nav class="text-sm text-gray-500 mb-2">
                <a href="{{ route('admin.dashboard') }}" class="hover:text-primary-600">لوحة التحكم</a>
                <span class="mx-2">/</span>
                <a href="{{ route('admin.online-enrollments.index') }}" class="hover:text-primary-600">التسجيلات</a>
                <span class="mx-2">/</span>
                <span>تفاصيل التسجيل</span>
            </nav>
        </div>
        <a href="{{ route('admin.online-enrollments.index') }}" 
           class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg font-medium transition-colors">
            <i class="fas fa-arrow-right ml-2"></i>
            العودة
        </a>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <!-- معلومات التسجيل -->
        <div class="xl:col-span-2">
            <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">معلومات التسجيل</h3>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                        @if($enrollment->status_color == 'yellow') bg-yellow-100 text-yellow-800
                        @elseif($enrollment->status_color == 'green') bg-green-100 text-green-800
                        @elseif($enrollment->status_color == 'blue') bg-blue-100 text-blue-800
                        @elseif($enrollment->status_color == 'red') bg-red-100 text-red-800
                        @else bg-gray-100 text-gray-800
                        @endif">
                        {{ $enrollment->status_text }}
                    </span>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-500 mb-1">الطالب</label>
                                <div class="font-semibold text-gray-900">{{ $enrollment->student->name }}</div>
                                <div class="text-sm text-gray-500">{{ $enrollment->student->email }}</div>
                            </div>
                        </div>
                        <div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-500 mb-1">رقم الهاتف</label>
                                <div class="text-gray-900">{{ $enrollment->student->phone ?? 'غير محدد' }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-500 mb-1">الكورس</label>
                                <div class="font-semibold text-gray-900">{{ $enrollment->course->title }}</div>
                                <div class="text-sm text-gray-500">
                                    {{ $enrollment->course->academicYear->name ?? 'غير محدد' }} - 
                                    {{ $enrollment->course->academicSubject->name ?? 'غير محدد' }}
                                </div>
                            </div>
                        </div>
                        <div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-500 mb-1">التقدم في المنهج</label>
                                @php
                                    $storedPct = (float) ($enrollment->progress ?? 0);
                                    $livePct = (float) ($progressBreakdown['progress'] ?? $storedPct);
                                    $finished = $enrollment->hasFinishedCurriculum();
                                @endphp
                                <div class="flex items-center justify-between text-sm mb-2">
                                    <span class="font-bold {{ $finished ? 'text-emerald-700' : 'text-gray-800' }}">{{ number_format($livePct, 1) }}%</span>
                                    @if($progressBreakdown)
                                        <span class="text-gray-500">{{ $progressBreakdown['completed'] }} / {{ $progressBreakdown['total'] }} عنصر مكتمل</span>
                                    @endif
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-3">
                                    <div class="{{ $finished ? 'bg-emerald-500' : 'bg-primary-600' }} h-3 rounded-full transition-all duration-300"
                                         style="width: {{ min($livePct, 100) }}%"></div>
                                </div>
                                @if($finished)
                                    <p class="text-xs text-emerald-700 font-semibold mt-2">
                                        أنهى المنهج
                                        @if($enrollment->curriculum_completed_at)
                                            · {{ $enrollment->curriculum_completed_at->format('Y-m-d H:i') }}
                                        @endif
                                    </p>
                                @elseif($progressBreakdown && $progressBreakdown['next_item'])
                                    <p class="text-xs text-amber-700 mt-2">
                                        واقف عند:
                                        <span class="font-bold">{{ $progressBreakdown['next_item']['type_label'] }}</span>
                                        — {{ $progressBreakdown['next_item']['title'] }}
                                        @if($progressBreakdown['next_item']['section'])
                                            <span class="text-gray-500">({{ $progressBreakdown['next_item']['section'] }})</span>
                                        @endif
                                    </p>
                                @endif
                                @if($progressBreakdown && $progressBreakdown['avg_lecture_watch_percent'] !== null)
                                    <p class="text-xs text-sky-700 mt-1.5">
                                        متوسط مشاهدة المحاضرات:
                                        <span class="font-bold">{{ number_format($progressBreakdown['avg_lecture_watch_percent'], 0) }}%</span>
                                        · مكتمل منها {{ $progressBreakdown['lectures_completed'] }}/{{ $progressBreakdown['lectures_total'] }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-500 mb-1">تاريخ التسجيل</label>
                                <div class="text-gray-900">{{ $enrollment->enrolled_at ? $enrollment->enrolled_at->format('Y-m-d H:i') : 'غير محدد' }}</div>
                            </div>
                        </div>
                        <div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-500 mb-1">تاريخ التفعيل</label>
                                <div class="text-gray-900">{{ $enrollment->activated_at ? $enrollment->activated_at->format('Y-m-d H:i') : 'غير مفعل' }}</div>
                            </div>
                        </div>
                    </div>

                    @if($enrollment->activatedBy)
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-500 mb-1">تم التفعيل بواسطة</label>
                            <div class="text-gray-900">{{ $enrollment->activatedBy->name }}</div>
                        </div>
                    @endif

                    @if($enrollment->notes)
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-500 mb-1">ملاحظات</label>
                            <div class="bg-gray-50 p-3 rounded-lg text-gray-900">{{ $enrollment->notes }}</div>
                        </div>
                    @endif
                </div>
            </div>

            @if($progressBreakdown)
                <div class="bg-white shadow-sm rounded-lg border border-gray-200 mt-6">
                    <div class="px-6 py-4 border-b border-gray-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">موقف الطالب في المنهج</h3>
                            <p class="text-xs text-gray-500 mt-0.5">تفصيل حي من عناصر المنهج — مكتمل / متبقي</p>
                        </div>
                        <div class="flex flex-wrap gap-2 text-xs font-semibold">
                            <span class="inline-flex px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-800">مكتمل: {{ $progressBreakdown['completed'] }}</span>
                            <span class="inline-flex px-2.5 py-1 rounded-full bg-amber-100 text-amber-800">متبقي: {{ $progressBreakdown['remaining'] }}</span>
                            <span class="inline-flex px-2.5 py-1 rounded-full bg-slate-100 text-slate-700">الإجمالي: {{ $progressBreakdown['total'] }}</span>
                        </div>
                    </div>
                    <div class="p-4 sm:p-6 space-y-5">
                        @forelse($progressBreakdown['sections'] as $section)
                            <div class="rounded-xl border border-slate-200 overflow-hidden">
                                <div class="px-4 py-3 bg-slate-50 border-b border-slate-200 flex items-center justify-between gap-3">
                                    <h4 class="font-bold text-slate-900">{{ $section['title'] }}</h4>
                                    <span class="text-xs font-semibold text-slate-600">{{ $section['completed'] }} / {{ $section['total'] }}</span>
                                </div>
                                <ul class="divide-y divide-slate-100">
                                    @foreach($section['items'] as $item)
                                        <li class="px-4 py-3 flex items-start gap-3 {{ $item['completed'] ? 'bg-emerald-50/40' : ($item['missing'] ? 'bg-rose-50/50' : '') }}">
                                            <span class="mt-0.5 w-6 h-6 rounded-full flex items-center justify-center shrink-0
                                                {{ $item['completed'] ? 'bg-emerald-500 text-white' : ($item['missing'] ? 'bg-rose-500 text-white' : 'bg-slate-200 text-slate-600') }}">
                                                <i class="fas {{ $item['completed'] ? 'fa-check' : ($item['missing'] ? 'fa-exclamation' : 'fa-minus') }} text-[10px]"></i>
                                            </span>
                                            <div class="min-w-0 flex-1">
                                                <div class="flex flex-wrap items-center gap-2">
                                                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-slate-100 text-slate-700">{{ $item['type_label'] }}</span>
                                                    <p class="font-semibold text-slate-900 truncate">{{ $item['title'] }}</p>
                                                </div>
                                                @if($item['detail'])
                                                    <p class="text-xs text-slate-500 mt-0.5">{{ $item['detail'] }}</p>
                                                @endif
                                            </div>
                                            <span class="text-[11px] font-bold shrink-0 {{ $item['completed'] ? 'text-emerald-700' : 'text-amber-700' }}">
                                                {{ $item['completed'] ? 'مكتمل' : 'لم يكتمل' }}
                                            </span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @empty
                            <p class="text-sm text-slate-500 text-center py-8">لا توجد عناصر منهج نشطة لهذا الكورس.</p>
                        @endforelse
                    </div>
                </div>
            @endif
        </div>

        <!-- إجراءات سريعة -->
        <div class="space-y-6">
            <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h4 class="text-lg font-semibold text-gray-900">إجراءات سريعة</h4>
                </div>
                <div class="p-6 space-y-3">
                    @if($enrollment->status === 'pending')
                        <form action="{{ route('admin.online-enrollments.activate', $enrollment) }}" method="POST" class="mb-2">
                            @csrf
                            <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition-colors" 
                                    onclick="return confirm('هل تريد تفعيل هذا التسجيل؟')">
                                <i class="fas fa-check ml-1"></i>
                                تفعيل التسجيل
                            </button>
                        </form>
                    @elseif($enrollment->status === 'active')
                        <form action="{{ route('admin.online-enrollments.deactivate', $enrollment) }}" method="POST" class="mb-2">
                            @csrf
                            <button type="submit" class="w-full bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg font-medium transition-colors" 
                                    onclick="return confirm('هل تريد إلغاء تفعيل هذا التسجيل؟')">
                                <i class="fas fa-pause ml-1"></i>
                                إلغاء التفعيل
                            </button>
                        </form>
                    @elseif($enrollment->status === 'suspended')
                        <form action="{{ route('admin.online-enrollments.activate', $enrollment) }}" method="POST" class="mb-2">
                            @csrf
                            <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg font-medium transition-colors" 
                                    onclick="return confirm('هل تريد إعادة تفعيل هذا التسجيل وفتح الكورس للطالب مرة أخرى؟')">
                                <i class="fas fa-redo ml-1"></i>
                                إعادة التفعيل وفتح الكورس
                            </button>
                        </form>
                    @endif

                    <a href="{{ route('admin.online-enrollments.index') }}" class="w-full bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg font-medium transition-colors block text-center">
                        <i class="fas fa-list ml-1"></i>
                        عرض جميع التسجيلات
                    </a>

                </div>
            </div>

            <!-- معلومات إضافية -->
            <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h4 class="text-lg font-semibold text-gray-900">معلومات إضافية</h4>
                </div>
                <div class="p-6 space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-500">ID التسجيل</span>
                        <span class="text-sm text-gray-900">{{ $enrollment->id }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-500">تم الإنشاء</span>
                        <span class="text-sm text-gray-900">{{ $enrollment->created_at->format('Y-m-d H:i') }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-500">آخر تحديث</span>
                        <span class="text-sm text-gray-900">{{ $enrollment->updated_at->format('Y-m-d H:i') }}</span>
                    </div>
                </div>
            </div>

            <!-- إحصائيات الكورس -->
            <div class="bg-white shadow-sm rounded-lg border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h4 class="text-lg font-semibold text-gray-900">إحصائيات الكورس</h4>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-2 gap-4 text-center">
                        <div class="p-4 bg-primary-50 rounded-lg">
                            <div class="text-2xl font-bold text-primary-600">{{ $enrollment->course->lessons->count() }}</div>
                            <div class="text-sm text-gray-500">دروس</div>
                        </div>
                        <div class="p-4 bg-green-50 rounded-lg">
                            <div class="text-2xl font-bold text-green-600">{{ $enrollment->course->duration_hours }}</div>
                            <div class="text-sm text-gray-500">ساعة</div>
                        </div>
                    </div>
                    <div class="mt-4 pt-4 border-t border-gray-200 text-center">
                        <div class="p-4 bg-blue-50 rounded-lg">
                            <div class="text-2xl font-bold text-blue-600">{{ $enrollment->course->enrollments->where('status', 'active')->count() }}</div>
                            <div class="text-sm text-gray-500">طالب مسجل</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
