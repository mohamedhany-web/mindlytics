@extends('layouts.admin')

@section('title', 'صفحات Facebook & Instagram')
@section('header', 'إدارة الصفحات')

@section('content')
<div class="p-3 sm:p-4 md:p-6 space-y-4 sm:space-y-6" style="background: #f8fafc; min-height: 100vh;">
    @include('admin.meta-social._alerts')

    @include('admin.meta-social._page-header', [
        'title' => 'صفحات Meta المربوطة',
        'subtitle' => 'Facebook Page + Instagram Business المرتبط',
        'icon' => 'fab fa-facebook',
        'actions' => '
            <form method="post" action="' . route('admin.meta-social.pages.sync') . '" class="inline">' . csrf_field() . '
                <button type="submit" class="' . $smBtnPrimary . '"><i class="fas fa-sync"></i> مزامنة من Meta</button>
            </form>
            <a href="' . route('admin.meta-social.oauth.redirect') . '" class="' . $smBtnMeta . '"><i class="fab fa-facebook"></i> إعادة الربط</a>
        ',
    ])

    <section class="{{ $smSectionClass }} overflow-x-auto">
        <div class="px-5 py-4 border-b border-slate-200 bg-gradient-to-r from-slate-50 to-white">
            <h3 class="font-bold text-slate-900">قائمة الصفحات</h3>
        </div>
        <table class="w-full text-sm">
            <thead class="bg-slate-50 border-b border-slate-200">
                <tr>
                    <th class="text-right p-3 font-bold text-slate-600">الصفحة</th>
                    <th class="text-right p-3 font-bold text-slate-600">Instagram</th>
                    <th class="text-right p-3 font-bold text-slate-600">الحالة</th>
                    <th class="text-right p-3 font-bold text-slate-600">Webhook</th>
                    <th class="text-right p-3 font-bold text-slate-600">إجراءات</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($pages as $page)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="p-3">
                            <div class="flex items-center gap-3">
                                @if($page->picture_url)
                                    <img src="{{ $page->picture_url }}" class="w-10 h-10 rounded-full object-cover border border-slate-200" alt="">
                                @else
                                    <div class="w-10 h-10 rounded-full bg-sky-100 flex items-center justify-center"><i class="fab fa-facebook text-sky-500"></i></div>
                                @endif
                                <div>
                                    <p class="font-bold text-slate-900">{{ $page->page_name }}</p>
                                    <p class="text-xs text-slate-500">{{ $page->category ?? '—' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="p-3">
                            @if($page->instagram_username)
                                <span class="inline-flex items-center gap-1 text-pink-700 font-semibold"><i class="fab fa-instagram text-xs"></i> @{{ $page->instagram_username }}</span>
                            @else
                                <span class="text-slate-400">—</span>
                            @endif
                        </td>
                        <td class="p-3">
                            @if($page->is_active)
                                <span class="text-xs px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-800 font-bold">نشطة</span>
                            @else
                                <span class="text-xs px-2.5 py-1 rounded-full bg-slate-100 text-slate-600 font-bold">موقوفة</span>
                            @endif
                        </td>
                        <td class="p-3 text-xs text-slate-500">
                            {{ $page->webhook_subscribed_at?->diffForHumans() ?? '—' }}
                        </td>
                        <td class="p-3">
                            <div class="flex flex-wrap gap-1.5">
                                @if($page->is_active)
                                    <form method="post" action="{{ route('admin.meta-social.pages.deactivate', $page) }}">@csrf
                                        <button type="submit" class="text-xs px-2.5 py-1.5 rounded-lg border-2 border-slate-200 hover:bg-slate-50 font-semibold">إيقاف</button>
                                    </form>
                                @else
                                    <form method="post" action="{{ route('admin.meta-social.pages.activate', $page) }}">@csrf
                                        <button type="submit" class="text-xs px-2.5 py-1.5 rounded-lg bg-emerald-600 text-white font-semibold">تفعيل</button>
                                    </form>
                                @endif
                                <form method="post" action="{{ route('admin.meta-social.pages.sync-conversations', $page) }}">@csrf
                                    <button type="submit" class="text-xs px-2.5 py-1.5 rounded-lg border-2 border-sky-200 text-sky-700 hover:bg-sky-50 font-semibold">مزامنة محادثات</button>
                                </form>
                                <a href="{{ route('admin.meta-social.inbox.index', ['page' => $page->id]) }}" class="text-xs px-2.5 py-1.5 rounded-lg bg-sky-600 text-white inline-flex items-center font-semibold">Inbox</a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="p-10 text-center">
                            <div class="w-14 h-14 rounded-2xl bg-slate-100 flex items-center justify-center mx-auto mb-3">
                                <i class="fab fa-facebook text-xl text-slate-400"></i>
                            </div>
                            <p class="text-sm text-slate-500">لا توجد صفحات — اربط Meta ثم اضغط «مزامنة من Meta»</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        @if($pages->hasPages())
            <div class="px-5 py-4 border-t border-slate-100">{{ $pages->links() }}</div>
        @endif
    </section>
</div>
@endsection
