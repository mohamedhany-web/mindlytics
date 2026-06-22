@extends('layouts.admin')

@section('title', 'سجل رسائل الواتساب - Mindlytics')
@section('header', 'قسم الواتساب')

@section('content')
<div class="space-y-6">
    @include('admin.whatsapp._nav', ['active' => 'messages'])

    @if(session('success'))
        <div class="rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 text-sm">{{ session('success') }}</div>
    @endif

    <section class="rounded-3xl bg-white border border-slate-200 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <h3 class="text-lg font-bold text-slate-900">سجل الرسائل</h3>
            <form method="GET" class="flex flex-wrap gap-2">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="بحث..."
                       class="rounded-xl border-slate-300 text-sm">
                <select name="status" class="rounded-xl border-slate-300 text-sm">
                    <option value="">كل الحالات</option>
                    @foreach(['sent' => 'مرسلة', 'failed' => 'فاشلة', 'pending' => 'انتظار'] as $val => $label)
                        <option value="{{ $val }}" @selected(request('status') === $val)>{{ $label }}</option>
                    @endforeach
                </select>
                <button class="px-3 py-2 rounded-xl bg-slate-800 text-white text-sm">تصفية</button>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 text-slate-600">
                    <tr>
                        <th class="px-4 py-3 text-right">التاريخ</th>
                        <th class="px-4 py-3 text-right">الرقم</th>
                        <th class="px-4 py-3 text-right">الرسالة</th>
                        <th class="px-4 py-3 text-right">الحالة</th>
                        <th class="px-4 py-3 text-right">المرسل</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($messages as $msg)
                        <tr class="hover:bg-slate-50/80">
                            <td class="px-4 py-3 whitespace-nowrap text-slate-600">{{ $msg->created_at?->format('Y-m-d H:i') }}</td>
                            <td class="px-4 py-3 font-mono">{{ $msg->phone_number }}</td>
                            <td class="px-4 py-3 max-w-md truncate" title="{{ $msg->message }}">{{ Str::limit($msg->message, 80) }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold
                                    @if($msg->status === 'sent') bg-emerald-100 text-emerald-800
                                    @elseif($msg->status === 'failed') bg-rose-100 text-rose-800
                                    @else bg-amber-100 text-amber-800 @endif">
                                    {{ $msg->status_text }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-slate-600">{{ $msg->user?->name ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-10 text-center text-slate-500">لا توجد رسائل بعد.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($messages->hasPages())
            <div class="px-6 py-4 border-t border-slate-200">{{ $messages->links() }}</div>
        @endif
    </section>
</div>
@endsection
