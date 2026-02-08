@extends('layouts.admin')

@section('title', 'إدارة الإشعارات')
@section('header', 'إدارة الإشعارات')

@section('content')
@php
    $summaryCards = [
        [
            'label' => 'إجمالي الإشعارات',
            'value' => number_format($stats['total']),
            'icon' => 'fas fa-bell',
            'color' => 'blue',
        ],
        [
            'label' => 'غير المقروء',
            'value' => number_format($stats['unread']),
            'icon' => 'fas fa-envelope-open-text',
            'color' => 'amber',
        ],
        [
            'label' => 'مقروء',
            'value' => number_format($stats['total'] - $stats['unread']),
            'icon' => 'fas fa-check-circle',
            'color' => 'emerald',
        ],
        [
            'label' => 'أُرسلت اليوم',
            'value' => number_format($stats['today']),
            'icon' => 'fas fa-calendar-day',
            'color' => 'purple',
        ],
    ];
@endphp

<div class="space-y-6">
    <!-- الهيدر -->
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-6 py-5 bg-slate-50 border-b border-slate-200 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white shadow-md">
                    <i class="fas fa-bell text-lg"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-black text-slate-900">مركز الإشعارات</h2>
                    <p class="text-sm text-slate-600 mt-1">إرسال التنبيهات ومتابعة حالة القراءة لجميع المستلمين.</p>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('admin.notifications.statistics') }}" class="inline-flex items-center gap-2 rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition-colors">
                    <i class="fas fa-chart-line"></i>
                    الإحصائيات
                </a>
                <a href="{{ route('admin.notifications.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-blue-600 to-blue-500 hover:from-blue-700 hover:to-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-md hover:shadow-lg transition-all duration-200">
                    <i class="fas fa-paper-plane"></i>
                    إرسال إشعار جديد
                </a>
            </div>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 p-6">
            @foreach ($summaryCards as $card)
                <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex-1">
                            <p class="text-xs font-semibold text-slate-600 mb-1">{{ $card['label'] }}</p>
                            <p class="text-2xl font-black text-slate-900">{{ $card['value'] }}</p>
                        </div>
                        <div class="w-12 h-12 rounded-lg bg-{{ $card['color'] }}-100 flex items-center justify-center text-{{ $card['color'] }}-600 shadow-sm">
                            <i class="{{ $card['icon'] }} text-lg"></i>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    <!-- البحث والفلترة -->
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-200 bg-slate-50">
            <h3 class="text-lg font-black text-slate-900 mb-2 flex items-center gap-2">
                <i class="fas fa-filter text-blue-600"></i>
                البحث والفلترة
            </h3>
            <p class="text-xs text-slate-600">تصفية الإشعارات حسب النوع والحالة أو البحث داخل العناوين.</p>
        </div>
        <div class="p-6">
            <form method="GET" id="filterForm" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-2 flex items-center gap-2">
                        <i class="fas fa-search text-blue-600 text-sm"></i>
                        البحث
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-3 flex items-center text-blue-500"><i class="fas fa-search"></i></span>
                        <input type="text" name="search" value="{{ old('search', request('search')) }}" maxlength="255" placeholder="عنوان الإشعار أو محتواه" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 pr-10 text-sm text-slate-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all" />
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-2 flex items-center gap-2">
                        <i class="fas fa-tag text-blue-600 text-sm"></i>
                        نوع الإشعار
                    </label>
                    <select name="type" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                        <option value="">جميع الأنواع</option>
                        @foreach ($notificationTypes as $key => $type)
                            <option value="{{ htmlspecialchars($key, ENT_QUOTES, 'UTF-8') }}" {{ request('type') == $key ? 'selected' : '' }}>{{ htmlspecialchars($type, ENT_QUOTES, 'UTF-8') }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-2 flex items-center gap-2">
                        <i class="fas fa-toggle-on text-blue-600 text-sm"></i>
                        الحالة
                    </label>
                    <select name="status" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                        <option value="">جميع الحالات</option>
                        <option value="read" {{ request('status') == 'read' ? 'selected' : '' }}>مقروءة</option>
                        <option value="unread" {{ request('status') == 'unread' ? 'selected' : '' }}>غير مقروءة</option>
                    </select>
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit" class="flex-1 inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-blue-600 to-blue-500 hover:from-blue-700 hover:to-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-md hover:shadow-lg transition-all duration-200">
                        <i class="fas fa-filter"></i>
                        تطبيق الفلاتر
                    </button>
                    @if(request()->anyFilled(['search', 'type', 'status']))
                    <a href="{{ route('admin.notifications.index') }}" class="inline-flex items-center justify-center rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition-colors" title="مسح الفلتر">
                        <i class="fas fa-times"></i>
                    </a>
                    @endif
                </div>
            </form>
        </div>
    </section>

    <!-- الإشعارات المرسلة -->
    <section class="rounded-2xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-200 bg-slate-50 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h3 class="text-lg font-black text-slate-900">الإشعارات المرسلة</h3>
                <p class="text-xs text-slate-600 mt-1">{{ $notifications->total() }} إشعار تم إرساله حتى الآن.</p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <button type="button" onclick="markAllAsRead()" class="inline-flex items-center gap-2 rounded-xl border border-emerald-300 px-4 py-2 text-sm font-semibold text-emerald-600 bg-white hover:bg-emerald-50 transition-colors">
                    <i class="fas fa-check-double"></i>
                    تحديد الكل كمقروء
                </button>
                <button type="button" onclick="cleanupOld()" class="inline-flex items-center gap-2 rounded-xl border border-rose-300 px-4 py-2 text-sm font-semibold text-rose-600 bg-white hover:bg-rose-50 transition-colors">
                    <i class="fas fa-trash-alt"></i>
                    حذف القديمة
                </button>
            </div>
        </div>

        @if ($notifications->count() > 0)
            <div class="p-6 space-y-3">
                @foreach ($notifications as $notification)
                    @php
                        $typeColor = match($notification->type_color ?? '') {
                            'blue' => 'bg-blue-100 text-blue-600',
                            'green' => 'bg-emerald-100 text-emerald-600',
                            'yellow' => 'bg-amber-100 text-amber-600',
                            'red' => 'bg-rose-100 text-rose-600',
                            'purple' => 'bg-purple-100 text-purple-600',
                            'orange' => 'bg-orange-100 text-orange-600',
                            default => 'bg-slate-100 text-slate-600'
                        };
                        $priorityClasses = match($notification->priority ?? '') {
                            'urgent' => 'bg-rose-100 text-rose-700 border border-rose-200',
                            'high' => 'bg-amber-100 text-amber-700 border border-amber-200',
                            'low' => 'bg-slate-100 text-slate-700 border border-slate-200',
                            default => 'bg-blue-100 text-blue-700 border border-blue-200'
                        };
                    @endphp
                    <div class="rounded-xl border border-slate-200 bg-white p-5 hover:border-blue-300 hover:shadow-md transition-all duration-200 {{ $notification->is_read ? 'opacity-75' : '' }}">
                        <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                            <div class="flex flex-1 items-start gap-4">
                                <div class="w-12 h-12 flex-shrink-0 items-center justify-center rounded-xl {{ $typeColor }} flex">
                                    <i class="{{ $notification->type_icon ?? 'fas fa-bell' }} text-lg"></i>
                                </div>
                                <div class="flex-1 space-y-2">
                                    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:gap-3">
                                        <h4 class="text-base font-bold text-slate-900">{{ htmlspecialchars($notification->title, ENT_QUOTES, 'UTF-8') }}</h4>
                                        <span class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1 text-xs font-semibold {{ $priorityClasses }}">
                                            <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                                            {{ htmlspecialchars(\App\Models\Notification::getPriorities()[$notification->priority] ?? $notification->priority, ENT_QUOTES, 'UTF-8') }}
                                        </span>
                                        @unless ($notification->is_read)
                                            <span class="inline-flex items-center gap-1.5 rounded-lg bg-blue-100 px-3 py-1 text-xs font-semibold text-blue-700 border border-blue-200">
                                                <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                                                جديد
                                            </span>
                                        @endunless
                                    </div>
                                    <p class="text-sm leading-relaxed text-slate-600">{{ htmlspecialchars(Str::limit($notification->message, 200), ENT_QUOTES, 'UTF-8') }}</p>
                                    <div class="flex flex-wrap items-center gap-3 text-xs text-slate-600">
                                        <span class="inline-flex items-center gap-1.5">
                                            <i class="fas fa-user text-blue-500"></i>
                                            {{ htmlspecialchars($notification->user->name ?? 'غير محدد', ENT_QUOTES, 'UTF-8') }}
                                        </span>
                                        <span class="inline-flex items-center gap-1.5">
                                            <i class="fas fa-tag text-blue-500"></i>
                                            {{ htmlspecialchars(\App\Models\Notification::getTypes()[$notification->type] ?? $notification->type, ENT_QUOTES, 'UTF-8') }}
                                        </span>
                                        <span class="inline-flex items-center gap-1.5">
                                            <i class="fas fa-clock text-blue-500"></i>
                                            {{ $notification->created_at->diffForHumans() }}
                                        </span>
                                        @if ($notification->target_type !== 'individual')
                                            <span class="inline-flex items-center gap-1.5">
                                                <i class="fas fa-users text-blue-500"></i>
                                                {{ htmlspecialchars(\App\Models\Notification::getTargetTypes()[$notification->target_type] ?? $notification->target_type, ENT_QUOTES, 'UTF-8') }}
                                            </span>
                                        @endif
                                        @if ($notification->action_url)
                                            <span class="inline-flex items-center gap-1.5 text-blue-600">
                                                <i class="fas fa-link text-blue-500"></i>
                                                {{ htmlspecialchars($notification->action_text ?: 'رابط مرتبط', ENT_QUOTES, 'UTF-8') }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 self-end lg:self-start flex-shrink-0">
                                <a href="{{ route('admin.notifications.show', $notification) }}" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-slate-300 bg-white text-blue-600 hover:bg-blue-50 hover:border-blue-400 transition-colors" title="عرض التفاصيل">
                                    <i class="fas fa-eye text-sm"></i>
                                </a>
                                <form action="{{ route('admin.notifications.destroy', $notification) }}" method="POST" class="delete-form" onsubmit="return confirmDelete(event);">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-rose-300 bg-white text-rose-600 hover:bg-rose-50 hover:border-rose-400 transition-colors" title="حذف">
                                        <i class="fas fa-trash text-sm"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach

                @if ($notifications->hasPages())
                    <div class="border-t border-slate-200 pt-5">
                        {{ $notifications->appends(request()->query())->links() }}
                    </div>
                @endif
            </div>
        @else
            <div class="p-12 text-center">
                <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-2xl bg-blue-50 text-blue-600">
                    <i class="fas fa-bell-slash text-2xl"></i>
                </div>
                <p class="font-bold text-slate-900 mb-1">لا توجد إشعارات</p>
                <p class="text-sm text-slate-600">لا توجد إشعارات مطابقة للبحث الحالي.</p>
            </div>
        @endif
    </section>

    <section class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <!-- إرسال سريع -->
        <div class="xl:col-span-2 rounded-xl bg-white border border-slate-200 shadow-lg overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-200 bg-slate-50">
                <h3 class="text-lg font-black text-slate-900 mb-2 flex items-center gap-2">
                    <i class="fas fa-bolt text-blue-600"></i>
                    إرسال سريع
                </h3>
                <p class="text-xs text-slate-600">أرسل تنبيهاً عاماً لجميع الطلاب بخطوات بسيطة.</p>
            </div>
            <div class="p-6">
                <form id="quick-send-form" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-2 flex items-center gap-2">
                            <i class="fas fa-heading text-blue-600 text-sm"></i>
                            العنوان
                        </label>
                        <input type="text" id="quick_title" name="title" required maxlength="255" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all" placeholder="عنوان الإشعار" />
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-2 flex items-center gap-2">
                            <i class="fas fa-users text-blue-600 text-sm"></i>
                            المستهدفون
                        </label>
                        <select id="quick_target" name="target_type" required onchange="updateQuickTargetCount()" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                            <option value="">اختر المستهدفين</option>
                            <option value="all_students">جميع الطلاب</option>
                        </select>
                        <p class="mt-2 text-xs text-slate-600">سيتم الإرسال إلى <span id="quick-target-count" class="font-semibold text-blue-600">0</span> طالب.</p>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-xs font-semibold text-slate-700 mb-2 flex items-center gap-2">
                            <i class="fas fa-align-right text-blue-600 text-sm"></i>
                            نص الإشعار
                        </label>
                        <textarea id="quick_message" name="message" rows="3" required maxlength="1000" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm leading-relaxed text-slate-900 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all resize-none" placeholder="اكتب رسالة مختصرة ومباشرة..."></textarea>
                    </div>
                    <div class="md:col-span-2 flex items-center justify-end">
                        <button type="button" onclick="sendQuickNotification()" id="quick-send-btn" class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-emerald-600 to-emerald-500 hover:from-emerald-700 hover:to-emerald-600 px-5 py-2.5 text-sm font-semibold text-white shadow-md hover:shadow-lg transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed">
                            <i class="fas fa-paper-plane"></i>
                            إرسال سريع
                        </button>
                    </div>
                </form>
            </div>
        </div>
        <!-- توزيع حسب النوع -->
        <div class="rounded-xl bg-white border border-slate-200 shadow-lg overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-200 bg-slate-50">
                <h3 class="text-lg font-black text-slate-900 mb-2 flex items-center gap-2">
                    <i class="fas fa-chart-pie text-blue-600"></i>
                    توزيع حسب النوع
                </h3>
                <p class="text-xs text-slate-600">مؤشر سريع لأكثر أنواع الإشعارات استخداماً.</p>
            </div>
            <div class="p-6">
                @if ($stats['by_type']->count() > 0)
                    <div class="grid grid-cols-2 gap-3">
                        @foreach ($stats['by_type'] as $type => $count)
                            <div class="rounded-lg border border-slate-200 bg-slate-50 p-4 text-center">
                                <p class="text-xl font-black text-slate-900">{{ $count }}</p>
                                <p class="text-xs text-slate-600 mt-1">{{ htmlspecialchars($notificationTypes[$type] ?? $type, ENT_QUOTES, 'UTF-8') }}</p>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-slate-600 text-center py-4">لم يتم تجميع بيانات كافية بعد.</p>
                @endif
            </div>
        </div>
    </section>
</div>

@push('scripts')
<script>
    // حماية من Double Submit
    let quickSendSubmitting = false;
    let actionSubmitting = false;

    // حماية من XSS - تنقية البيانات
    function sanitizeInput(input) {
        if (!input) return '';
        const div = document.createElement('div');
        div.textContent = input;
        return div.innerHTML.replace(/[<>]/g, '');
    }

    function updateQuickTargetCount() {
        const targetType = document.getElementById('quick_target')?.value;
        const targetId = null;
        if (targetType && targetType.trim()) {
            // حماية من SQL Injection في URL
            const safeTargetType = encodeURIComponent(targetType.trim());
            fetch(`{{ route('admin.notifications.target-count') }}?target_type=${safeTargetType}&target_id=${targetId || ''}`)
                .then(response => {
                    if (!response.ok) throw new Error('Network response was not ok');
                    return response.json();
                })
                .then(data => {
                    const countEl = document.getElementById('quick-target-count');
                    if (countEl) {
                        countEl.textContent = parseInt(data.count) || 0;
                    }
                })
                .catch(() => {
                    const countEl = document.getElementById('quick-target-count');
                    if (countEl) {
                        countEl.textContent = 0;
                    }
                });
        } else {
            const countEl = document.getElementById('quick-target-count');
            if (countEl) {
                countEl.textContent = 0;
            }
        }
    }

    function sendQuickNotification() {
        if (quickSendSubmitting) {
            return false;
        }

        const form = document.getElementById('quick-send-form');
        if (!form) return;

        const titleEl = document.getElementById('quick_title');
        const messageEl = document.getElementById('quick_message');
        const targetEl = document.getElementById('quick_target');
        const sendBtn = document.getElementById('quick-send-btn');

        if (!titleEl || !messageEl || !targetEl) return;

        // Sanitization - تنقية البيانات
        const title = sanitizeInput(titleEl.value.trim());
        const message = sanitizeInput(messageEl.value.trim());
        const targetType = targetEl.value.trim();

        if (!title || !message || !targetType) {
            alert('يرجى ملء جميع الحقول المطلوبة');
            return false;
        }

        // التحقق من الأطوال
        if (title.length > 255) {
            alert('العنوان يجب ألا يتجاوز 255 حرف');
            return false;
        }

        if (message.length > 1000) {
            alert('النص يجب ألا يتجاوز 1000 حرف');
            return false;
        }

        const payload = {
            title: title,
            message: message,
            target_type: targetType,
            target_id: null,
        };

        quickSendSubmitting = true;
        if (sendBtn) {
            sendBtn.disabled = true;
            sendBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري الإرسال...';
        }

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

        fetch('{{ route("admin.notifications.quick-send") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
            body: JSON.stringify(payload),
        })
        .then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            return response.json();
        })
        .then(data => {
            if (data.success) {
                alert(data.message || 'تم إرسال الإشعار بنجاح');
                form.reset();
                const countEl = document.getElementById('quick-target-count');
                if (countEl) countEl.textContent = 0;
                window.location.reload();
            } else {
                alert(data.message || 'حدث خطأ في إرسال الإشعار');
                quickSendSubmitting = false;
                if (sendBtn) {
                    sendBtn.disabled = false;
                    sendBtn.innerHTML = '<i class="fas fa-paper-plane"></i> إرسال سريع';
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('حدث خطأ في إرسال الإشعار. يرجى المحاولة مرة أخرى.');
            quickSendSubmitting = false;
            if (sendBtn) {
                sendBtn.disabled = false;
                sendBtn.innerHTML = '<i class="fas fa-paper-plane"></i> إرسال سريع';
            }
        });
    }

    function markAllAsRead() {
        if (actionSubmitting) return false;
        if (!confirm('هل تريد تحديد جميع الإشعارات كمقروءة؟')) return false;

        actionSubmitting = true;
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

        fetch('{{ route("admin.notifications.mark-all-read") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
        })
        .then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            return response.json();
        })
        .then(data => {
            if (data.success) {
                alert(data.message || 'تم التحديث بنجاح');
                window.location.reload();
            } else {
                actionSubmitting = false;
            }
        })
        .catch(() => {
            alert('حدث خطأ. يرجى المحاولة مرة أخرى.');
            actionSubmitting = false;
        });
    }

    function cleanupOld() {
        if (actionSubmitting) return false;
        if (!confirm('هل تريد حذف الإشعارات المقروءة الأقدم من 30 يوم؟')) return false;

        actionSubmitting = true;
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

        fetch('{{ route("admin.notifications.cleanup") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
            body: JSON.stringify({ days: 30 }),
        })
        .then(response => {
            if (!response.ok) throw new Error('Network response was not ok');
            return response.json();
        })
        .then(data => {
            if (data.success) {
                alert(data.message || 'تم الحذف بنجاح');
                window.location.reload();
            } else {
                actionSubmitting = false;
            }
        })
        .catch(() => {
            alert('حدث خطأ. يرجى المحاولة مرة أخرى.');
            actionSubmitting = false;
        });
    }

    function confirmDelete(event) {
        if (actionSubmitting) {
            event.preventDefault();
            return false;
        }
        const confirmed = confirm('هل أنت متأكد من حذف هذا الإشعار؟');
        if (confirmed) {
            actionSubmitting = true;
            const btn = event.target.closest('form').querySelector('button[type="submit"]');
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            }
        }
        return confirmed;
    }

    // حماية من XSS في بيانات البحث
    const filterForm = document.getElementById('filterForm');
    if (filterForm) {
        filterForm.addEventListener('submit', function(e) {
            const searchInput = this.querySelector('input[name="search"]');
            if (searchInput) {
                searchInput.value = searchInput.value.replace(/<[^>]*>/g, '').trim();
            }
        });
    }

    // منع الإرسال المتكرر
    document.querySelectorAll('.delete-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            if (actionSubmitting) {
                e.preventDefault();
                return false;
            }
        });
    });
</script>
@endpush
@endsection
