@extends('layouts.app')

@section('title', __('instructor.my_requests_to_management') . ' - Mindlytics')
@section('header', __('instructor.submit_requests_to_management'))

@section('content')
<div class="space-y-6">
    {{-- عنوان الصفحة --}}
    <div class="relative rounded-2xl border border-slate-200 bg-gradient-to-br from-white via-slate-50/40 to-white shadow-sm overflow-hidden">
        <div class="absolute top-0 right-0 w-28 h-28 rounded-full bg-sky-100/50 -translate-y-1/2 translate-x-1/2 pointer-events-none" aria-hidden="true"></div>
        <div class="relative flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 p-5 sm:p-6">
            <div class="flex items-center gap-4 min-w-0 flex-1">
                <div class="w-14 h-14 rounded-2xl bg-white border border-slate-100 shadow-sm flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-paper-plane text-sky-600 text-2xl"></i>
                </div>
                <div class="min-w-0">
                    <p class="text-xs font-semibold text-sky-600 uppercase tracking-wider mb-1">{{ __('instructor.instructor_panel') }}</p>
                    <h1 class="text-xl sm:text-2xl font-bold text-slate-800 truncate">{{ __('instructor.my_requests_to_management') }}</h1>
                    <p class="text-sm text-slate-500 mt-0.5">{{ __('instructor.my_requests_description') }}</p>
                </div>
            </div>
            <a href="{{ route('instructor.management-requests.create') }}"
               class="inline-flex items-center justify-center gap-2 rounded-xl bg-sky-600 hover:bg-sky-700 text-white px-5 py-2.5 text-sm font-semibold shadow-sm border border-sky-700/20 transition-colors flex-shrink-0">
                <i class="fas fa-plus text-sm"></i>
                {{ __('instructor.new_request') }}
            </a>
        </div>
    </div>

    {{-- قائمة + فلتر --}}
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="px-5 py-4 sm:px-6 border-b border-slate-200">
            <h2 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                <span class="w-9 h-9 rounded-xl bg-sky-50 border border-slate-100 flex items-center justify-center">
                    <i class="fas fa-inbox text-sky-600 text-sm"></i>
                </span>
                {{ __('instructor.my_requests_to_management') }}
            </h2>
        </div>

        <form method="GET" class="px-5 py-4 sm:px-6 bg-slate-50/80 border-b border-slate-100 flex flex-wrap items-center gap-3">
            <label class="sr-only" for="mgmt-req-status">{{ __('common.status') }}</label>
            <select id="mgmt-req-status" name="status"
                    class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm text-slate-800 focus:ring-2 focus:ring-sky-500/30 focus:border-sky-500 min-w-[180px]">
                <option value="">{{ __('instructor.all_statuses_filter') }}</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>{{ __('instructor.pending_review') }}</option>
                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>{{ __('instructor.approved') }}</option>
                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>{{ __('instructor.rejected') }}</option>
            </select>
            <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-slate-800 hover:bg-slate-900 text-white px-4 py-2.5 text-sm font-semibold transition-colors">
                <i class="fas fa-filter text-xs"></i>
                {{ __('common.search') }}
            </button>
            @if(request()->filled('status'))
                <a href="{{ route('instructor.management-requests.index') }}" class="text-sm font-semibold text-sky-600 hover:text-sky-700">
                    {{ __('instructor.clear_filter') }}
                </a>
            @endif
        </form>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th scope="col" class="px-4 sm:px-6 py-3.5 text-right text-xs font-bold text-slate-600 uppercase tracking-wide">{{ __('instructor.request_subject') }}</th>
                        <th scope="col" class="px-4 sm:px-6 py-3.5 text-right text-xs font-bold text-slate-600 uppercase tracking-wide">{{ __('common.status') }}</th>
                        <th scope="col" class="px-4 sm:px-6 py-3.5 text-right text-xs font-bold text-slate-600 uppercase tracking-wide">{{ __('common.date') }}</th>
                        <th scope="col" class="px-4 sm:px-6 py-3.5 text-center text-xs font-bold text-slate-600 uppercase tracking-wide">{{ __('instructor.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 bg-white">
                    @forelse($requests as $req)
                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <td class="px-4 sm:px-6 py-4">
                            <p class="font-semibold text-slate-900 text-sm">{{ $req->subject }}</p>
                            <p class="text-xs text-slate-500 mt-0.5 line-clamp-2">{{ Str::limit($req->message, 80) }}</p>
                        </td>
                        <td class="px-4 sm:px-6 py-4 whitespace-nowrap">
                            @if($req->status == 'pending')
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold border bg-amber-50 text-amber-800 border-amber-100">{{ __('instructor.pending_review') }}</span>
                            @elseif($req->status == 'approved')
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold border bg-emerald-50 text-emerald-800 border-emerald-100">{{ __('instructor.approved') }}</span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold border bg-rose-50 text-rose-800 border-rose-100">{{ __('instructor.rejected') }}</span>
                            @endif
                        </td>
                        <td class="px-4 sm:px-6 py-4 whitespace-nowrap text-sm text-slate-600 tabular-nums">
                            {{ $req->created_at->format('Y-m-d H:i') }}
                        </td>
                        <td class="px-4 sm:px-6 py-4 text-center whitespace-nowrap">
                            <a href="{{ route('instructor.management-requests.show', $req) }}"
                               class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-sky-50 hover:bg-sky-100 text-sky-700 border border-sky-100 transition-colors"
                               title="{{ __('common.view') }}">
                                <i class="fas fa-eye text-sm"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-14 text-center">
                            <div class="flex flex-col items-center gap-4 max-w-sm mx-auto">
                                <div class="w-16 h-16 rounded-2xl bg-slate-100 border border-slate-200 flex items-center justify-center">
                                    <i class="fas fa-inbox text-slate-400 text-2xl"></i>
                                </div>
                                <div>
                                    <p class="font-bold text-slate-800">{{ __('instructor.no_requests_yet') }}</p>
                                    <a href="{{ route('instructor.management-requests.create') }}" class="mt-3 inline-flex items-center gap-2 text-sm font-semibold text-sky-600 hover:text-sky-700">
                                        <i class="fas fa-plus text-xs"></i>
                                        {{ __('instructor.new_request') }}
                                    </a>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($requests->hasPages())
            <div class="px-5 py-4 sm:px-6 border-t border-slate-200 bg-slate-50/80">
                {{ $requests->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
