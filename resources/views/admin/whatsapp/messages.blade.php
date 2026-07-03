@extends('layouts.admin')

@section('title', 'سجل رسائل الواتساب - Mindlytics')
@section('header', 'قسم الواتساب')

@section('content')
<div class="p-3 sm:p-4 md:p-6 space-y-4 sm:space-y-6" style="background: #f8fafc; min-height: 100vh;">
    @include('admin.whatsapp._alerts')

    @include('admin.whatsapp._page-header', [
        'title' => 'سجل رسائل الواتساب',
        'subtitle' => 'جميع الرسائل المرسلة عبر Meta Cloud API مع حالة التسليم.',
        'icon' => 'fas fa-list',
        'actions' => '<a href="' . route('admin.whatsapp.send') . '" class="' . $waBtnPrimary . '"><i class="fas fa-plus"></i> رسالة جديدة</a>',
    ])

    <section class="{{ $waSectionClass }}">
        <div class="px-5 py-4 border-b border-slate-200">
            <h3 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                <i class="fas fa-filter text-emerald-600"></i>
                بحث وتصفية
            </h3>
        </div>
        <div class="p-5">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="md:col-span-2">
                    <label class="{{ $waLabelClass }}">بحث</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-3 flex items-center text-slate-400"><i class="fas fa-search"></i></span>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="رقم أو نص الرسالة..."
                               class="{{ $waInputClass }} pl-10">
                    </div>
                </div>
                <div>
                    <label class="{{ $waLabelClass }}">الحالة</label>
                    <select name="status" class="{{ $waSelectClass }}">
                        <option value="">كل الحالات</option>
                        @foreach(['sent' => 'مقبولة من Meta', 'delivered' => 'تم التسليم', 'read' => 'تم القراءة', 'failed' => 'فاشلة', 'pending' => 'في الانتظار'] as $val => $label)
                            <option value="{{ $val }}" @selected(request('status') === $val)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="md:col-span-3 flex flex-wrap gap-2">
                    <button type="submit" class="{{ $waBtnDark }}"><i class="fas fa-search"></i> تصفية</button>
                    <a href="{{ route('admin.whatsapp.messages') }}" class="{{ $waBtnSecondary }}">إعادة تعيين</a>
                </div>
            </form>
        </div>
    </section>

    <section class="{{ $waSectionClass }} overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-200 flex items-center justify-between gap-3">
            <h3 class="text-lg font-bold text-slate-900">الرسائل</h3>
            <span class="text-xs font-semibold text-slate-500">{{ $messages->total() }} رسالة</span>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 text-slate-600 border-b border-slate-200">
                    <tr>
                        <th class="px-5 py-3 text-right font-semibold">التاريخ</th>
                        <th class="px-5 py-3 text-right font-semibold">الرقم</th>
                        <th class="px-5 py-3 text-right font-semibold">الرسالة</th>
                        <th class="px-5 py-3 text-right font-semibold">الحالة</th>
                        <th class="px-5 py-3 text-right font-semibold">المرسل</th>
                        <th class="px-5 py-3 text-right font-semibold">إجراء</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($messages as $msg)
                        <tr class="hover:bg-emerald-50/30 transition-colors">
                            <td class="px-5 py-3.5 whitespace-nowrap text-slate-600 tabular-nums">{{ $msg->created_at?->format('Y-m-d H:i') }}</td>
                            <td class="px-5 py-3.5 font-mono text-slate-800 dir-ltr text-right">+{{ $msg->phone_number }}</td>
                            <td class="px-5 py-3.5 max-w-xs sm:max-w-md">
                                <p class="truncate text-slate-700" title="{{ $msg->message }}">{{ Str::limit($msg->message, 80) }}</p>
                            </td>
                            <td class="px-5 py-3.5">
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold border
                                    @if(in_array($msg->status, ['sent', 'delivered', 'read'], true)) bg-emerald-100 text-emerald-800 border-emerald-200
                                    @elseif($msg->status === 'failed') bg-rose-100 text-rose-800 border-rose-200
                                    @else bg-amber-100 text-amber-800 border-amber-200 @endif">
                                    @if(in_array($msg->status, ['sent', 'delivered', 'read'], true))<i class="fas fa-check"></i>
                                    @elseif($msg->status === 'failed')<i class="fas fa-times"></i>
                                    @else<i class="fas fa-clock"></i>@endif
                                    {{ $msg->status_text }}
                                </span>
                                @if($msg->status === 'sent' && $msg->whatsapp_message_id)
                                    <p class="text-[10px] text-slate-500 mt-1">قُبلت من Meta — انتظر التسليم أو راجع Webhook</p>
                                @endif
                                @if($msg->status === 'failed' && $msg->error_message)
                                    <p class="text-[10px] text-rose-600 mt-1 max-w-xs truncate" title="{{ $msg->error_message }}">{{ Str::limit($msg->error_message, 50) }}</p>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 text-slate-600">{{ $msg->user?->name ?? '—' }}</td>
                            <td class="px-5 py-3.5">
                                @if($msg->status === 'failed')
                                    <form method="POST" action="{{ route('admin.whatsapp.messages.resend', $msg) }}" class="inline">
                                        @csrf
                                        <button type="submit" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-[11px] font-semibold bg-rose-600 hover:bg-rose-700 text-white">
                                            <i class="fas fa-redo text-[10px]"></i>
                                            إعادة الإرسال
                                        </button>
                                    </form>
                                @else
                                    <span class="text-slate-300">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-16 text-center">
                                <div class="flex flex-col items-center text-slate-400">
                                    <i class="fas fa-inbox text-4xl mb-3"></i>
                                    <p class="font-semibold text-slate-600">لا توجد رسائل بعد</p>
                                    <a href="{{ route('admin.whatsapp.send') }}" class="{{ $waBtnPrimary }} mt-4 text-sm">إرسال أول رسالة</a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($messages->hasPages())
            <div class="px-5 py-4 border-t border-slate-200 bg-slate-50/50">{{ $messages->links() }}</div>
        @endif
    </section>
</div>
@endsection
