@extends('layouts.app')

@section('title', __('instructor.submit_request_title') . ' - Mindlytics')
@section('header', __('instructor.submit_request_title'))

@section('content')
<div class="space-y-6 max-w-3xl mx-auto w-full">
    <div class="relative rounded-2xl border border-slate-200 bg-gradient-to-br from-white via-slate-50/40 to-white shadow-sm overflow-hidden">
        <div class="absolute top-0 right-0 w-24 h-24 rounded-full bg-sky-100/50 -translate-y-1/2 translate-x-1/2 pointer-events-none" aria-hidden="true"></div>
        <div class="relative p-5 sm:p-6 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-white border border-slate-100 shadow-sm flex items-center justify-center flex-shrink-0">
                <i class="fas fa-edit text-sky-600 text-lg"></i>
            </div>
            <div>
                <h1 class="text-lg sm:text-xl font-bold text-slate-800">{{ __('instructor.submit_new_request_title') }}</h1>
                <p class="text-sm text-slate-500 mt-0.5">{{ __('instructor.submit_request_desc') }}</p>
            </div>
        </div>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm p-6 sm:p-8">
        <form action="{{ route('instructor.management-requests.store') }}" method="POST" class="space-y-6">
            @csrf

            <div>
                <label for="mgmt-subject" class="block text-sm font-semibold text-slate-700 mb-2">{{ __('instructor.request_subject_required') }}</label>
                <input id="mgmt-subject" type="text" name="subject" value="{{ old('subject') }}" required
                       class="w-full rounded-xl border border-slate-200 px-4 py-3 text-slate-900 placeholder-slate-400 focus:ring-2 focus:ring-sky-500/30 focus:border-sky-500 transition-shadow"
                       placeholder="{{ __('instructor.subject_placeholder') }}">
                @error('subject')
                    <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="mgmt-message" class="block text-sm font-semibold text-slate-700 mb-2">{{ __('instructor.request_details_required') }}</label>
                <textarea id="mgmt-message" name="message" rows="6" required
                          class="w-full rounded-xl border border-slate-200 px-4 py-3 text-slate-900 placeholder-slate-400 focus:ring-2 focus:ring-sky-500/30 focus:border-sky-500 transition-shadow resize-y min-h-[140px]"
                          placeholder="{{ __('instructor.message_placeholder') }}">{{ old('message') }}</textarea>
                @error('message')
                    <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex flex-wrap gap-3 justify-end pt-2 border-t border-slate-100">
                <a href="{{ route('instructor.management-requests.index') }}"
                   class="inline-flex items-center justify-center px-5 py-2.5 rounded-xl border border-slate-200 text-slate-700 font-semibold text-sm hover:bg-slate-50 transition-colors">
                    {{ __('common.cancel') }}
                </a>
                <button type="submit" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-sky-600 hover:bg-sky-700 text-white font-semibold text-sm shadow-sm border border-sky-700/20 transition-colors">
                    <i class="fas fa-paper-plane text-sm"></i>
                    {{ __('instructor.send_request') }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
