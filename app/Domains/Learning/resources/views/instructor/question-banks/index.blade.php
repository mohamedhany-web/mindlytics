@extends('layouts.app')

@section('title', __('instructor.question_banks'))
@section('header', __('instructor.question_banks'))

@section('content')
<div class="space-y-6">
    <div class="rounded-2xl p-5 sm:p-6 bg-white border border-slate-200 shadow-sm">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">{{ __('instructor.question_banks') }}</h1>
                <p class="text-sm text-slate-500 mt-0.5">{{ __('instructor.question_banks_manage_desc') }}</p>
            </div>
            <a href="{{ route('instructor.question-banks.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-sky-500 hover:bg-sky-600 text-white rounded-xl font-semibold transition-colors">
                <i class="fas fa-plus"></i>
                <span>{{ __('instructor.create_question_bank') }}</span>
            </a>
        </div>
    </div>

    @if($questionBanks->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($questionBanks as $bank)
                <div class="rounded-xl bg-white border border-slate-200 shadow-sm hover:border-sky-300 hover:shadow-md transition-all p-5">
                    <div class="flex items-start justify-between gap-3 mb-4">
                        <h3 class="text-lg font-bold text-slate-800 flex-1">{{ $bank->title }}</h3>
                        <span class="shrink-0 inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold {{ $bank->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                            {{ $bank->is_active ? __('instructor.active_status') : __('instructor.inactive_status') }}
                        </span>
                    </div>
                    @if($bank->description)
                        <p class="text-sm text-slate-600 mb-4 line-clamp-2">{{ $bank->description }}</p>
                    @endif
                    <div class="flex items-center gap-4 text-sm text-slate-500 mb-4">
                        <span class="font-semibold text-slate-700">{{ $bank->questions_count }}</span> {{ __('instructor.question_single') }}
                        @if($bank->difficulty)
                            <span>• {{ $bank->difficulty === 'easy' ? __('instructor.difficulty_easy') : ($bank->difficulty === 'medium' ? __('instructor.intermediate') : __('instructor.difficulty_hard')) }}</span>
                        @endif
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('instructor.question-banks.show', $bank) }}" class="flex-1 inline-flex items-center justify-center gap-2 px-3 py-2 bg-sky-500 hover:bg-sky-600 text-white rounded-lg text-sm font-semibold transition-colors">
                            <i class="fas fa-eye"></i> {{ __('common.view') }}
                        </a>
                        <a href="{{ route('instructor.question-banks.edit', $bank) }}" class="p-2 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 transition-colors" title="{{ __('common.edit') }}">
                            <i class="fas fa-edit"></i>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="flex justify-center">
            <div class="rounded-xl p-3 bg-white border border-slate-200 shadow-sm">{{ $questionBanks->links() }}</div>
        </div>
    @else
        <div class="rounded-xl border border-dashed border-slate-200 bg-slate-50/50 py-12 text-center">
            <div class="w-16 h-16 rounded-2xl bg-sky-100 flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-database text-2xl text-sky-500"></i>
            </div>
            <h3 class="text-lg font-bold text-slate-800 mb-2">{{ __('instructor.no_question_banks') }}</h3>
            <p class="text-sm text-slate-500 mb-4 max-w-sm mx-auto">{{ __('instructor.no_question_banks_description') }}</p>
            <a href="{{ route('instructor.question-banks.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-sky-500 hover:bg-sky-600 text-white rounded-xl font-semibold transition-colors">
                <i class="fas fa-plus"></i> {{ __('instructor.create_question_bank') }}
            </a>
        </div>
    @endif
</div>
@endsection
