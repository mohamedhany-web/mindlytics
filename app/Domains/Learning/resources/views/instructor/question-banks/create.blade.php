@extends('layouts.app')

@section('title', __('instructor.create_question_bank_new') . ' - Mindlytics')
@section('header', __('instructor.create_question_bank_new'))

@section('content')
<div class="w-full px-4 sm:px-6 lg:px-8 py-6">
    <!-- هيدر الصفحة (عرض الصفحة كاملاً) -->
    <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-5 sm:p-6 mb-6">
        <nav class="text-sm text-slate-500 mb-2">
            <a href="{{ route('instructor.question-banks.index') }}" class="hover:text-sky-600 transition-colors">{{ __('instructor.question_banks') }}</a>
            <span class="mx-2">/</span>
            <span class="text-slate-700 font-semibold">{{ __('instructor.create_bank') }}</span>
        </nav>
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div class="flex flex-wrap items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-sky-100 text-sky-600 flex items-center justify-center shrink-0">
                    <i class="fas fa-database text-lg"></i>
                </div>
                <div class="min-w-0">
                    <h1 class="text-xl sm:text-2xl font-bold text-slate-800">{{ __('instructor.create_question_bank_new') }}</h1>
                    <p class="text-sm text-slate-600 mt-0.5">{{ __('instructor.create_question_bank_desc') }}</p>
                </div>
            </div>
            <a href="{{ route('instructor.question-banks.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-semibold transition-colors">
                <i class="fas fa-arrow-right"></i>
                {{ __('instructor.back') }}
            </a>
        </div>
    </div>

    <!-- بطاقة النموذج -->
    <div class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
        <form action="{{ route('instructor.question-banks.store') }}" method="POST">
            @csrf
            <div class="p-6 sm:p-8 space-y-8">
                <!-- معلومات بنك الأسئلة -->
                <div class="space-y-6">
                    <h2 class="text-lg font-bold text-slate-800 border-b border-slate-200 pb-2">{{ __('instructor.question_bank_info') }}</h2>
                    <div>
                        <label for="title" class="block text-sm font-semibold text-slate-700 mb-1">{{ __('instructor.title_required') }} <span class="text-red-500">*</span></label>
                        <input type="text" name="title" id="title" value="{{ old('title') }}" required
                               class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 text-slate-800"
                               placeholder="{{ __('instructor.question_bank_title_placeholder') }}">
                        @error('title')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="description" class="block text-sm font-semibold text-slate-700 mb-1">{{ __('instructor.description') }}</label>
                        <textarea name="description" id="description" rows="4"
                                  class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 text-slate-800"
                                  placeholder="{{ __('instructor.description_placeholder') }}">{{ old('description') }}</textarea>
                        @error('description')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div class="max-w-xs">
                        <label for="difficulty" class="block text-sm font-semibold text-slate-700 mb-1">{{ __('instructor.difficulty') }}</label>
                        <select name="difficulty" id="difficulty"
                                class="w-full px-4 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-sky-500/20 focus:border-sky-500 text-slate-800">
                            <option value="">{{ __('instructor.optional_label') }}</option>
                            <option value="easy" {{ old('difficulty') == 'easy' ? 'selected' : '' }}>{{ __('instructor.easy') }}</option>
                            <option value="medium" {{ old('difficulty') == 'medium' ? 'selected' : '' }}>{{ __('instructor.medium') }}</option>
                            <option value="hard" {{ old('difficulty') == 'hard' ? 'selected' : '' }}>{{ __('instructor.hard') }}</option>
                        </select>
                    </div>
                </div>

                <!-- الحالة -->
                <div class="space-y-4 pt-6 border-t border-slate-200">
                    <h2 class="text-lg font-bold text-slate-800 border-b border-slate-200 pb-2">{{ __('instructor.status_label') }}</h2>
                    <label class="inline-flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                               class="rounded border-slate-300 text-sky-600 focus:ring-sky-500">
                        <span class="text-sm font-medium text-slate-700">{{ __('instructor.bank_active') }}</span>
                    </label>
                </div>

                <!-- نصائح وأزرار -->
                <div class="pt-6 border-t border-slate-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div class="bg-sky-50 border border-sky-200 rounded-xl p-4 text-sm text-sky-800 max-w-md">
                        <span class="font-semibold">{{ __('instructor.tips') }}:</span> {{ __('instructor.tip_after_create_bank') }}
                    </div>
                    <div class="flex gap-3 shrink-0">
                        <button type="submit" class="px-6 py-2.5 bg-sky-600 hover:bg-sky-700 text-white rounded-xl font-semibold transition-colors">
                            <i class="fas fa-save ml-2"></i>
                            {{ __('instructor.create_bank_btn') }}
                        </button>
                        <a href="{{ route('instructor.question-banks.index') }}" class="px-6 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-semibold transition-colors">
                            {{ __('common.cancel') }}
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
