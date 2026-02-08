@extends('layouts.app')

@section('title', $group->name . ' - Mindlytics')
@section('header', $group->name)

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <div class="space-y-6">
        <!-- الهيدر -->
        <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-5 sm:p-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="min-w-0">
                    <nav class="text-sm text-slate-500 mb-2">
                        <a href="{{ route('instructor.groups.index') }}" class="hover:text-sky-600 transition-colors">المجموعات</a>
                        <span class="mx-2">/</span>
                        <span class="text-slate-700 font-semibold">{{ $group->name }}</span>
                    </nav>
                    <div class="flex flex-wrap items-center gap-2 mb-1">
                        <h1 class="text-xl sm:text-2xl font-bold text-slate-800">{{ $group->name }}</h1>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold
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
                    <p class="text-sm text-slate-600">{{ $group->course->title ?? 'غير محدد' }}</p>
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    <a href="{{ route('instructor.groups.edit', $group) }}"
                       class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-semibold transition-colors">
                        <i class="fas fa-edit"></i> تعديل
                    </a>
                    <a href="{{ route('instructor.groups.index') }}"
                       class="inline-flex items-center gap-2 px-4 py-2.5 bg-sky-500 hover:bg-sky-600 text-white rounded-xl font-semibold transition-colors">
                        <i class="fas fa-arrow-right"></i> العودة
                    </a>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- المحتوى الرئيسي -->
            <div class="lg:col-span-2 space-y-6">
                @if($group->description)
                <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-5 sm:p-6">
                    <h3 class="text-base font-bold text-slate-800 mb-3">الوصف</h3>
                    <p class="text-slate-600 leading-relaxed">{{ $group->description }}</p>
                </div>
                @endif

                <!-- الأعضاء -->
                <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-5 sm:p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-base font-bold text-slate-800">الأعضاء</h3>
                        <span class="text-sm font-medium text-slate-600">
                            {{ $group->members->count() }} / {{ $group->max_members }}
                        </span>
                    </div>

                    @if($group->members->count() > 0)
                        <ul class="space-y-2">
                            @foreach($group->members as $member)
                            <li class="flex items-center justify-between p-3 rounded-xl bg-slate-50 hover:bg-slate-100 border border-slate-100 transition-colors">
                                <div class="flex items-center gap-3 min-w-0">
                                    <div class="w-10 h-10 rounded-xl bg-sky-100 text-sky-600 flex items-center justify-center font-bold shrink-0">
                                        {{ mb_substr($member->name ?? '?', 0, 1) }}
                                    </div>
                                    <div class="min-w-0">
                                        <div class="font-semibold text-slate-800 truncate">{{ $member->name }}</div>
                                        <div class="text-sm text-slate-500 truncate">{{ $member->email ?? '—' }}</div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2 shrink-0">
                                    @if($member->pivot->role == 'leader')
                                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-lg text-xs font-semibold bg-amber-100 text-amber-700">
                                            <i class="fas fa-crown"></i> قائد
                                        </span>
                                    @endif
                                    <form action="{{ route('instructor.groups.remove-member', $group) }}" method="POST" class="inline"
                                          onsubmit="return confirm('هل أنت متأكد من إزالة هذا العضو؟')">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" name="user_id" value="{{ $member->id }}">
                                        <button type="submit" class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="إزالة">
                                            <i class="fas fa-user-minus text-sm"></i>
                                        </button>
                                    </form>
                                </div>
                            </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="text-center py-8 rounded-xl bg-slate-50 border border-slate-100">
                            <div class="w-12 h-12 rounded-xl bg-slate-200 text-slate-500 flex items-center justify-center mx-auto mb-2">
                                <i class="fas fa-users"></i>
                            </div>
                            <p class="text-slate-600 font-medium">لا يوجد أعضاء في هذه المجموعة</p>
                            <p class="text-sm text-slate-500 mt-1">أضف أعضاء من القائمة على اليمين</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- واجبات المجموعة -->
            <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-5 sm:p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base font-bold text-slate-800">واجبات المجموعة</h3>
                    <a href="{{ route('instructor.assignments.create') }}?advanced_course_id={{ $group->course_id }}&group_id={{ $group->id }}"
                       class="inline-flex items-center gap-2 px-3 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-xl text-sm font-semibold transition-colors">
                        <i class="fas fa-plus"></i> إضافة واجب للمجموعة
                    </a>
                </div>
                @if(isset($groupAssignments) && $groupAssignments->count() > 0)
                    <ul class="space-y-2">
                        @foreach($groupAssignments as $a)
                            <li class="flex items-center justify-between p-3 rounded-xl bg-slate-50 border border-slate-100">
                                <div class="min-w-0 flex-1">
                                    <a href="{{ route('instructor.assignments.show', $a) }}" class="font-semibold text-slate-800 hover:text-sky-600 truncate block">{{ $a->title }}</a>
                                    <div class="flex items-center gap-2 mt-1 text-xs text-slate-500">
                                        @if($a->due_date)
                                            <span><i class="fas fa-calendar ml-1"></i> {{ $a->due_date->format('Y/m/d') }}</span>
                                        @endif
                                        <span>{{ $a->submissions_count ?? 0 }} تسليم</span>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2 shrink-0">
                                    <span class="text-xs font-semibold px-2 py-1 rounded
                                        @if($a->status == 'published') bg-emerald-100 text-emerald-700
                                        @elseif($a->status == 'draft') bg-slate-200 text-slate-600
                                        @else bg-slate-100 text-slate-500
                                        @endif">{{ $a->status == 'published' ? 'منشور' : ($a->status == 'draft' ? 'مسودة' : 'مؤرشف') }}</span>
                                    <a href="{{ route('instructor.assignments.edit', $a) }}" class="p-2 text-slate-400 hover:text-sky-600 hover:bg-sky-50 rounded-lg" title="تعديل"><i class="fas fa-edit text-sm"></i></a>
                                    <a href="{{ route('instructor.assignments.submissions', $a) }}" class="p-2 text-slate-400 hover:text-sky-600 hover:bg-sky-50 rounded-lg" title="التسليمات"><i class="fas fa-inbox text-sm"></i></a>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <div class="text-center py-6 rounded-xl bg-slate-50 border border-slate-100">
                        <div class="w-12 h-12 rounded-xl bg-amber-100 text-amber-500 flex items-center justify-center mx-auto mb-2">
                            <i class="fas fa-tasks"></i>
                        </div>
                        <p class="text-slate-600 font-medium">لا توجد واجبات مخصصة لهذه المجموعة</p>
                        <p class="text-sm text-slate-500 mt-1">أضف واجباً يظهر لأعضاء المجموعة فقط ويسلّمون جماعياً</p>
                        <a href="{{ route('instructor.assignments.create') }}?advanced_course_id={{ $group->course_id }}&group_id={{ $group->id }}"
                           class="inline-flex items-center gap-2 mt-3 px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-xl text-sm font-semibold">
                            <i class="fas fa-plus"></i> إضافة واجب للمجموعة
                        </a>
                    </div>
                @endif
            </div>

            <!-- الشريط الجانبي -->
            <div class="space-y-6">
                <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-5 sm:p-6">
                    <h3 class="text-base font-bold text-slate-800 mb-4">معلومات المجموعة</h3>
                    <dl class="space-y-3 text-sm">
                        <div>
                            <dt class="text-slate-500 mb-0.5">الكورس</dt>
                            <dd class="font-medium text-slate-800">{{ $group->course->title ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-slate-500 mb-0.5">الحد الأقصى للأعضاء</dt>
                            <dd class="font-medium text-slate-800">{{ $group->max_members }}</dd>
                        </div>
                        <div>
                            <dt class="text-slate-500 mb-0.5">عدد الأعضاء الحالي</dt>
                            <dd class="font-medium text-slate-800">{{ $group->members->count() }}</dd>
                        </div>
                        @if($group->leader)
                        <div>
                            <dt class="text-slate-500 mb-0.5">قائد المجموعة</dt>
                            <dd class="font-medium text-slate-800">{{ $group->leader->name }}</dd>
                        </div>
                        @endif
                        <div>
                            <dt class="text-slate-500 mb-0.5">تاريخ الإنشاء</dt>
                            <dd class="font-medium text-slate-800">{{ $group->created_at->format('Y/m/d') }}</dd>
                        </div>
                    </dl>
                </div>

                @if(!$group->isFull())
                <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-5 sm:p-6">
                    <h3 class="text-base font-bold text-slate-800 mb-4">إضافة عضو</h3>
                    <form action="{{ route('instructor.groups.add-member', $group) }}" method="POST" class="space-y-3">
                        @csrf
                        <div>
                            <label for="add_user_id" class="block text-sm font-medium text-slate-700 mb-1">الطالب</label>
                            <select name="user_id" id="add_user_id" required
                                    class="w-full px-4 py-2.5 border border-slate-200 rounded-xl bg-white text-slate-800 focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                                <option value="">اختر الطالب</option>
                                @foreach($enrollments as $enrollment)
                                    @if(!$group->members->contains($enrollment->user_id))
                                    <option value="{{ $enrollment->user->id }}">{{ $enrollment->user->name }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="add_role" class="block text-sm font-medium text-slate-700 mb-1">الدور</label>
                            <select name="role" id="add_role"
                                    class="w-full px-4 py-2.5 border border-slate-200 rounded-xl bg-white text-slate-800 focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                                <option value="member">عضو</option>
                                <option value="leader">قائد</option>
                            </select>
                        </div>
                        <button type="submit"
                                class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-sky-500 hover:bg-sky-600 text-white rounded-xl font-semibold transition-colors">
                            <i class="fas fa-plus"></i> إضافة
                        </button>
                    </form>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
