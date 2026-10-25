@extends('layouts.app')

@section('title', __('instructor.transfer_account') . ' - Mindlytics')
@section('header', __('instructor.transfer_account'))

@section('content')
<div class="space-y-6 max-w-4xl mx-auto w-full">
    <div class="relative rounded-2xl border border-slate-200 bg-gradient-to-br from-white via-slate-50/40 to-white shadow-sm overflow-hidden">
        <div class="absolute top-0 right-0 w-28 h-28 rounded-full bg-sky-100/50 -translate-y-1/2 translate-x-1/2 pointer-events-none" aria-hidden="true"></div>
        <div class="relative p-5 sm:p-6 flex items-start gap-4">
            <div class="w-14 h-14 rounded-2xl bg-white border border-slate-100 shadow-sm flex items-center justify-center flex-shrink-0">
                <i class="fas fa-university text-sky-600 text-2xl"></i>
            </div>
            <div class="min-w-0">
                <p class="text-xs font-semibold text-sky-600 uppercase tracking-wider mb-1">{{ __('instructor.instructor_panel') }}</p>
                <h1 class="text-xl sm:text-2xl font-bold text-slate-800">{{ __('instructor.transfer_account') }}</h1>
                <p class="text-sm text-slate-500 mt-1 leading-relaxed">{{ __('instructor.transfer_account_desc') }}</p>
            </div>
        </div>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
        <div class="px-5 py-4 sm:px-6 border-b border-slate-200 bg-slate-50/80">
            <h2 class="text-base font-bold text-slate-800 flex items-center gap-2">
                <span class="w-9 h-9 rounded-xl bg-sky-50 border border-slate-100 flex items-center justify-center">
                    <i class="fas fa-id-card text-sky-600 text-sm"></i>
                </span>
                {{ __('instructor.account_info') }}
            </h2>
            <p class="text-xs text-slate-500 mt-2 mr-11 leading-relaxed">{{ __('instructor.transfer_account_desc') }}</p>
        </div>

        <form action="{{ route('instructor.transfer-account.store') }}" method="POST" class="p-5 sm:p-8 space-y-6">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label for="bank_name" class="block text-sm font-semibold text-slate-700 mb-2">{{ __('instructor.bank_name') }}</label>
                    <input type="text" name="bank_name" id="bank_name" value="{{ old('bank_name', $detail->bank_name) }}"
                           class="w-full px-4 py-3 border border-slate-200 rounded-xl text-slate-900 placeholder-slate-400 focus:ring-2 focus:ring-sky-500/30 focus:border-sky-500 transition-shadow"
                           placeholder="{{ __('instructor.placeholder_bank_example') }}">
                    @error('bank_name')<p class="text-red-600 text-xs mt-1.5">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="account_holder_name" class="block text-sm font-semibold text-slate-700 mb-2">{{ __('instructor.account_holder_name') }}</label>
                    <input type="text" name="account_holder_name" id="account_holder_name" value="{{ old('account_holder_name', $detail->account_holder_name) }}"
                           class="w-full px-4 py-3 border border-slate-200 rounded-xl text-slate-900 placeholder-slate-400 focus:ring-2 focus:ring-sky-500/30 focus:border-sky-500 transition-shadow"
                           placeholder="{{ __('instructor.placeholder_name_on_card') }}">
                    @error('account_holder_name')<p class="text-red-600 text-xs mt-1.5">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="account_number" class="block text-sm font-semibold text-slate-700 mb-2">{{ __('instructor.account_number') }}</label>
                    <input type="text" name="account_number" id="account_number" value="{{ old('account_number', $detail->account_number) }}" dir="ltr"
                           class="w-full px-4 py-3 border border-slate-200 rounded-xl text-slate-900 placeholder-slate-400 focus:ring-2 focus:ring-sky-500/30 focus:border-sky-500 transition-shadow"
                           placeholder="{{ __('instructor.placeholder_account_number') }}">
                    @error('account_number')<p class="text-red-600 text-xs mt-1.5">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="iban" class="block text-sm font-semibold text-slate-700 mb-2">{{ __('instructor.iban') }}</label>
                    <input type="text" name="iban" id="iban" value="{{ old('iban', $detail->iban) }}" dir="ltr"
                           class="w-full px-4 py-3 border border-slate-200 rounded-xl text-slate-900 placeholder-slate-400 focus:ring-2 focus:ring-sky-500/30 focus:border-sky-500 transition-shadow"
                           placeholder="EG...">
                    @error('iban')<p class="text-red-600 text-xs mt-1.5">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="branch_name" class="block text-sm font-semibold text-slate-700 mb-2">{{ __('instructor.branch_name') }}</label>
                    <input type="text" name="branch_name" id="branch_name" value="{{ old('branch_name', $detail->branch_name) }}"
                           class="w-full px-4 py-3 border border-slate-200 rounded-xl text-slate-900 placeholder-slate-400 focus:ring-2 focus:ring-sky-500/30 focus:border-sky-500 transition-shadow"
                           placeholder="{{ __('instructor.placeholder_branch_optional') }}">
                    @error('branch_name')<p class="text-red-600 text-xs mt-1.5">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="swift_code" class="block text-sm font-semibold text-slate-700 mb-2">{{ __('instructor.swift_code') }}</label>
                    <input type="text" name="swift_code" id="swift_code" value="{{ old('swift_code', $detail->swift_code) }}" dir="ltr"
                           class="w-full px-4 py-3 border border-slate-200 rounded-xl text-slate-900 placeholder-slate-400 focus:ring-2 focus:ring-sky-500/30 focus:border-sky-500 transition-shadow"
                           placeholder="{{ __('instructor.placeholder_optional') }}">
                    @error('swift_code')<p class="text-red-600 text-xs mt-1.5">{{ $message }}</p>@enderror
                </div>
            </div>
            <div>
                <label for="notes" class="block text-sm font-semibold text-slate-700 mb-2">{{ __('instructor.notes') }}</label>
                <textarea name="notes" id="notes" rows="3"
                          class="w-full px-4 py-3 border border-slate-200 rounded-xl text-slate-900 placeholder-slate-400 focus:ring-2 focus:ring-sky-500/30 focus:border-sky-500 transition-shadow resize-y min-h-[80px]"
                          placeholder="{{ __('instructor.placeholder_extra_transfer') }}">{{ old('notes', $detail->notes) }}</textarea>
                @error('notes')<p class="text-red-600 text-xs mt-1.5">{{ $message }}</p>@enderror
            </div>
            <div class="flex flex-wrap gap-3 pt-2 border-t border-slate-100">
                <button type="submit" class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl bg-sky-600 hover:bg-sky-700 text-white font-semibold text-sm shadow-sm border border-sky-700/20 transition-colors">
                    <i class="fas fa-save text-sm"></i>
                    {{ __('instructor.save_transfer_data') }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
