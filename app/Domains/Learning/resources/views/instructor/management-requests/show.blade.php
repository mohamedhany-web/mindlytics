@extends('layouts.app')

@section('title', __('instructor.request_details_title') . ' - Mindlytics')
@section('header', __('instructor.request_details_title'))

@section('content')
<div class="space-y-6 max-w-3xl mx-auto w-full">
    <a href="{{ route('instructor.management-requests.index') }}"
       class="inline-flex items-center gap-2 text-sm font-semibold text-slate-600 hover:text-sky-700 transition-colors">
        <i class="fas fa-arrow-right text-xs"></i>
        {{ __('instructor.back_to_list') }}
    </a>

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="px-5 py-5 sm:px-6 border-b border-slate-200 bg-slate-50/80">
            <h1 class="text-xl sm:text-2xl font-bold text-slate-900 leading-snug">{{ $request->subject }}</h1>
            <div class="flex flex-wrap items-center gap-3 mt-3 text-sm text-slate-500">
                <span class="inline-flex items-center gap-1.5 tabular-nums">
                    <i class="fas fa-calendar-alt text-slate-400 text-xs"></i>
                    {{ $request->created_at->format('Y-m-d H:i') }}
                </span>
                @if($request->status == 'pending')
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold border bg-amber-50 text-amber-800 border-amber-100">{{ __('instructor.pending_review') }}</span>
                @elseif($request->status == 'approved')
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold border bg-emerald-50 text-emerald-800 border-emerald-100">{{ __('instructor.approved') }}</span>
                @else
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold border bg-rose-50 text-rose-800 border-rose-100">{{ __('instructor.rejected') }}</span>
                @endif
            </div>
        </div>

        <div class="px-5 py-6 sm:px-6">
            <h2 class="text-xs font-bold text-slate-500 uppercase tracking-wide mb-2">{{ __('instructor.request_text_label') }}</h2>
            <div class="rounded-xl border border-slate-100 bg-slate-50/50 p-4 text-slate-800 text-sm leading-relaxed whitespace-pre-wrap">{{ $request->message }}</div>
        </div>

        @if($request->admin_reply)
            <div class="px-5 py-6 sm:px-6 border-t border-slate-200 bg-sky-50/60">
                <h2 class="text-xs font-bold text-sky-800 uppercase tracking-wide mb-2 flex items-center gap-2">
                    <i class="fas fa-reply text-sky-600"></i>
                    {{ __('instructor.admin_response_label') }}
                </h2>
                <div class="rounded-xl border border-sky-100 bg-white p-4 text-slate-800 text-sm leading-relaxed whitespace-pre-wrap shadow-sm">{{ $request->admin_reply }}</div>
                <p class="text-xs text-slate-500 mt-3 flex flex-wrap items-center gap-2">
                    <span class="tabular-nums">{{ $request->replied_at?->format('Y-m-d H:i') }}</span>
                    @if($request->repliedByUser)
                        <span class="text-slate-400">—</span>
                        <span class="font-medium text-slate-600">{{ $request->repliedByUser->name }}</span>
                    @endif
                </p>
            </div>
        @endif
    </div>
</div>
@endsection
