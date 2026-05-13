@extends('layouts.admin')

@section('title', $branch->name)
@section('header', $branch->name)

@section('content')
<div class="space-y-6 pb-16">
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-6 py-5 bg-slate-50 border-b border-slate-200 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-4 min-w-0">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white shadow-md shrink-0">
                    <i class="fas fa-code-branch text-lg"></i>
                </div>
                <div class="min-w-0">
                    <nav class="text-xs font-medium text-slate-500 flex flex-wrap items-center gap-2 mb-1">
                        <a href="{{ route('admin.dashboard') }}" class="text-blue-600 hover:text-blue-700">لوحة التحكم</a>
                        <span>/</span>
                        <a href="{{ route('admin.branches.index') }}" class="text-blue-600 hover:text-blue-700">الفروع</a>
                        <span>/</span>
                        <span class="text-slate-600 truncate">عرض الفرع</span>
                    </nav>
                    <h2 class="text-2xl font-black text-slate-900 mt-1 truncate">{{ $branch->name }}</h2>
                    <p class="text-sm text-slate-600 mt-1 font-mono truncate" dir="ltr">{{ $branch->slug }}</p>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-2 shrink-0">
                @if($branch->is_active)
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-emerald-100 text-emerald-700 border border-emerald-200">
                        <span class="h-2 w-2 rounded-full bg-current"></span>
                        نشط
                    </span>
                @else
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-slate-200 text-slate-700 border border-slate-300">
                        موقوف
                    </span>
                @endif
                <a href="{{ route('admin.branches.edit', $branch) }}" class="inline-flex items-center gap-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white px-4 py-2.5 text-sm font-semibold transition-colors">
                    <i class="fas fa-edit"></i>
                    تعديل
                </a>
                <a href="{{ route('admin.branches.index') }}" class="inline-flex items-center gap-2 rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition-colors">
                    <i class="fas fa-arrow-right"></i>
                    العودة للقائمة
                </a>
            </div>
        </div>
    </section>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
                <div class="px-6 py-5 border-b border-slate-200 bg-slate-50">
                    <h3 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                        <i class="fas fa-chart-pie text-blue-600"></i>
                        أعداد مرتبطة بالفرع
                    </h3>
                </div>
                <div class="p-6 grid grid-cols-2 sm:grid-cols-3 gap-4">
                    @php
                        $counts = [
                            ['label' => 'مستخدمون', 'value' => $branch->users_count ?? 0, 'icon' => 'fas fa-users'],
                            ['label' => 'طلبات', 'value' => $branch->orders_count ?? 0, 'icon' => 'fas fa-shopping-cart'],
                            ['label' => 'تسجيلات', 'value' => $branch->student_course_enrollments_count ?? 0, 'icon' => 'fas fa-user-check'],
                            ['label' => 'فواتير', 'value' => $branch->invoices_count ?? 0, 'icon' => 'fas fa-file-invoice'],
                            ['label' => 'مدفوعات', 'value' => $branch->payments_count ?? 0, 'icon' => 'fas fa-money-bill-wave'],
                            ['label' => 'محافظ', 'value' => $branch->wallets_count ?? 0, 'icon' => 'fas fa-wallet'],
                            ['label' => 'حركات محفظة', 'value' => $branch->wallet_transactions_count ?? 0, 'icon' => 'fas fa-exchange-alt'],
                            ['label' => 'معاملات محاسبة', 'value' => $branch->accounting_transactions_count ?? 0, 'icon' => 'fas fa-calculator'],
                        ];
                    @endphp
                    @foreach($counts as $row)
                        <div class="rounded-xl border border-slate-200 bg-slate-50/80 p-4 flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center text-blue-600 shrink-0">
                                <i class="{{ $row['icon'] }}"></i>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-slate-600">{{ $row['label'] }}</p>
                                <p class="text-xl font-black text-slate-900 tabular-nums">{{ number_format($row['value']) }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
                <div class="px-6 py-5 border-b border-slate-200 bg-slate-50">
                    <h3 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                        <i class="fas fa-link text-blue-600"></i>
                        الربط والنطاق
                    </h3>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <p class="text-xs font-semibold text-slate-600 mb-1">slug</p>
                        <p class="font-mono text-sm text-slate-900 break-all" dir="ltr">{{ $branch->slug }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-slate-600 mb-1">دومين مخصص</p>
                        <p class="font-mono text-sm text-slate-900 break-all" dir="ltr">{{ $branch->custom_domain ?: '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-slate-600 mb-1">رابط النطاق الفرعي المقترح</p>
                        @if($branch->suggestedSubdomainUrl())
                            <a href="{{ $branch->suggestedSubdomainUrl() }}" class="text-blue-600 hover:underline font-mono text-sm break-all" dir="ltr">{{ $branch->suggestedSubdomainUrl() }}</a>
                            <p class="text-xs text-slate-500 mt-2">يتطلب تطابق <code class="bg-slate-100 px-1 rounded">APP_URL</code> مع المضيف الأساسي وإعداد DNS عند الإنتاج.</p>
                        @else
                            <span class="text-slate-400 text-sm">غير متاح — تحقق من <code class="bg-slate-100 px-1 rounded">APP_URL</code></span>
                        @endif
                    </div>
                    <div class="pt-4 border-t border-slate-200 grid sm:grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs font-semibold text-slate-600 mb-1">دولة / عملة / منطقة زمنية</p>
                            <p class="text-slate-900 text-sm">
                                {{ $branch->country_code ?? '—' }}
                                <span class="text-slate-300 mx-1">·</span>
                                <span class="font-mono">{{ $branch->currency ? strtoupper($branch->currency) : '—' }}</span>
                                <span class="text-slate-300 mx-1">·</span>
                                <span class="font-mono text-xs" dir="ltr">{{ $branch->timezone ?? '—' }}</span>
                            </p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-slate-600 mb-1">لون العلامة / شعار</p>
                            <div class="flex items-center gap-3 flex-wrap">
                                @if($branch->primary_color)
                                    <span class="inline-flex h-9 w-9 rounded-lg border border-slate-200 shadow-inner shrink-0" style="background: {{ $branch->primary_color }}"></span>
                                    <span class="font-mono text-sm">{{ $branch->primary_color }}</span>
                                @else
                                    <span class="text-slate-400">—</span>
                                @endif
                                @if($branch->logo_path)
                                    <span class="text-xs text-slate-600 break-all">{{ $branch->logo_path }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    @if($branch->internal_notes)
                        <div class="pt-4 border-t border-slate-200">
                            <p class="text-xs font-semibold text-slate-600 mb-2">ملاحظات داخلية</p>
                            <p class="text-slate-800 text-sm whitespace-pre-wrap">{{ $branch->internal_notes }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
                <div class="px-6 py-5 border-b border-slate-200 bg-slate-50">
                    <h3 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                        <i class="fas fa-info-circle text-blue-600"></i>
                        ملخص سريع
                    </h3>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <p class="text-xs font-semibold text-slate-600 mb-1">ترتيب العرض</p>
                        <p class="font-bold text-slate-900 text-lg tabular-nums">{{ $branch->sort_order }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-slate-600 mb-1">معرّف السجل</p>
                        <p class="font-bold text-slate-900">#{{ $branch->id }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if (session('generated_branch_manager_password'))
        <div class="rounded-2xl border-2 border-amber-300 bg-amber-50 p-6 text-amber-950 shadow-lg">
            <h3 class="font-black text-lg mb-2 flex items-center gap-2"><i class="fas fa-key text-amber-600"></i> بيانات الدخول (عرض لمرة واحدة)</h3>
            <p class="text-sm mb-4 text-amber-900/90">أرسل هذه البيانات لمدير الفرع عبر قناة آمنة. لن تُعرض كلمة المرور مرة أخرى بعد مغادرة الصفحة.</p>
            <div class="grid sm:grid-cols-2 gap-4 text-sm">
                <div class="bg-white/90 rounded-xl p-4 border border-amber-200">
                    <p class="text-xs font-semibold text-amber-800 mb-1">البريد</p>
                    <p class="font-mono break-all" dir="ltr">{{ session('generated_branch_manager_email') }}</p>
                </div>
                <div class="bg-white/90 rounded-xl p-4 border border-amber-200">
                    <p class="text-xs font-semibold text-amber-800 mb-1">كلمة المرور</p>
                    <p class="font-mono break-all select-all" dir="ltr">{{ session('generated_branch_manager_password') }}</p>
                </div>
            </div>
            <p class="text-xs mt-4 text-amber-900/80">
                تسجيل الدخول: <a class="underline font-semibold text-blue-700" href="{{ url('/login') }}" dir="ltr">{{ url('/login') }}</a>
                — لوحة الفرع: <a class="underline font-semibold text-blue-700" href="{{ url('/branch-office') }}" dir="ltr">{{ url('/branch-office') }}</a>
            </p>
        </div>
    @endif

    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-200 bg-slate-50 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white shadow-md">
                    <i class="fas fa-user-tie text-lg"></i>
                </div>
                <div>
                    <h3 class="text-lg font-black text-slate-900">مديرو الفرع</h3>
                    <p class="text-xs text-slate-600 font-medium mt-1">حسابات بصلاحية <code class="text-xs bg-slate-200 px-1.5 py-0.5 rounded">branch_manager</code></p>
                </div>
            </div>
        </div>

        @if($branchManagers->isNotEmpty())
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50">
                        <tr class="text-xs font-semibold uppercase tracking-widest text-slate-700">
                            <th class="px-6 py-4 text-right">الاسم</th>
                            <th class="px-6 py-4 text-right">البريد</th>
                            <th class="px-6 py-4 text-right">آخر دخول</th>
                            <th class="px-6 py-4 text-right">الحالة</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @foreach($branchManagers as $m)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="px-6 py-4 font-semibold text-slate-900">{{ $m->name }}</td>
                                <td class="px-6 py-4 font-mono text-xs text-slate-700" dir="ltr">{{ $m->email }}</td>
                                <td class="px-6 py-4 text-slate-600">{{ $m->last_login_at?->diffForHumans() ?? '—' }}</td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-semibold {{ $m->is_active ? 'bg-emerald-100 text-emerald-700 border border-emerald-200' : 'bg-rose-100 text-rose-700 border border-rose-200' }}">
                                        {{ $m->is_active ? 'نشط' : 'موقوف' }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="px-6 py-8 text-slate-500 text-sm text-center">لا يوجد مدير فرع مسجّل لهذا الفرع بعد.</p>
        @endif

        <div class="px-6 py-6 border-t border-slate-200 bg-slate-50">
            <h4 class="font-bold text-slate-900 mb-4 text-sm flex items-center gap-2">
                <i class="fas fa-user-plus text-blue-600"></i>
                إضافة مدير فرع جديد
            </h4>
            <form action="{{ route('admin.branches.branch-managers.store', $branch) }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-4 max-w-3xl">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-2">الاسم الكامل <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                           class="w-full rounded-xl border-2 border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                    @error('name')<p class="text-rose-600 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-2">البريد <span class="text-rose-500">*</span></label>
                    <input type="email" name="email" value="{{ old('email') }}" required dir="ltr" autocomplete="off"
                           class="w-full rounded-xl border-2 border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                    @error('email')<p class="text-rose-600 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-slate-700 mb-2">كلمة المرور (اختياري — فارغ = توليد تلقائي)</label>
                    <input type="password" name="password" dir="ltr" autocomplete="new-password" minlength="10"
                           placeholder="اتركها فارغة للتوليد التلقائي"
                           class="w-full rounded-xl border-2 border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                    @error('password')<p class="text-rose-600 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div class="md:col-span-2">
                    <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-blue-600 to-blue-500 hover:from-blue-700 hover:to-blue-600 text-white px-6 py-3 text-sm font-bold shadow-md hover:shadow-lg transition-all">
                        <i class="fas fa-user-plus"></i>
                        إنشاء الحساب
                    </button>
                </div>
            </form>
        </div>
    </section>
</div>
@endsection
