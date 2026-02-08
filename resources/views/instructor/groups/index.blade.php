@extends('layouts.app')

@section('title', 'المجموعات')
@section('header', 'المجموعات')

@section('content')
<div class="space-y-6">
    <div class="rounded-2xl p-5 sm:p-6 bg-white border border-slate-200 shadow-sm">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">المجموعات</h1>
                <p class="text-sm text-slate-500 mt-0.5">إدارة مجموعات الطلاب في الكورسات</p>
            </div>
            <a href="{{ route('instructor.groups.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-sky-500 hover:bg-sky-600 text-white rounded-xl font-semibold transition-colors">
                <i class="fas fa-plus"></i>
                <span>إنشاء مجموعة جديدة</span>
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
                <div class="w-12 h-12 rounded-xl bg-sky-100 flex items-center justify-center text-sky-600"><i class="fas fa-users"></i></div>
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
                    <p class="text-xs font-semibold text-slate-500 uppercase">معطلة</p>
                    <p class="text-2xl font-bold text-slate-800">{{ $stats['inactive'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-amber-100 flex items-center justify-center text-amber-600"><i class="fas fa-ban"></i></div>
            </div>
        </div>
        <div class="rounded-xl p-4 bg-white border border-slate-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase">إجمالي الأعضاء</p>
                    <p class="text-2xl font-bold text-slate-800">{{ $stats['total_members'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-violet-100 flex items-center justify-center text-violet-600"><i class="fas fa-user-friends"></i></div>
            </div>
        </div>
    </div>

    <div class="rounded-xl bg-white border border-slate-200 shadow-sm p-5">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label for="course_id" class="block text-sm font-semibold text-slate-700 mb-1">الكورس</label>
                <select name="course_id" id="course_id" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 text-slate-800">
                    <option value="">جميع الكورسات</option>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}" {{ request('course_id') == $course->id ? 'selected' : '' }}>{{ $course->title }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="status" class="block text-sm font-semibold text-slate-700 mb-1">الحالة</label>
                <select name="status" id="status" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 text-slate-800">
                    <option value="">الكل</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>نشطة</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>معطلة</option>
                    <option value="archived" {{ request('status') == 'archived' ? 'selected' : '' }}>مؤرشفة</option>
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
                @if(request()->anyFilled(['course_id', 'status', 'search']))
                    <a href="{{ route('instructor.groups.index') }}" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-semibold transition-colors">
                        <i class="fas fa-times"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    @if($groups->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($groups as $group)
                <div class="rounded-xl bg-white border border-slate-200 shadow-sm hover:border-sky-300 hover:shadow-md transition-all overflow-hidden flex flex-col">
                    <div class="p-5 flex-1">
                        <div class="flex items-center justify-between gap-2 mb-3">
                            <h3 class="text-lg font-bold text-slate-800 truncate">{{ $group->name }}</h3>
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold shrink-0
                                @if($group->status == 'active') bg-emerald-100 text-emerald-700
                                @elseif($group->status == 'inactive') bg-amber-100 text-amber-700
                                @else bg-slate-100 text-slate-600
                                @endif">
                                @if($group->status == 'active') نشطة
                                @elseif($group->status == 'inactive') معطلة
                                @else مؤرشفة
                                @endif
                            </span>
                        </div>
                        @if($group->description)
                            <p class="text-sm text-slate-600 mb-3 line-clamp-2">{{ $group->description }}</p>
                        @endif
                        <div class="space-y-2 text-sm text-slate-500">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-book text-sky-500 w-4"></i>
                                <span>{{ $group->course->title ?? '—' }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <i class="fas fa-users text-violet-500 w-4"></i>
                                <span>{{ $group->members_count ?? 0 }} / {{ $group->max_members ?? '—' }} أعضاء</span>
                            </div>
                            @if($group->leader)
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-crown text-amber-500 w-4"></i>
                                    <span>{{ $group->leader->name }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="border-t border-slate-200 p-4 bg-slate-50/50">
                        <a href="{{ route('instructor.groups.show', $group) }}" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-sky-500 hover:bg-sky-600 text-white rounded-xl font-semibold text-sm transition-colors">
                            <i class="fas fa-eye"></i> عرض التفاصيل
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="flex justify-center">
            <div class="rounded-xl p-3 bg-white border border-slate-200 shadow-sm">{{ $groups->links() }}</div>
        </div>
    @else
        <div class="rounded-xl border border-dashed border-slate-200 bg-slate-50/50 py-12 text-center">
            <div class="w-16 h-16 rounded-2xl bg-sky-100 flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-users text-2xl text-sky-500"></i>
            </div>
            <h3 class="text-lg font-bold text-slate-800 mb-2">لا توجد مجموعات</h3>
            <p class="text-sm text-slate-500 mb-4">لم يتم إنشاء أي مجموعات بعد</p>
            <a href="{{ route('instructor.groups.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-sky-500 hover:bg-sky-600 text-white rounded-xl font-semibold transition-colors">
                <i class="fas fa-plus"></i> إنشاء مجموعة جديدة
            </a>
        </div>
    @endif
</div>
@endsection
