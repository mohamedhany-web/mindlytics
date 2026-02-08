@extends('layouts.app')

@section('title', 'كورساتي الأوفلاين - Mindlytics')
@section('header', 'كورساتي الأوفلاين')

@section('content')
<div class="space-y-6">
    <!-- الهيدر -->
    <div class="rounded-2xl p-5 sm:p-6 bg-white border border-slate-200 shadow-sm">
        <h1 class="text-2xl sm:text-3xl font-bold text-slate-800 mb-1">كورساتي الأوفلاين</h1>
        <p class="text-sm text-slate-500">الكورسات الحضورية المعينة لك</p>
    </div>

    <!-- الإحصائيات -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
        <div class="rounded-2xl p-5 bg-white border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1">إجمالي الكورسات</p>
                <p class="text-2xl sm:text-3xl font-bold text-slate-800">{{ $stats['total'] ?? 0 }}</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-amber-50 flex items-center justify-center">
                <i class="fas fa-map-marker-alt text-amber-600 text-lg"></i>
            </div>
        </div>
        <div class="rounded-2xl p-5 bg-white border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1">نشطة</p>
                <p class="text-2xl sm:text-3xl font-bold text-slate-800">{{ $stats['active'] ?? 0 }}</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-emerald-50 flex items-center justify-center">
                <i class="fas fa-check-circle text-emerald-600 text-lg"></i>
            </div>
        </div>
        <div class="rounded-2xl p-5 bg-white border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1">مسودة</p>
                <p class="text-2xl sm:text-3xl font-bold text-slate-800">{{ $stats['draft'] ?? 0 }}</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-slate-100 flex items-center justify-center">
                <i class="fas fa-pen text-slate-600 text-lg"></i>
            </div>
        </div>
        <div class="rounded-2xl p-5 bg-white border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1">منتهية</p>
                <p class="text-2xl sm:text-3xl font-bold text-slate-800">{{ $stats['completed'] ?? 0 }}</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-violet-50 flex items-center justify-center">
                <i class="fas fa-flag-checkered text-violet-600 text-lg"></i>
            </div>
        </div>
        <div class="rounded-2xl p-5 bg-white border border-slate-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1">إجمالي الطلاب</p>
                <p class="text-2xl sm:text-3xl font-bold text-slate-800">{{ $stats['total_students'] ?? 0 }}</p>
            </div>
            <div class="w-12 h-12 rounded-xl bg-sky-50 flex items-center justify-center">
                <i class="fas fa-user-graduate text-sky-600 text-lg"></i>
            </div>
        </div>
    </div>

    <!-- الفلاتر -->
    <div class="bg-white rounded-2xl p-5 sm:p-6 border border-slate-200 shadow-sm">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label for="search" class="block text-sm font-semibold text-slate-700 mb-2">البحث</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}"
                       placeholder="البحث في عناوين الكورسات..."
                       class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-slate-800 focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 transition-colors">
            </div>
            <div>
                <label for="status" class="block text-sm font-semibold text-slate-700 mb-2">الحالة</label>
                <select name="status" id="status" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-slate-800 focus:border-amber-500 focus:ring-2 focus:ring-amber-500/20 transition-colors">
                    <option value="">جميع الحالات</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>نشط</option>
                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>مسودة</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>منتهي</option>
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 inline-flex items-center justify-center gap-2 bg-amber-500 hover:bg-amber-600 text-white px-5 py-2.5 rounded-xl font-semibold transition-colors">
                    <i class="fas fa-search"></i>
                    <span>بحث</span>
                </button>
                @if(request()->anyFilled(['search', 'status']))
                    <a href="{{ route('instructor.offline-courses.index') }}" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-semibold transition-colors inline-flex items-center justify-center">
                        <i class="fas fa-times"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- قائمة الكورسات -->
    @if($courses->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($courses as $course)
            <div class="rounded-2xl overflow-hidden bg-white border border-slate-200 shadow-sm hover:shadow-md transition-shadow">
                <div class="px-5 py-4 border-b border-slate-200">
                    <div class="flex items-center justify-between gap-2">
                        <h3 class="text-lg font-bold text-slate-800 truncate flex-1">{{ $course->title }}</h3>
                        @php
                            $statusClass = match($course->status ?? '') {
                                'active' => 'bg-emerald-100 text-emerald-700',
                                'completed' => 'bg-violet-100 text-violet-700',
                                default => 'bg-slate-100 text-slate-700',
                            };
                            $statusLabel = match($course->status ?? '') {
                                'active' => 'نشط',
                                'completed' => 'منتهي',
                                default => 'مسودة',
                            };
                        @endphp
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold shrink-0 {{ $statusClass }}">
                            <i class="fas {{ $course->status === 'active' ? 'fa-check-circle' : ($course->status === 'completed' ? 'fa-flag-checkered' : 'fa-pen') }}"></i>
                            {{ $statusLabel }}
                        </span>
                    </div>
                </div>

                <div class="px-5 py-4">
                    @if($course->description)
                        <p class="text-sm text-slate-600 mb-4 line-clamp-2">{{ Str::limit($course->description, 100) }}</p>
                    @endif

                    <div class="space-y-2 mb-4">
                        @if($course->locationModel)
                            <div class="flex items-center gap-2 text-sm">
                                <div class="w-8 h-8 rounded-lg bg-amber-50 flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-map-marker-alt text-amber-600 text-xs"></i>
                                </div>
                                <span class="text-slate-500">المكان:</span>
                                <span class="text-slate-800 font-medium">{{ $course->locationModel->name ?? $course->location }}</span>
                            </div>
                        @elseif($course->location)
                            <div class="flex items-center gap-2 text-sm">
                                <div class="w-8 h-8 rounded-lg bg-amber-50 flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-map-marker-alt text-amber-600 text-xs"></i>
                                </div>
                                <span class="text-slate-800 font-medium">{{ Str::limit($course->location, 40) }}</span>
                            </div>
                        @endif
                        @if($course->start_date)
                            <div class="flex items-center gap-2 text-sm">
                                <div class="w-8 h-8 rounded-lg bg-sky-50 flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-calendar-alt text-sky-600 text-xs"></i>
                                </div>
                                <span class="text-slate-500">من</span>
                                <span class="text-slate-800 font-medium">{{ $course->start_date->format('Y-m-d') }}</span>
                                @if($course->end_date)
                                    <span class="text-slate-500">إلى</span>
                                    <span class="text-slate-800 font-medium">{{ $course->end_date->format('Y-m-d') }}</span>
                                @endif
                            </div>
                        @endif
                        @if($course->price && $course->price > 0)
                            <div class="flex items-center gap-2 text-sm">
                                <div class="w-8 h-8 rounded-lg bg-emerald-50 flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-money-bill-wave text-emerald-600 text-xs"></i>
                                </div>
                                <span class="text-slate-800 font-semibold">{{ number_format($course->price, 2) }} ج.م</span>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="px-5 py-3 bg-slate-50/80 border-t border-slate-200">
                    <div class="grid grid-cols-2 gap-4 text-center">
                        <div>
                            <div class="text-lg font-bold text-slate-800">{{ $course->groups_count ?? 0 }}</div>
                            <div class="text-xs text-slate-500 font-medium">مجموعة</div>
                        </div>
                        <div>
                            <div class="text-lg font-bold text-slate-800">{{ $course->enrollments_count ?? 0 }}</div>
                            <div class="text-xs text-slate-500 font-medium">طالب</div>
                        </div>
                    </div>
                </div>

                <div class="px-5 py-4 border-t border-slate-200">
                    <a href="{{ route('instructor.offline-courses.show', $course) }}"
                       class="w-full inline-flex items-center justify-center gap-2 bg-amber-500 hover:bg-amber-600 text-white px-4 py-2.5 rounded-xl font-semibold transition-colors">
                        <i class="fas fa-eye"></i>
                        <span>عرض التفاصيل</span>
                    </a>
                </div>
            </div>
            @endforeach
        </div>

        <div class="mt-6 flex justify-center">
            <div class="bg-white rounded-xl p-3 border border-slate-200 shadow-sm">
                {{ $courses->links() }}
            </div>
        </div>
    @else
        <div class="rounded-2xl p-12 sm:p-16 text-center bg-white border border-slate-200 shadow-sm">
            <div class="w-24 h-24 rounded-2xl bg-amber-50 flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-map-marker-alt text-4xl text-amber-500"></i>
            </div>
            <h3 class="text-xl sm:text-2xl font-bold text-slate-800 mb-2">لا توجد كورسات أوفلاين</h3>
            <p class="text-slate-500 max-w-md mx-auto">لم يتم تعيين أي كورس حضوري لك بعد</p>
        </div>
    @endif
</div>
@endsection
