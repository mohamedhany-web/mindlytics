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
                        <a href="{{ route('instructor.groups.index') }}" class="hover:text-sky-600 transition-colors">{{ __('instructor.groups') }}</a>
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
                            @if($group->status == 'active') {{ __('instructor.active') }}
                            @elseif($group->status == 'inactive') {{ __('instructor.inactive') }}
                            @else {{ __('instructor.archived') }}
                            @endif
                        </span>
                    </div>
                    <p class="text-sm text-slate-600">{{ $group->course->title ?? __('instructor.not_specified') }}</p>
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    <a href="{{ route('instructor.groups.edit', $group) }}"
                       class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-semibold transition-colors">
                        <i class="fas fa-edit"></i> {{ __('common.edit') }}
                    </a>
                    <a href="{{ route('instructor.groups.index') }}"
                       class="inline-flex items-center gap-2 px-4 py-2.5 bg-sky-500 hover:bg-sky-600 text-white rounded-xl font-semibold transition-colors">
                        <i class="fas fa-arrow-right"></i> {{ __('instructor.back') }}
                    </a>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- المحتوى الرئيسي -->
            <div class="lg:col-span-2 space-y-6">
                @if($group->description)
                <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-5 sm:p-6">
                    <h3 class="text-base font-bold text-slate-800 mb-3">{{ __('instructor.description') }}</h3>
                    <p class="text-slate-600 leading-relaxed">{{ $group->description }}</p>
                </div>
                @endif

                <!-- الأعضاء -->
                <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-5 sm:p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-base font-bold text-slate-800">{{ __('instructor.members_label') }}</h3>
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
                                            <i class="fas fa-crown"></i> {{ __('instructor.group_leader') }}
                                        </span>
                                    @endif
                                    <form action="{{ route('instructor.groups.remove-member', $group) }}" method="POST" class="inline"
                                          onsubmit="return confirm('{{ __('instructor.confirm_remove_member') }}')">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" name="user_id" value="{{ $member->id }}">
                                        <button type="submit" class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="{{ __('instructor.remove_member_title') }}">
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
                            <p class="text-slate-600 font-medium">{{ __('instructor.no_members_in_group') }}</p>
                            <p class="text-sm text-slate-500 mt-1">{{ __('instructor.add_members_from_right') }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- واجبات المجموعة -->
            <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-5 sm:p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base font-bold text-slate-800">{{ __('instructor.group_assignments_title') }}</h3>
                    <a href="{{ route('instructor.assignments.create') }}?advanced_course_id={{ $group->course_id }}&group_id={{ $group->id }}"
                       class="inline-flex items-center gap-2 px-3 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-xl text-sm font-semibold transition-colors">
                        <i class="fas fa-plus"></i> {{ __('instructor.add_assignment_to_group') }}
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
                                        <span>{{ $a->submissions_count ?? 0 }} {{ __('instructor.submission_single') }}</span>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2 shrink-0">
                                    <span class="text-xs font-semibold px-2 py-1 rounded
                                        @if($a->status == 'published') bg-emerald-100 text-emerald-700
                                        @elseif($a->status == 'draft') bg-slate-200 text-slate-600
                                        @else bg-slate-100 text-slate-500
                                        @endif">{{ $a->status == 'published' ? __('instructor.published') : ($a->status == 'draft' ? __('instructor.draft') : __('instructor.archived')) }}</span>
                                    <a href="{{ route('instructor.assignments.edit', $a) }}" class="p-2 text-slate-400 hover:text-sky-600 hover:bg-sky-50 rounded-lg" title="{{ __('common.edit') }}"><i class="fas fa-edit text-sm"></i></a>
                                    <a href="{{ route('instructor.assignments.submissions', $a) }}" class="p-2 text-slate-400 hover:text-sky-600 hover:bg-sky-50 rounded-lg" title="{{ __('instructor.submissions_title') }}"><i class="fas fa-inbox text-sm"></i></a>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <div class="text-center py-6 rounded-xl bg-slate-50 border border-slate-100">
                        <div class="w-12 h-12 rounded-xl bg-amber-100 text-amber-500 flex items-center justify-center mx-auto mb-2">
                            <i class="fas fa-tasks"></i>
                        </div>
                        <p class="text-slate-600 font-medium">{{ __('instructor.no_group_assignments') }}</p>
                        <p class="text-sm text-slate-500 mt-1">{{ __('instructor.add_group_assignment_hint') }}</p>
                        <a href="{{ route('instructor.assignments.create') }}?advanced_course_id={{ $group->course_id }}&group_id={{ $group->id }}"
                           class="inline-flex items-center gap-2 mt-3 px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-xl text-sm font-semibold">
                            <i class="fas fa-plus"></i> {{ __('instructor.add_assignment_to_group') }}
                        </a>
                    </div>
                @endif
            </div>

            <!-- الشريط الجانبي -->
            <div class="space-y-6">
                <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-5 sm:p-6">
                    <h3 class="text-base font-bold text-slate-800 mb-4">{{ __('instructor.group_info_title') }}</h3>
                    <dl class="space-y-3 text-sm">
                        <div>
                            <dt class="text-slate-500 mb-0.5">{{ __('instructor.course_label') }}</dt>
                            <dd class="font-medium text-slate-800">{{ $group->course->title ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-slate-500 mb-0.5">{{ __('instructor.max_members_label') }}</dt>
                            <dd class="font-medium text-slate-800">{{ $group->max_members }}</dd>
                        </div>
                        <div>
                            <dt class="text-slate-500 mb-0.5">{{ __('instructor.current_members_count') }}</dt>
                            <dd class="font-medium text-slate-800">{{ $group->members->count() }}</dd>
                        </div>
                        @if($group->leader)
                        <div>
                            <dt class="text-slate-500 mb-0.5">{{ __('instructor.group_leader_label') }}</dt>
                            <dd class="font-medium text-slate-800">{{ $group->leader->name }}</dd>
                        </div>
                        @endif
                        <div>
                            <dt class="text-slate-500 mb-0.5">{{ __('instructor.created_at_label') }}</dt>
                            <dd class="font-medium text-slate-800">{{ $group->created_at->format('Y/m/d') }}</dd>
                        </div>
                    </dl>
                </div>

                @if(!$group->isFull())
                <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-5 sm:p-6">
                    <h3 class="text-base font-bold text-slate-800 mb-4">{{ __('instructor.add_member_title') }}</h3>
                    <form action="{{ route('instructor.groups.add-member', $group) }}" method="POST" class="space-y-3">
                        @csrf
                        <div>
                            <label for="add_user_id" class="block text-sm font-medium text-slate-700 mb-1">{{ __('instructor.students') }}</label>
                            <select name="user_id" id="add_user_id" required
                                    class="w-full px-4 py-2.5 border border-slate-200 rounded-xl bg-white text-slate-800 focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                                <option value="">{{ __('instructor.choose_student_option') }}</option>
                                @foreach($enrollments as $enrollment)
                                    @if(!$group->members->contains($enrollment->user_id))
                                    <option value="{{ $enrollment->user->id }}">{{ $enrollment->user->name }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="add_role" class="block text-sm font-medium text-slate-700 mb-1">{{ __('instructor.role_label') }}</label>
                            <select name="role" id="add_role"
                                    class="w-full px-4 py-2.5 border border-slate-200 rounded-xl bg-white text-slate-800 focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                                <option value="member">{{ __('instructor.member_option') }}</option>
                                <option value="leader">{{ __('instructor.leader_option') }}</option>
                            </select>
                        </div>
                        <button type="submit"
                                class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-sky-500 hover:bg-sky-600 text-white rounded-xl font-semibold transition-colors">
                            <i class="fas fa-plus"></i> {{ __('instructor.add_btn') }}
                        </button>
                    </form>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
