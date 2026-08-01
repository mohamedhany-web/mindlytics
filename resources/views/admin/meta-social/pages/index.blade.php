@extends('layouts.admin')

@section('title', 'صفحات Facebook & Instagram')
@section('header', 'إدارة الصفحات')

@section('content')
<div class="p-3 sm:p-4 md:p-6 space-y-4 sm:space-y-6" style="background: #f8fafc; min-height: 100vh;">
    @include('admin.meta-social._alerts')

    @include('admin.meta-social._page-header', [
        'title' => 'صفحات Meta — متعددة',
        'subtitle' => 'اربط أكثر من حساب Meta وفعّل أي عدد من الصفحات (Facebook + Instagram)',
        'icon' => 'fab fa-facebook',
        'statCards' => [
            ['label' => 'حسابات Meta', 'value' => $connections->count(), 'icon' => 'fab fa-meta', 'bg' => 'bg-violet-100', 'text' => 'text-violet-600'],
            ['label' => 'صفحات مزامنة', 'value' => $pages->total(), 'icon' => 'fab fa-facebook', 'bg' => 'bg-sky-100', 'text' => 'text-sky-600'],
            ['label' => 'صفحات نشطة', 'value' => \App\Models\MetaSocialPage::where('is_active', true)->count(), 'icon' => 'fas fa-check-circle', 'bg' => 'bg-emerald-100', 'text' => 'text-emerald-600'],
            ['label' => 'Inbox', 'value' => 'موحّد', 'icon' => 'fas fa-inbox', 'bg' => 'bg-slate-100', 'text' => 'text-slate-600', 'description' => 'كل الصفحات النشطة'],
        ],
        'actions' => '
            <a href="' . route('admin.meta-social.oauth.redirect') . '" class="' . $smBtnMeta . '"><i class="fas fa-plus"></i> ربط حساب Meta</a>
            <form method="post" action="' . route('admin.meta-social.pages.sync') . '" class="inline">' . csrf_field() . '
                <button type="submit" class="' . $smBtnPrimary . '"><i class="fas fa-sync"></i> مزامنة الكل</button>
            </form>
        ',
    ])

    @if($showPicker && $pages->isNotEmpty())
        <div class="rounded-2xl border-2 border-sky-300 bg-sky-50 p-4 sm:p-5 flex flex-wrap items-center justify-between gap-3">
            <div>
                <p class="font-bold text-sky-900">اختر الصفحات للتفعيل</p>
                <p class="text-sm text-sky-800 mt-0.5">يمكنك تفعيل أكثر من صفحة — Messenger و Instagram لكل صفحة.</p>
            </div>
            <form method="post" action="{{ route('admin.meta-social.pages.activate-all') }}" class="inline">@csrf
                <button type="submit" class="{{ $smBtnPrimary }}"><i class="fas fa-check-double"></i> تفعيل الكل</button>
            </form>
        </div>
    @endif

    @if($connections->isNotEmpty())
    <section class="{{ $smSectionClass }}">
        <div class="px-5 py-4 border-b border-slate-200 bg-gradient-to-r from-slate-50 to-white">
            <h3 class="font-bold text-slate-900">حسابات Meta المربوطة</h3>
        </div>
        <div class="p-4 sm:p-5 flex flex-wrap gap-3">
            @foreach($connections as $conn)
                <div class="rounded-xl border border-slate-200 bg-white px-4 py-3 flex items-center gap-3 min-w-[220px]">
                    <div class="w-10 h-10 rounded-full bg-violet-100 flex items-center justify-center text-violet-600">
                        <i class="fab fa-meta"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-bold text-sm text-slate-900 truncate">{{ $conn->meta_user_name ?: 'Meta User' }}</p>
                        <p class="text-[10px] text-slate-500">{{ $conn->connected_at?->diffForHumans() }}</p>
                    </div>
                    <form method="post" action="{{ route('admin.meta-social.oauth.disconnect') }}" onsubmit="return confirm('قطع هذا الحساب؟')">@csrf
                        <input type="hidden" name="connection_id" value="{{ $conn->id }}">
                        <button type="submit" class="text-xs text-rose-600 hover:text-rose-800 font-bold">قطع</button>
                    </form>
                </div>
            @endforeach
            <a href="{{ route('admin.meta-social.oauth.redirect') }}" class="rounded-xl border-2 border-dashed border-sky-300 bg-sky-50/50 px-4 py-3 flex items-center gap-2 text-sky-700 hover:bg-sky-50 font-semibold text-sm">
                <i class="fas fa-plus"></i> حساب Meta إضافي
            </a>
        </div>
    </section>
    @endif

    <section class="{{ $smSectionClass }} overflow-x-auto" x-data="{ selected: [] }">
        <div class="px-5 py-4 border-b border-slate-200 bg-gradient-to-r from-slate-50 to-white flex flex-wrap items-center justify-between gap-3">
            <h3 class="font-bold text-slate-900">قائمة الصفحات</h3>
            <div class="flex flex-wrap gap-2" x-show="selected.length > 0" x-cloak>
                <form method="post" action="{{ route('admin.meta-social.pages.bulk-activate') }}">@csrf
                    <template x-for="id in selected" :key="'a-'+id">
                        <input type="hidden" name="page_ids[]" :value="id">
                    </template>
                    <button type="submit" class="text-xs px-3 py-1.5 rounded-lg bg-emerald-600 text-white font-bold">تفعيل المحدد (<span x-text="selected.length"></span>)</button>
                </form>
                <form method="post" action="{{ route('admin.meta-social.pages.bulk-deactivate') }}">@csrf
                    <template x-for="id in selected" :key="'d-'+id">
                        <input type="hidden" name="page_ids[]" :value="id">
                    </template>
                    <button type="submit" class="text-xs px-3 py-1.5 rounded-lg border-2 border-slate-200 font-bold">إيقاف المحدد</button>
                </form>
            </div>
        </div>
        <table class="w-full text-sm">
            <thead class="bg-slate-50 border-b border-slate-200">
                <tr>
                    <th class="p-3 w-10">
                        <input type="checkbox" class="rounded border-slate-300"
                               @change="selected = $event.target.checked ? @json($pages->pluck('id')) : []">
                    </th>
                    <th class="text-right p-3 font-bold text-slate-600">الصفحة</th>
                    <th class="text-right p-3 font-bold text-slate-600">حساب Meta</th>
                    <th class="text-right p-3 font-bold text-slate-600">Instagram</th>
                    <th class="text-right p-3 font-bold text-slate-600">الحالة</th>
                    <th class="text-right p-3 font-bold text-slate-600">إجراءات</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($pages as $page)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="p-3">
                            <input type="checkbox" class="rounded border-slate-300" value="{{ $page->id }}"
                                   @change="if($event.target.checked) { if(!selected.includes({{ $page->id }})) selected.push({{ $page->id }}) } else { selected = selected.filter(id => id !== {{ $page->id }}) }"
                                   :checked="selected.includes({{ $page->id }})">
                        </td>
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
                        <td class="p-3 text-xs text-slate-600">{{ $page->connection?->meta_user_name ?? '—' }}</td>
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
                                <span class="text-xs px-2.5 py-1 rounded-full bg-slate-100 text-slate-600 font-bold">غير مفعّلة</span>
                            @endif
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
                                @if($page->is_active)
                                    <form method="post" action="{{ route('admin.meta-social.pages.sync-conversations', $page) }}"
                                          onsubmit="return confirm('سيتم جلب كل المحادثات وكل الرسائل من Meta — قد يستغرق وقتاً. متابعة؟')">
                                        @csrf
                                        <button type="submit" class="text-xs px-2.5 py-1.5 rounded-lg border-2 border-sky-200 text-sky-800 bg-sky-50 hover:bg-sky-100 font-semibold inline-flex items-center gap-1">
                                            <i class="fas fa-cloud-download-alt"></i> جلب كل الرسائل
                                        </button>
                                    </form>
                                @endif
                                <a href="{{ route('admin.meta-social.inbox.index', ['page' => $page->id]) }}" class="text-xs px-2.5 py-1.5 rounded-lg bg-sky-600 text-white inline-flex items-center font-semibold">Inbox</a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="p-10 text-center">
                            <div class="w-14 h-14 rounded-2xl bg-slate-100 flex items-center justify-center mx-auto mb-3">
                                <i class="fab fa-facebook text-xl text-slate-400"></i>
                            </div>
                            <p class="text-sm text-slate-500 mb-3">لا توجد صفحات — اربط Meta ثم مزامنة</p>
                            <a href="{{ route('admin.meta-social.oauth.redirect') }}" class="{{ $smBtnMeta }} text-sm inline-flex"><i class="fab fa-facebook"></i> ربط Meta</a>
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
