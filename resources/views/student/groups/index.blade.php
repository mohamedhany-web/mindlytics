@extends('layouts.app')

@section('title', 'مجموعاتي - Mindlytics')
@section('header', 'مجموعاتي')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-5 sm:p-6 mb-6">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-xl bg-sky-100 text-sky-600 flex items-center justify-center">
                <i class="fas fa-users text-lg"></i>
            </div>
            <div>
                <h1 class="text-xl font-bold text-slate-800">مجموعاتي</h1>
                <p class="text-sm text-slate-600 mt-0.5">المجموعات التي أنتمي إليها في الكورسات</p>
            </div>
        </div>
    </div>

    @if($groups->isEmpty())
        <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-12 text-center">
            <div class="w-16 h-16 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-users text-2xl"></i>
            </div>
            <p class="text-slate-600 font-medium">لا تنتمي لأي مجموعة حالياً</p>
            <p class="text-sm text-slate-500 mt-1">سيتم إضافتك لمجموعة من قبل المدرب عند تفعيل المجموعات في الكورس</p>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($groups as $group)
                <a href="{{ route('student.groups.show', $group) }}"
                   class="block rounded-2xl bg-white border border-slate-200 shadow-sm p-5 hover:border-sky-200 hover:shadow-md transition-all">
                    <div class="flex items-start gap-3">
                        <div class="w-11 h-11 rounded-xl bg-sky-100 text-sky-600 flex items-center justify-center shrink-0">
                            <i class="fas fa-user-friends"></i>
                        </div>
                        <div class="min-w-0 flex-1">
                            <h3 class="font-bold text-slate-800 truncate">{{ $group->name }}</h3>
                            <p class="text-sm text-slate-500 mt-0.5">{{ $group->course->title ?? '—' }}</p>
                            <div class="flex items-center gap-2 mt-2">
                                <span class="text-xs text-slate-500">
                                    {{ $group->members->count() }} / {{ $group->max_members }} عضو
                                </span>
                                @if($group->leader)
                                    <span class="text-xs text-amber-600">قائد: {{ $group->leader->name }}</span>
                                @endif
                            </div>
                        </div>
                        <i class="fas fa-chevron-left text-slate-400 shrink-0 mt-1"></i>
                    </div>
                </a>
            @endforeach
        </div>
    @endif
</div>
@endsection
