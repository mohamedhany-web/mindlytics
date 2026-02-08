@extends('layouts.app')

@section('title', 'الاختبارات')
@section('header', 'الاختبارات')

@section('content')
<div class="space-y-6">
    <div class="rounded-2xl p-5 sm:p-6 bg-white border border-slate-200 shadow-sm">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">الاختبارات</h1>
                <p class="text-sm text-slate-500 mt-0.5">إدارة الاختبارات والمحاولات</p>
            </div>
            <a href="{{ route('instructor.exams.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-sky-500 hover:bg-sky-600 text-white rounded-xl font-semibold transition-colors">
                <i class="fas fa-plus"></i>
                <span>إنشاء اختبار</span>
            </a>
        </div>
    </div>

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="rounded-xl p-4 bg-white border border-slate-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase">الإجمالي</p>
                    <p class="text-2xl font-bold text-slate-800">{{ $stats['total'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-sky-100 flex items-center justify-center text-sky-600"><i class="fas fa-clipboard-check"></i></div>
            </div>
        </div>
        <div class="rounded-xl p-4 bg-white border border-slate-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase">نشطة</p>
                    <p class="text-2xl font-bold text-slate-800">{{ $stats['active'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-emerald-100 flex items-center justify-center text-emerald-600"><i class="fas fa-check-circle"></i></div>
            </div>
        </div>
        <div class="rounded-xl p-4 bg-white border border-slate-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase">المحاولات</p>
                    <p class="text-2xl font-bold text-slate-800">{{ $stats['total_attempts'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-violet-100 flex items-center justify-center text-violet-600"><i class="fas fa-redo"></i></div>
            </div>
        </div>
        <div class="rounded-xl p-4 bg-white border border-slate-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase">مكتملة</p>
                    <p class="text-2xl font-bold text-slate-800">{{ $stats['completed_attempts'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-blue-100 flex items-center justify-center text-blue-600"><i class="fas fa-check-double"></i></div>
            </div>
        </div>
    </div>

    <div class="rounded-xl bg-white border border-slate-200 shadow-sm p-5">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label for="course_id" class="block text-sm font-semibold text-slate-700 mb-1">الكورس الأونلاين</label>
                <select name="course_id" id="course_id" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 text-slate-800">
                    <option value="">جميع الكورسات (أونلاين)</option>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}" {{ request('course_id') == $course->id ? 'selected' : '' }}>{{ $course->title }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="offline_course_id" class="block text-sm font-semibold text-slate-700 mb-1">الكورس الأوفلاين</label>
                <select name="offline_course_id" id="offline_course_id" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 text-slate-800">
                    <option value="">جميع الكورسات الأوفلاين</option>
                    @foreach($offlineCourses ?? [] as $oc)
                        <option value="{{ $oc->id }}" {{ request('offline_course_id') == $oc->id ? 'selected' : '' }}>{{ $oc->title }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="is_active" class="block text-sm font-semibold text-slate-700 mb-1">الحالة</label>
                <select name="is_active" id="is_active" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 text-slate-800">
                    <option value="">الكل</option>
                    <option value="1" {{ request('is_active') == '1' ? 'selected' : '' }}>نشطة</option>
                    <option value="0" {{ request('is_active') == '0' ? 'selected' : '' }}>معطلة</option>
                </select>
            </div>
            <div>
                <label for="search" class="block text-sm font-semibold text-slate-700 mb-1">البحث</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="بحث..." class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 text-slate-800">
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="px-4 py-2.5 bg-sky-500 hover:bg-sky-600 text-white rounded-xl font-semibold transition-colors">
                    <i class="fas fa-search ml-1"></i> بحث
                </button>
                @if(request()->anyFilled(['course_id', 'offline_course_id', 'is_active', 'search']))
                    <a href="{{ route('instructor.exams.index') }}" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-semibold transition-colors">
                        <i class="fas fa-times"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    @if($exams->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
            @foreach($exams as $exam)
                <div class="rounded-xl bg-white border border-slate-200 shadow-sm hover:border-sky-300 hover:shadow-md transition-all overflow-hidden flex flex-col">
                    <div class="p-5 flex-1 flex flex-col">
                        <div class="flex items-start justify-between gap-2 mb-2">
                            <h3 class="text-lg font-bold text-slate-800 line-clamp-2 flex-1 min-w-0">{{ $exam->title }}</h3>
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold shrink-0 {{ $exam->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                                {{ $exam->is_active ? 'نشط' : 'معطل' }}
                            </span>
                        </div>
                        @if($exam->description)
                            <p class="text-sm text-slate-600 mb-3 line-clamp-2">{{ $exam->description }}</p>
                        @endif
                        <div class="flex flex-wrap items-center gap-x-3 gap-y-1 text-sm text-slate-500 mt-auto">
                            <span class="truncate max-w-full" title="{{ $exam->offlineCourse->title ?? $exam->advancedCourse->title ?? '—' }}"><i class="fas fa-book text-sky-500 ml-1"></i> {{ $exam->offlineCourse->title ?? $exam->advancedCourse->title ?? '—' }} @if($exam->offline_course_id)<span class="text-amber-600">(أوفلاين)</span>@endif</span>
                            <span>{{ $exam->duration_minutes }} د</span>
                            <span>{{ $exam->total_marks }} درجة</span>
                            <span>{{ $exam->questions_count }} سؤال</span>
                            <span>{{ $exam->attempts_count }} محاولة</span>
                        </div>
                    </div>
                    <div class="px-5 py-4 border-t border-slate-100 bg-slate-50/50 flex flex-wrap items-center gap-2">
                        <a href="{{ route('instructor.exams.questions.manage', $exam) }}" class="inline-flex items-center gap-1.5 px-3 py-2 bg-violet-500 hover:bg-violet-600 text-white rounded-lg font-semibold text-sm transition-colors">
                            <i class="fas fa-list"></i> الأسئلة
                        </a>
                        <a href="{{ route('instructor.exams.show', $exam) }}" class="inline-flex items-center gap-1.5 px-3 py-2 bg-sky-500 hover:bg-sky-600 text-white rounded-lg font-semibold text-sm transition-colors">
                            <i class="fas fa-eye"></i> عرض
                        </a>
                        <form action="{{ route('instructor.exams.destroy', $exam) }}" method="POST" class="inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا الاختبار؟ سيتم حذف جميع الأسئلة والمحاولات المرتبطة به.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-2 bg-red-50 hover:bg-red-100 text-red-600 rounded-lg font-semibold text-sm transition-colors border border-red-100 hover:border-red-200" title="حذف الاختبار">
                                <i class="fas fa-trash-alt"></i> حذف
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="flex justify-center">
            <div class="rounded-xl p-3 bg-white border border-slate-200 shadow-sm">{{ $exams->links() }}</div>
        </div>
    @else
        <div class="rounded-xl border border-dashed border-slate-200 bg-slate-50/50 py-12 text-center">
            <div class="w-16 h-16 rounded-2xl bg-sky-100 flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-clipboard-check text-2xl text-sky-500"></i>
            </div>
            <h3 class="text-lg font-bold text-slate-800 mb-2">لا توجد اختبارات</h3>
            <p class="text-sm text-slate-500 mb-4">لم يتم إنشاء أي اختبارات بعد</p>
            <a href="{{ route('instructor.exams.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-sky-500 hover:bg-sky-600 text-white rounded-xl font-semibold transition-colors">
                <i class="fas fa-plus"></i> إنشاء اختبار
            </a>
        </div>
    @endif
</div>
@endsection
