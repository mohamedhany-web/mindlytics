@extends('layouts.admin')

@section('title', 'تذكرة دعم')

@section('page_title')
    تذكرة #{{ $ticket->id }}
@endsection

@section('content')
    <div class="space-y-5">
        @if(session('success'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl p-4 font-bold">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white/90 border border-slate-200/60 rounded-2xl shadow-sm p-4 sm:p-5">
            <div class="flex items-start justify-between gap-3">
                <div class="min-w-0">
                    <div class="text-slate-900 font-black text-lg truncate">{{ $ticket->subject }}</div>
                    <div class="text-sm text-slate-600 mt-1">
                        {{ $ticket->user?->name ?? '—' }} · {{ $ticket->user?->email ?? '—' }} · {{ $ticket->role }}
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-xs px-2 py-1 rounded-full {{ $ticket->status === 'open' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">
                        {{ $ticket->status === 'open' ? 'مفتوحة' : 'مغلقة' }}
                    </span>
                    @if($ticket->status === 'open')
                        <form method="POST" action="{{ route('admin.support-tickets.close', $ticket) }}">
                            @csrf
                            <button class="px-3 py-2 rounded-xl bg-slate-900 text-white font-black hover:bg-slate-800">
                                إغلاق
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        <div class="bg-white/90 border border-slate-200/60 rounded-2xl shadow-sm overflow-hidden">
            <div class="p-4 sm:p-5 border-b border-slate-200/60">
                <div class="text-slate-800 font-black">المحادثة</div>
            </div>

            <div class="p-4 sm:p-5 space-y-3">
                @forelse($ticket->messages as $m)
                    @php
                        $isAdmin = $m->sender && ($m->sender->isAdmin() || $m->sender->isSuperAdmin());
                    @endphp
                    <div class="flex {{ $isAdmin ? 'justify-end' : 'justify-start' }}">
                        <div class="max-w-[780px] w-full sm:w-auto rounded-2xl border px-4 py-3 {{ $isAdmin ? 'bg-blue-50 border-blue-200' : 'bg-slate-50 border-slate-200' }}">
                            <div class="text-xs text-slate-500 mb-1 flex items-center justify-between gap-3">
                                <span class="font-bold">
                                    {{ $m->sender?->name ?? '—' }}
                                </span>
                                <span class="whitespace-nowrap">{{ $m->created_at->diffForHumans() }}</span>
                            </div>
                            <div class="text-slate-800 whitespace-pre-wrap leading-relaxed">{{ $m->body }}</div>
                        </div>
                    </div>
                @empty
                    <div class="text-slate-600">لا توجد رسائل.</div>
                @endforelse
            </div>

            @if($ticket->status === 'open')
                <div class="p-4 sm:p-5 border-t border-slate-200/60">
                    <form method="POST" action="{{ route('admin.support-tickets.reply', $ticket) }}" class="space-y-3">
                        @csrf
                        <textarea name="body" rows="4" class="w-full rounded-2xl border-slate-200 focus:border-blue-400 focus:ring-blue-400" placeholder="اكتب رد الإدارة..."></textarea>
                        @error('body')
                            <div class="text-sm text-red-600 font-bold">{{ $message }}</div>
                        @enderror
                        <div class="flex justify-end">
                            <button class="px-4 py-2 rounded-xl bg-blue-600 text-white font-black hover:bg-blue-700">
                                إرسال الرد
                            </button>
                        </div>
                    </form>
                </div>
            @endif
        </div>
    </div>
@endsection

