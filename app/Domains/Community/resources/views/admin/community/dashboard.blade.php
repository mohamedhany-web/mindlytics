@extends('layouts.admin')

@section('title', __('admin.community_dashboard'))
@section('header', __('admin.community_dashboard'))

@section('content')
<div class="w-full space-y-6">
    {{-- بطاقة ترحيب / عنوان القسم --}}
    <div class="bg-gradient-to-br from-cyan-600 via-blue-600 to-slate-800 rounded-3xl border border-cyan-500/20 p-6 sm:p-8 shadow-xl text-white overflow-hidden relative">
        <div class="absolute top-0 left-0 w-40 h-40 bg-white/10 rounded-full -translate-x-1/2 -translate-y-1/2"></div>
        <div class="absolute bottom-0 right-0 w-32 h-32 bg-white/5 rounded-full translate-x-1/3 translate-y-1/3"></div>
        <div class="relative z-10">
            <p class="text-cyan-200 text-sm font-bold mb-1">الإدارة العليا — مجتمع البيانات والذكاء الاصطناعي</p>
            <h1 class="text-2xl sm:text-3xl font-black mb-2">{{ __('admin.community_dashboard') }}</h1>
            <p class="text-white/90 text-sm sm:text-base max-w-xl">إدارة المسابقات، مجموعات البيانات، التقديمات، المناقشات وإعدادات المجتمع.</p>
        </div>
    </div>

    {{-- كروت الإحصائيات — تعبّر عن المجتمع --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <a href="{{ route('admin.community.competitions.index') }}" class="group block bg-white rounded-2xl border border-slate-200 p-5 shadow-sm hover:shadow-lg hover:border-cyan-200 transition-all duration-300">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-slate-500 text-sm font-semibold mb-1">{{ __('admin.community_competitions') }}</p>
                    <p class="text-3xl font-black text-cyan-600">{{ $stats['competitions_count'] ?? 0 }}</p>
                    <p class="text-xs text-slate-400 mt-1">{{ $stats['competitions_active'] ?? 0 }} نشط</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-cyan-100 text-cyan-600 flex items-center justify-center group-hover:bg-cyan-500 group-hover:text-white transition-colors">
                    <i class="fas fa-trophy text-xl"></i>
                </div>
            </div>
        </a>
        <a href="{{ route('admin.community.datasets.index') }}" class="group block bg-white rounded-2xl border border-slate-200 p-5 shadow-sm hover:shadow-lg hover:border-blue-200 transition-all duration-300">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-slate-500 text-sm font-semibold mb-1">{{ __('admin.community_datasets') }}</p>
                    <p class="text-3xl font-black text-blue-600">{{ $stats['datasets_count'] ?? 0 }}</p>
                    <p class="text-xs text-slate-400 mt-1">{{ $stats['datasets_active'] ?? 0 }} نشط</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center group-hover:bg-blue-500 group-hover:text-white transition-colors">
                    <i class="fas fa-database text-xl"></i>
                </div>
            </div>
        </a>
        <a href="{{ route('admin.community.submissions.index') }}" class="group block bg-white rounded-2xl border border-slate-200 p-5 shadow-sm hover:shadow-lg hover:border-green-200 transition-all duration-300">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-slate-500 text-sm font-semibold mb-1">تقديمات البيانات</p>
                    <p class="text-3xl font-black text-green-600">{{ $stats['pending_submissions'] ?? 0 }}</p>
                    <p class="text-xs text-slate-400 mt-1">معلقة</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-green-100 text-green-600 flex items-center justify-center group-hover:bg-green-500 group-hover:text-white transition-colors">
                    <i class="fas fa-paper-plane text-xl"></i>
                </div>
            </div>
        </a>
        <a href="{{ route('admin.community.submissions.models.index') }}" class="group block bg-white rounded-2xl border border-slate-200 p-5 shadow-sm hover:shadow-lg hover:border-amber-200 transition-all duration-300">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-slate-500 text-sm font-semibold mb-1">{{ __('admin.community_models') }}</p>
                    <p class="text-3xl font-black text-amber-600">{{ $stats['pending_models'] ?? 0 }}</p>
                    <p class="text-xs text-slate-400 mt-1">تقديمات معلقة</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center group-hover:bg-amber-500 group-hover:text-white transition-colors">
                    <i class="fas fa-brain text-xl"></i>
                </div>
            </div>
        </a>
        <a href="{{ route('admin.community.discussions.index') }}" class="group block bg-white rounded-2xl border border-slate-200 p-5 shadow-sm hover:shadow-lg hover:border-amber-200 transition-all duration-300">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-slate-500 text-sm font-semibold mb-1">{{ __('admin.community_discussions') }}</p>
                    <p class="text-3xl font-black text-amber-600">0</p>
                    <p class="text-xs text-slate-400 mt-1">قريباً</p>
                </div>
                <div class="w-12 h-12 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center group-hover:bg-amber-500 group-hover:text-white transition-colors">
                    <i class="fas fa-comments text-xl"></i>
                </div>
            </div>
        </a>
    </div>

    {{-- إدارة سريعة — كارد إرشادي --}}
    <div class="bg-white rounded-2xl border border-slate-200 p-6 sm:p-8 shadow-sm">
        <h2 class="text-lg font-black text-slate-900 mb-2 flex items-center gap-2">
            <span class="w-8 h-8 rounded-lg bg-cyan-100 text-cyan-600 flex items-center justify-center"><i class="fas fa-cogs"></i></span>
            إدارة سريعة
        </h2>
        <p class="text-slate-600 mb-6">إضافة أو تعديل المسابقات ومجموعات البيانات، أو الدخول إلى التقديمات والمناقشات والإعدادات.</p>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('admin.community.competitions.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-cyan-600 text-white font-bold hover:bg-cyan-700 transition-colors shadow-md hover:shadow-lg">
                <i class="fas fa-plus"></i>
                <span>إضافة مسابقة</span>
            </a>
            <a href="{{ route('admin.community.competitions.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-cyan-100 text-cyan-800 font-bold hover:bg-cyan-200 transition-colors">
                <i class="fas fa-trophy"></i>
                <span>المسابقات</span>
            </a>
            <a href="{{ route('admin.community.datasets.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-blue-600 text-white font-bold hover:bg-blue-700 transition-colors shadow-md hover:shadow-lg">
                <i class="fas fa-plus"></i>
                <span>إضافة مجموعة بيانات</span>
            </a>
            <a href="{{ route('admin.community.datasets.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-blue-100 text-blue-800 font-bold hover:bg-blue-200 transition-colors">
                <i class="fas fa-database"></i>
                <span>مجموعات البيانات</span>
            </a>
            <a href="{{ route('admin.community.submissions.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-slate-100 text-slate-700 font-bold hover:bg-slate-200 transition-colors">
                <i class="fas fa-paper-plane"></i>
                <span>تقديمات البيانات</span>
            </a>
            <a href="{{ route('admin.community.submissions.models.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-amber-100 text-amber-800 font-bold hover:bg-amber-200 transition-colors">
                <i class="fas fa-brain"></i>
                <span>تقديمات النماذج</span>
            </a>
            <a href="{{ route('admin.community.discussions.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-slate-100 text-slate-700 font-bold hover:bg-slate-200 transition-colors">
                <i class="fas fa-comments"></i>
                <span>{{ __('admin.community_discussions') }}</span>
            </a>
            <a href="{{ route('admin.community.settings.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl border-2 border-slate-200 text-slate-700 font-bold hover:border-slate-300 hover:bg-slate-50 transition-colors">
                <i class="fas fa-cog"></i>
                <span>{{ __('admin.community_settings') }}</span>
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- كارد آخر المسابقات --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-4 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
                <h2 class="text-lg font-black text-slate-900 flex items-center gap-2">
                    <i class="fas fa-trophy text-cyan-600"></i>
                    آخر المسابقات
                </h2>
                <a href="{{ route('admin.community.competitions.index') }}" class="text-sm font-bold text-cyan-600 hover:underline">عرض الكل</a>
            </div>
            <div class="p-4">
                @if($recentCompetitions->isNotEmpty())
                    <ul class="space-y-3">
                        @foreach($recentCompetitions as $c)
                            <li class="flex items-center gap-3 p-3 rounded-xl bg-slate-50 hover:bg-cyan-50/50 border border-transparent hover:border-cyan-100 transition-colors">
                                <div class="w-10 h-10 rounded-lg bg-cyan-100 text-cyan-600 flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-trophy"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="font-bold text-slate-900 truncate">{{ $c->title }}</p>
                                    <p class="text-xs text-slate-500">
                                        @if($c->start_at) {{ $c->start_at->translatedFormat('Y-m-d') }} @endif
                                        @if($c->end_at) — {{ $c->end_at->translatedFormat('Y-m-d') }} @endif
                                        @if($c->is_active)
                                            <span class="text-green-600 font-semibold">· نشط</span>
                                        @else
                                            <span class="text-slate-400">· معطّل</span>
                                        @endif
                                    </p>
                                </div>
                                <a href="{{ route('admin.community.competitions.edit', $c) }}" class="text-cyan-600 hover:text-cyan-700 text-sm font-semibold flex-shrink-0">تعديل</a>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <div class="text-center py-8 text-slate-500">
                        <div class="w-14 h-14 rounded-2xl bg-slate-100 flex items-center justify-center mx-auto mb-3">
                            <i class="fas fa-trophy text-2xl text-slate-400"></i>
                        </div>
                        <p class="text-sm font-semibold">لا توجد مسابقات</p>
                        <a href="{{ route('admin.community.competitions.create') }}" class="inline-block mt-2 text-cyan-600 font-bold hover:underline">إضافة مسابقة</a>
                    </div>
                @endif
            </div>
        </div>

        {{-- كارد آخر مجموعات البيانات --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-4 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
                <h2 class="text-lg font-black text-slate-900 flex items-center gap-2">
                    <i class="fas fa-database text-blue-600"></i>
                    آخر مجموعات البيانات
                </h2>
                <a href="{{ route('admin.community.datasets.index') }}" class="text-sm font-bold text-blue-600 hover:underline">عرض الكل</a>
            </div>
            <div class="p-4">
                @if($recentDatasets->isNotEmpty())
                    <ul class="space-y-3">
                        @foreach($recentDatasets as $d)
                            <li class="flex items-center gap-3 p-3 rounded-xl bg-slate-50 hover:bg-blue-50/50 border border-transparent hover:border-blue-100 transition-colors">
                                <div class="w-10 h-10 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-database"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="font-bold text-slate-900 truncate">{{ $d->title }}</p>
                                    <p class="text-xs text-slate-500">{{ $d->file_size ?? '—' }} @if($d->is_active) <span class="text-green-600 font-semibold">· نشط</span> @else <span class="text-slate-400">· معطّل</span> @endif</p>
                                </div>
                                <a href="{{ route('admin.community.datasets.edit', $d) }}" class="text-blue-600 hover:text-blue-700 text-sm font-semibold flex-shrink-0">تعديل</a>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <div class="text-center py-8 text-slate-500">
                        <div class="w-14 h-14 rounded-2xl bg-slate-100 flex items-center justify-center mx-auto mb-3">
                            <i class="fas fa-database text-2xl text-slate-400"></i>
                        </div>
                        <p class="text-sm font-semibold">لا توجد مجموعات بيانات</p>
                        <a href="{{ route('admin.community.datasets.create') }}" class="inline-block mt-2 text-blue-600 font-bold hover:underline">إضافة مجموعة بيانات</a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- كروت التقديمات والمناقشات والإعدادات --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
        <a href="{{ route('admin.community.submissions.index') }}" class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm hover:shadow-md hover:border-green-200 transition-all group">
            <h2 class="text-lg font-black text-slate-900 mb-3 flex items-center gap-2">
                <i class="fas fa-paper-plane text-green-500 group-hover:text-green-600"></i>
                {{ __('admin.community_submissions') }}
            </h2>
            <p class="text-slate-600 text-sm mb-4">إدارة تقديمات المشاركين في المسابقات. القسم قيد الإعداد.</p>
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-green-100 text-green-700 text-sm font-semibold">قريباً</span>
        </a>
        <a href="{{ route('admin.community.discussions.index') }}" class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm hover:shadow-md hover:border-amber-200 transition-all group">
            <h2 class="text-lg font-black text-slate-900 mb-3 flex items-center gap-2">
                <i class="fas fa-comments text-amber-500 group-hover:text-amber-600"></i>
                {{ __('admin.community_discussions') }}
            </h2>
            <p class="text-slate-600 text-sm mb-4">إدارة المناقشات والمواضيع. القسم قيد الإعداد.</p>
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-amber-100 text-amber-700 text-sm font-semibold">قريباً</span>
        </a>
        <a href="{{ route('admin.community.settings.index') }}" class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm hover:shadow-md hover:border-slate-300 transition-all group">
            <h2 class="text-lg font-black text-slate-900 mb-3 flex items-center gap-2">
                <i class="fas fa-cog text-slate-500 group-hover:text-slate-600"></i>
                {{ __('admin.community_settings') }}
            </h2>
            <p class="text-slate-600 text-sm mb-4">إعدادات عامة لمجتمع البيانات. القسم قيد الإعداد.</p>
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-slate-100 text-slate-600 text-sm font-semibold">قريباً</span>
        </a>
    </div>

    {{-- كارد توضيحي --}}
    <div class="bg-gradient-to-r from-slate-100 to-slate-50 rounded-2xl border border-slate-200 p-6 text-center">
        <p class="text-slate-600 text-sm">لوحة تحكم مجتمع البيانات والذكاء الاصطناعي — للإدارة العليا. المسابقات ومجموعات البيانات قابلة للإدارة من الروابط أعلاه.</p>
    </div>
</div>
@endsection
