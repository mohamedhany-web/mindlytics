@extends('layouts.admin')

@section('title', 'تسجيلات الطلاب - ' . $offlineCourse->title)
@section('header', 'تسجيلات الطلاب')

@section('content')
<div class="space-y-6" x-data="enrollmentsPage()">
    <!-- الهيدر -->
    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
        <div class="flex flex-wrap justify-between items-center gap-4">
            <div>
                <nav class="text-sm text-gray-500 mb-1">
                    <a href="{{ route('admin.offline-courses.index') }}" class="hover:text-blue-600">الكورسات الأوفلاين</a>
                    <span class="mx-2">/</span>
                    <a href="{{ route('admin.offline-courses.show', $offlineCourse) }}" class="hover:text-blue-600">{{ $offlineCourse->title }}</a>
                    <span class="mx-2">/</span>
                    <span class="text-gray-700 font-semibold">التسجيلات</span>
                </nav>
                <h1 class="text-2xl font-bold text-gray-900">تسجيلات الطلاب: {{ $offlineCourse->title }}</h1>
                <p class="text-gray-600 mt-1">سعر الكورس: <span class="font-bold text-green-700">{{ number_format($offlineCourse->price, 2) }} ج.م</span></p>
                <p class="text-sm text-gray-500 mt-2 max-w-3xl">
                    المجموعة الواحدة تشترك في <strong>نفس المواعيد والجلسات</strong>؛ ويُفصل فقط <strong>من حضر بالمركز (أوفلاين)</strong> عن <strong>من سجّل على قناة الأونلاين</strong> من حيث السعة والتسجيلات.
                </p>
            </div>
            <a href="{{ route('admin.offline-courses.show', $offlineCourse) }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg font-medium transition-colors inline-flex items-center">
                <i class="fas fa-arrow-right mr-2"></i>العودة للكورس
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $err) <li>{{ $err }}</li> @endforeach
            </ul>
        </div>
    @endif

    <!-- تبويب القناة: أوفلاين / أونلاين -->
    <div class="bg-white rounded-xl shadow-lg p-4 border border-gray-200 flex flex-wrap gap-2 items-center">
        <span class="text-sm font-semibold text-gray-600 ml-2">عرض القائمة:</span>
        <a href="{{ route('admin.offline-courses.enrollments.index', ['offlineCourse' => $offlineCourse, 'channel' => 'offline']) }}"
           class="px-4 py-2 rounded-lg text-sm font-semibold transition-colors {{ $channel === 'offline' ? 'bg-blue-600 text-white shadow' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
            <i class="fas fa-building ml-1"></i> تسجيلات الحضور (أوفلاين)
            <span class="opacity-90">({{ $channelCounts['offline'] }})</span>
        </a>
        <a href="{{ route('admin.offline-courses.enrollments.index', ['offlineCourse' => $offlineCourse, 'channel' => 'online']) }}"
           class="px-4 py-2 rounded-lg text-sm font-semibold transition-colors {{ $channel === 'online' ? 'bg-indigo-600 text-white shadow' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
            <i class="fas fa-video ml-1"></i> تسجيلات الأونلاين
            <span class="opacity-90">({{ $channelCounts['online'] }})</span>
        </a>
    </div>

    <!-- تسجيل طالب جديد -->
    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200 border-t-4 {{ $channel === 'online' ? 'border-t-indigo-500' : 'border-t-blue-500' }}">
        <h2 class="text-lg font-bold text-gray-900 mb-1">
            <i class="fas fa-user-plus text-blue-600 ml-2"></i>تسجيل طالب جديد
            @if($channel === 'online')
                <span class="text-sm font-normal text-indigo-700">— على قناة <strong>الأونلاين</strong> (يُحتسب ضمن سعة الأونلاين للمجموعة)</span>
            @else
                <span class="text-sm font-normal text-blue-800">— على قناة <strong>الحضور بالمركز</strong> (يُحتسب ضمن سعة الحضور)</span>
            @endif
        </h2>
        <form action="{{ route('admin.offline-courses.enrollments.store', $offlineCourse) }}" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" name="enrollment_channel" value="{{ $channel }}">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">البحث بالإيميل</label>
                    <input type="email" x-model="emailSearch" @input="filterStudents()"
                           placeholder="اكتب إيميل الطالب للبحث..."
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <p class="text-gray-400 text-xs mt-1">اكتب الإيميل لتصفية قائمة الطلاب</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">الطالب <span class="text-red-500">*</span></label>
                    <select name="user_id" id="studentSelect" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" @change="fetchWorkshopPromo($event.target.value)">
                        <option value="">اختر الطالب</option>
                        @foreach($students as $s)
                            <option value="{{ $s->id }}" data-email="{{ $s->email }}" data-name="{{ $s->name }}">{{ $s->name }} ({{ $s->email }})</option>
                        @endforeach
                    </select>
                    @if($students->isEmpty())
                        <p class="text-amber-600 text-xs mt-1">جميع الطلاب مسجلون بالفعل.</p>
                    @endif
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">المجموعة <span class="text-red-500">*</span></label>
                    <select name="group_id" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="">اختر المجموعة</option>
                        @foreach($groups as $g)
                            <option value="{{ $g->id }}">
                                {{ $g->name }}
                                @if($g->start_date) — يبدأ {{ $g->start_date->format('Y-m-d') }} @endif
                                | حضور {{ $g->current_students }}/{{ $g->max_students }} ({{ $g->offline_enrollments_count ?? 0 }} سجل)
                                | أونلاين {{ $g->current_students_online }}/{{ $g->max_students_online }} ({{ $g->online_enrollments_count ?? 0 }} سجل)
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">الحالة</label>
                    <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="active" selected>نشط</option>
                        <option value="pending">قيد الانتظار</option>
                    </select>
                </div>
            </div>

            <!-- الدفع -->
            <div class="bg-gray-50 rounded-xl p-4 border border-gray-200">
                <h3 class="font-bold text-gray-800 mb-3"><i class="fas fa-money-bill-wave text-green-600 ml-2"></i>تفاصيل الدفع</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                    <div class="lg:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">سعر الكورس لهذا الطالب (ج.م)</label>
                        <input type="number" name="custom_price" x-model.number="coursePrice" step="0.01" min="0"
                               class="w-full px-3 py-2 border border-amber-300 bg-amber-50 rounded-lg focus:ring-2 focus:ring-amber-500 font-semibold"
                               placeholder="{{ number_format((float) $offlineCourse->price, 2, '.', '') }}">
                        <p class="text-xs text-amber-800 mt-1">
                            السعر الافتراضي للكورس: <strong>{{ number_format((float) $offlineCourse->price, 2) }} ج.م</strong>
                            — يمكنك تغييره لهذا الطالب فقط دون التأثير على باقي المسجلين.
                        </p>
                        <button type="button" @click="coursePrice = defaultCoursePrice" class="text-xs text-blue-600 hover:underline mt-1">إعادة السعر الافتراضي</button>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">نوع الدفع <span class="text-red-500">*</span></label>
                        <select name="payment_type" x-model="paymentType" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="full">دفع كامل</option>
                            <option value="partial">دفع جزئي</option>
                            <option value="free">دفع مجاني (بدون مبلغ على الطالب أو الحساب)</option>
                        </select>
                        <p x-show="paymentType === 'full'" class="text-xs text-green-700 mt-1 font-semibold">
                            المبلغ المستحق: <span x-text="formatMoney(netPrice)"></span> ج.م
                        </p>
                    </div>
                    <div x-show="paymentType === 'partial'">
                        <label class="block text-sm font-medium text-gray-700 mb-1">المبلغ المدفوع</label>
                        <input type="number" name="paid_amount" step="0.01" min="0" :max="netPrice"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                               placeholder="0.00">
                        <p class="text-xs text-gray-500 mt-1">الحد الأقصى بعد الخصم: <span x-text="formatMoney(netPrice)"></span> ج.م</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">طريقة الدفع</label>
                        <select name="payment_method" id="payment_method" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="cash" {{ old('payment_method', 'cash') === 'cash' ? 'selected' : '' }}>نقدي</option>
                            <option value="wallet" {{ old('payment_method') === 'wallet' ? 'selected' : '' }}>تحويل على محفظة</option>
                        </select>
                        @error('payment_method')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div id="wallet_wrap">
                        <label class="block text-sm font-medium text-gray-700 mb-1">المحفظة</label>
                        <select name="wallet_id" id="wallet_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="">اختر المحفظة</option>
                            @foreach($wallets as $wallet)
                                <option value="{{ $wallet->id }}" @selected((string) old('wallet_id') === (string) $wallet->id)>
                                    {{ $wallet->name }} — {{ \App\Models\Wallet::typeLabel($wallet->type) }}
                                </option>
                            @endforeach
                        </select>
                        @error('wallet_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">ملاحظات الدفع</label>
                        <input type="text" name="payment_notes" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                               placeholder="اختياري">
                    </div>
                </div>

                <!-- الخصم -->
                <div x-show="paymentType !== 'free'" x-cloak class="mt-4 pt-4 border-t border-gray-200">
                    <div x-show="workshopPromo.has_discount" class="mb-4 rounded-xl border border-violet-200 bg-violet-50 p-4">
                        <p class="text-sm font-bold text-violet-900"><i class="fas fa-ticket-alt ml-1"></i> خصم ورشة مفعّل للطالب</p>
                        <p class="text-sm text-violet-800 mt-1">
                            كود <span class="font-mono font-bold" x-text="workshopPromo.promo_code"></span> —
                            خصم <span x-text="formatMoney(workshopPromo.discount_amount)"></span> ج.م
                        </p>
                    </div>
                    <label class="inline-flex items-center gap-2 cursor-pointer mb-3">
                        <input type="checkbox" name="apply_discount" value="1" x-model="applyDiscount"
                               class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        <span class="text-sm font-semibold text-gray-800"><i class="fas fa-tag text-amber-500 ml-1"></i>تطبيق خصم على الاشتراك</span>
                    </label>
                    <div x-show="applyDiscount" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">نوع الخصم</label>
                            <select name="discount_type" x-model="discountType" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500">
                                <option value="fixed">مبلغ ثابت (ج.م)</option>
                                <option value="percent">نسبة مئوية (%)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">قيمة الخصم</label>
                            <input type="number" name="discount_value" x-model="discountValue" step="0.01" min="0"
                                   :max="discountType === 'percent' ? 100 : coursePrice"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500"
                                   placeholder="0.00">
                            @error('discount_value')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div class="bg-amber-50 border border-amber-200 rounded-lg p-3 text-sm">
                            <p class="text-gray-600">السعر الأصلي: <strong x-text="formatMoney(coursePrice)"></strong> ج.م</p>
                            <p class="text-amber-700">الخصم: <strong x-text="formatMoney(discountAmount)"></strong> ج.م</p>
                            <p class="text-green-800 font-bold mt-1">صافي الاشتراك: <span x-text="formatMoney(netPrice)"></span> ج.م</p>
                        </div>
                    </div>
                </div>
            </div>

            <div>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-lg font-medium transition-colors" {{ $students->isEmpty() ? 'disabled' : '' }}>
                    <i class="fas fa-plus mr-2"></i>تسجيل الطالب
                </button>
            </div>
        </form>
    </div>

    <!-- قائمة التسجيلات -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200 flex flex-wrap justify-between items-center gap-2">
            <h2 class="text-lg font-bold text-gray-900">
                قائمة تسجيلات {{ $channel === 'online' ? 'الأونلاين' : 'الحضور (أوفلاين)' }}
                ({{ $enrollments->total() }})
            </h2>
            @if($channel === 'online')
                <span class="text-xs font-semibold px-2 py-1 rounded-full bg-indigo-100 text-indigo-800">قناة أونلاين</span>
            @else
                <span class="text-xs font-semibold px-2 py-1 rounded-full bg-blue-100 text-blue-800">قناة حضور بالمركز</span>
            @endif
        </div>
        @if($enrollments->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-right font-medium text-gray-500">الطالب</th>
                            <th class="px-4 py-3 text-right font-medium text-gray-500">الإيميل</th>
                            <th class="px-4 py-3 text-right font-medium text-gray-500">المجموعة</th>
                            <th class="px-4 py-3 text-right font-medium text-gray-500">التسجيل</th>
                            <th class="px-4 py-3 text-right font-medium text-gray-500">الحالة</th>
                            <th class="px-4 py-3 text-right font-medium text-gray-500">الإجمالي</th>
                            <th class="px-4 py-3 text-right font-medium text-gray-500">الخصم</th>
                            <th class="px-4 py-3 text-right font-medium text-gray-500">المدفوع</th>
                            <th class="px-4 py-3 text-right font-medium text-gray-500">المتبقي</th>
                            <th class="px-4 py-3 text-right font-medium text-gray-500">حالة الدفع</th>
                            <th class="px-4 py-3 text-right font-medium text-gray-500">إجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($enrollments as $enrollment)
                        <tr>
                            <td class="px-4 py-3">
                                <div class="font-medium text-gray-900">{{ $enrollment->student->name ?? '—' }}</div>
                            </td>
                            <td class="px-4 py-3 text-gray-600 text-xs">{{ $enrollment->student->email ?? '—' }}</td>
                            <td class="px-4 py-3 text-gray-700">{{ $enrollment->group->name ?? '—' }}</td>
                            <td class="px-4 py-3 text-gray-700">{{ $enrollment->enrolled_at?->format('Y-m-d') ?? '—' }}</td>
                            <td class="px-4 py-3">
                                @php
                                    $sLabels = [
                                        'pending' => ['قيد الانتظار', 'bg-amber-100 text-amber-800'],
                                        'active' => ['نشط', 'bg-green-100 text-green-800'],
                                        'completed' => ['منتهي', 'bg-blue-100 text-blue-800'],
                                        'suspended' => ['موقوف', 'bg-red-100 text-red-800'],
                                        'cancelled' => ['ملغي', 'bg-gray-100 text-gray-800'],
                                    ];
                                    $sl = $sLabels[$enrollment->status] ?? ['—', 'bg-gray-100 text-gray-800'];
                                @endphp
                                <span class="px-2 py-0.5 text-xs font-semibold rounded-full {{ $sl[1] }}">{{ $sl[0] }}</span>
                            </td>
                            <td class="px-4 py-3 text-gray-700 font-semibold">{{ number_format($enrollment->total_amount, 2) }}</td>
                            <td class="px-4 py-3 text-gray-700">
                                @if((float) ($enrollment->discount_amount ?? 0) > 0)
                                    <span class="text-amber-700 font-semibold">-{{ number_format($enrollment->discount_amount, 2) }}</span>
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-gray-700 font-semibold">{{ number_format($enrollment->paid_amount, 2) }}</td>
                            <td class="px-4 py-3 text-gray-700">
                                @if((float)$enrollment->remaining_amount > 0)
                                    <span class="text-red-600 font-semibold">{{ number_format($enrollment->remaining_amount, 2) }}</span>
                                @else
                                    <span class="text-green-600">0.00</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @if((float) $enrollment->total_amount <= 0)
                                    <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-slate-100 text-slate-700">مجاني</span>
                                @else
                                    @php
                                        $pLabels = [
                                            'unpaid' => ['لم يدفع', 'bg-red-100 text-red-800'],
                                            'partial' => ['جزئي', 'bg-amber-100 text-amber-800'],
                                            'paid' => ['مكتمل', 'bg-green-100 text-green-800'],
                                        ];
                                        $pl = $pLabels[$enrollment->payment_status] ?? ['—', 'bg-gray-100 text-gray-800'];
                                    @endphp
                                    <span class="px-2 py-0.5 text-xs font-semibold rounded-full {{ $pl[1] }}">{{ $pl[0] }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="flex items-center gap-1 flex-wrap">
                                    <!-- تفعيل/إيقاف -->
                                    <form action="{{ route('admin.offline-courses.enrollments.update-status', [$offlineCourse, $enrollment]) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="status" value="{{ $enrollment->status === 'active' ? 'suspended' : 'active' }}">
                                        <button type="submit" class="text-blue-600 hover:text-blue-800 font-medium text-xs px-1.5 py-1 rounded hover:bg-blue-50">
                                            {{ $enrollment->status === 'active' ? 'إيقاف' : 'تفعيل' }}
                                        </button>
                                    </form>

                                    <!-- تعديل البيانات المالية -->
                                    <button type="button"
                                            @click="openEditModal({{ json_encode([
                                                'id' => $enrollment->id,
                                                'student' => ['name' => $enrollment->student->name ?? ''],
                                                'total_amount' => (float) $enrollment->total_amount,
                                                'discount_amount' => (float) ($enrollment->discount_amount ?? 0),
                                                'paid_amount' => (float) $enrollment->paid_amount,
                                                'remaining_amount' => (float) $enrollment->remaining_amount,
                                                'payment_notes' => $enrollment->payment_notes,
                                                'status' => $enrollment->status,
                                            ], JSON_UNESCAPED_UNICODE) }})"
                                            class="text-sky-600 hover:text-sky-800 font-medium text-xs px-1.5 py-1 rounded hover:bg-sky-50">
                                        <i class="fas fa-edit"></i> تعديل
                                    </button>

                                    @if((float)$enrollment->remaining_amount > 0)
                                    <!-- دفعة إضافية -->
                                    <button type="button" @click="openPaymentModal({{ json_encode([
                                        'id' => $enrollment->id,
                                        'student' => ['name' => $enrollment->student->name ?? ''],
                                        'remaining_amount' => (float) $enrollment->remaining_amount,
                                    ], JSON_UNESCAPED_UNICODE) }})"
                                            class="text-green-600 hover:text-green-800 font-medium text-xs px-1.5 py-1 rounded hover:bg-green-50">
                                        <i class="fas fa-money-bill-wave"></i> دفعة
                                    </button>
                                    @endif

                                    @if((float)$enrollment->paid_amount > 0)
                                    <!-- استرداد -->
                                    <button type="button"
                                            @click="openRefundModal({{ json_encode([
                                                'id' => $enrollment->id,
                                                'student' => ['name' => $enrollment->student->name ?? ''],
                                                'paid_amount' => (float) $enrollment->paid_amount,
                                            ], JSON_UNESCAPED_UNICODE) }})"
                                            class="text-amber-700 hover:text-amber-900 font-medium text-xs px-1.5 py-1 rounded hover:bg-amber-50">
                                        <i class="fas fa-undo"></i> استرداد
                                    </button>
                                    @endif

                                    <!-- حذف -->
                                    <form action="{{ route('admin.offline-courses.enrollments.destroy', [$offlineCourse, $enrollment]) }}" method="POST" class="inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا التسجيل؟');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800 font-medium text-xs px-1.5 py-1 rounded hover:bg-red-50">حذف</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($enrollments->hasPages())
                <div class="px-6 py-3 border-t border-gray-200">{{ $enrollments->links() }}</div>
            @endif
        @else
            <div class="px-6 py-12 text-center text-gray-500">
                <i class="fas fa-user-graduate text-4xl text-gray-300 mb-3"></i>
                <p>لا يوجد تسجيلات لهذا الكورس.</p>
            </div>
        @endif
    </div>

    <!-- نافذة دفعة إضافية -->
    <div x-show="showPaymentModal" x-cloak class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" @click.self="showPaymentModal = false">
        <div class="bg-white rounded-xl shadow-xl max-w-md w-full">
            <div class="p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4"><i class="fas fa-money-bill-wave text-green-600 ml-2"></i>تسجيل دفعة إضافية</h3>
                <div class="bg-gray-50 rounded-lg p-3 mb-4 text-sm">
                    <p>الطالب: <strong x-text="paymentEnrollment?.student?.name"></strong></p>
                    <p>المبلغ المتبقي: <strong class="text-red-600" x-text="formatMoney(paymentEnrollment?.remaining_amount) + ' ج.م'"></strong></p>
                </div>
                <form :action="paymentAction" method="POST">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">المبلغ <span class="text-red-500">*</span></label>
                            <input type="number" name="amount" step="0.01" min="0.01" :max="paymentEnrollment?.remaining_amount" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">طريقة الدفع <span class="text-red-500">*</span></label>
                            <select name="payment_method" x-model="paymentMethodModal" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500">
                                <option value="cash">نقدي</option>
                                <option value="wallet">تحويل على محفظة (إيداع في محفظة الأكاديمية)</option>
                            </select>
                        </div>
                        <div x-show="paymentMethodModal === 'wallet'" x-cloak class="space-y-1">
                            <label class="block text-sm font-medium text-gray-700 mb-1">المحفظة <span class="text-red-500">*</span></label>
                            <select name="wallet_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500"
                                    :disabled="paymentMethodModal !== 'wallet'"
                                    :required="paymentMethodModal === 'wallet'">
                                <option value="">اختر المحفظة</option>
                                @foreach($wallets as $wallet)
                                    <option value="{{ $wallet->id }}">{{ $wallet->name }} — {{ \App\Models\Wallet::typeLabel($wallet->type) }}</option>
                                @endforeach
                            </select>
                            @if($wallets->isEmpty())
                                <p class="text-amber-600 text-xs">لا توجد محافظ مفعّلة. أنشئ محفظة من <a href="{{ route('admin.wallets.index') }}" class="font-semibold text-sky-700 underline">إدارة المحافظ</a>.</p>
                            @endif
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">ملاحظات</label>
                            <textarea name="notes" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500"></textarea>
                        </div>
                    </div>
                    <div class="mt-4 flex justify-end gap-2">
                        <button type="button" @click="showPaymentModal = false" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded-lg font-medium">إلغاء</button>
                        <button type="submit" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium">تسجيل الدفعة</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- نافذة تعديل البيانات المالية -->
    <div x-show="showEditModal" x-cloak class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" @click.self="showEditModal = false">
        <div class="bg-white rounded-xl shadow-xl max-w-lg w-full max-h-[90vh] overflow-y-auto">
            <div class="p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4"><i class="fas fa-edit text-sky-600 ml-2"></i>تعديل بيانات الدفع</h3>
                <div class="bg-sky-50 rounded-lg p-3 mb-4 text-sm">
                    <p>الطالب: <strong x-text="editEnrollment?.student?.name"></strong></p>
                </div>
                <form :action="editAction" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">الإجمالي المستحق (بعد الخصم) <span class="text-red-500">*</span></label>
                            <input type="number" name="total_amount" x-model.number="editForm.total_amount" step="0.01" min="0" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">قيمة الخصم</label>
                            <input type="number" name="discount_amount" x-model.number="editForm.discount_amount" step="0.01" min="0"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">المدفوع <span class="text-red-500">*</span></label>
                            <input type="number" name="paid_amount" x-model.number="editForm.paid_amount" step="0.01" min="0" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500">
                        </div>
                        <div class="bg-gray-50 rounded-lg p-3 text-sm">
                            <p>المتبقي المحسوب:
                                <strong class="text-red-600" x-text="formatMoney(Math.max(0, (editForm.total_amount || 0) - (editForm.paid_amount || 0))) + ' ج.م'"></strong>
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">حالة التسجيل</label>
                            <select name="status" x-model="editForm.status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500">
                                <option value="pending">قيد الانتظار</option>
                                <option value="active">نشط</option>
                                <option value="completed">منتهي</option>
                                <option value="suspended">موقوف</option>
                                <option value="cancelled">ملغي</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">ملاحظات الدفع</label>
                            <textarea name="payment_notes" x-model="editForm.payment_notes" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500"></textarea>
                        </div>
                    </div>
                    <div class="mt-4 flex justify-end gap-2">
                        <button type="button" @click="showEditModal = false" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded-lg font-medium">إلغاء</button>
                        <button type="submit" class="px-4 py-2 bg-sky-600 hover:bg-sky-700 text-white rounded-lg font-medium">حفظ التعديلات</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- نافذة استرداد -->
    <div x-show="showRefundModal" x-cloak class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" @click.self="showRefundModal = false">
        <div class="bg-white rounded-xl shadow-xl max-w-md w-full">
            <div class="p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4"><i class="fas fa-undo text-amber-600 ml-2"></i>استرداد مبلغ</h3>
                <div class="bg-amber-50 border border-amber-200 rounded-lg p-3 mb-4 text-sm">
                    <p>الطالب: <strong x-text="refundEnrollment?.student?.name"></strong></p>
                    <p class="mt-1">أقصى مبلغ قابل للاسترداد:
                        <strong class="text-amber-800" x-text="formatMoney(refundEnrollment?.paid_amount) + ' ج.م'"></strong>
                    </p>
                </div>
                <form :action="refundAction" method="POST" onsubmit="return confirm('تأكيد استرداد هذا المبلغ؟');">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">مبلغ الاسترداد <span class="text-red-500">*</span></label>
                            <input type="number" name="amount" x-model.number="refundAmount" step="0.01" min="0.01"
                                   :max="refundEnrollment?.paid_amount" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">ملاحظات</label>
                            <textarea name="notes" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500"
                                      placeholder="سبب الاسترداد (اختياري)"></textarea>
                        </div>
                    </div>
                    <div class="mt-4 flex justify-end gap-2">
                        <button type="button" @click="showRefundModal = false" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded-lg font-medium">إلغاء</button>
                        <button type="submit" class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-lg font-medium">تأكيد الاسترداد</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function enrollmentsPage() {
    const allOptions = [];
    const sel = document.getElementById('studentSelect');
    if (sel) {
        for (let i = 1; i < sel.options.length; i++) {
            allOptions.push({
                value: sel.options[i].value,
                text: sel.options[i].text,
                email: sel.options[i].dataset.email || '',
                name: sel.options[i].dataset.name || '',
            });
        }
    }

    return {
        paymentType: 'full',
        defaultCoursePrice: {{ (float) $offlineCourse->price }},
        coursePrice: {{ (float) old('custom_price', $offlineCourse->price) }},
        applyDiscount: {{ old('apply_discount') ? 'true' : 'false' }},
        discountType: @json(old('discount_type', 'fixed')),
        discountValue: @json(old('discount_value', '')),
        showPaymentModal: false,
        paymentEnrollment: null,
        paymentAction: '',
        paymentMethodModal: 'cash',
        showEditModal: false,
        editEnrollment: null,
        editAction: '',
        editForm: { total_amount: 0, discount_amount: 0, paid_amount: 0, payment_notes: '', status: 'active' },
        showRefundModal: false,
        refundEnrollment: null,
        refundAction: '',
        refundAmount: 0,
        emailSearch: '',
        workshopPromo: { has_discount: false, discount_amount: 0, promo_code: '' },
        offlineCourseId: {{ $offlineCourse->id }},
        promoPreviewUrl: @json(route('admin.workshop-promo-codes.preview-discount')),
        enrollmentsBaseUrl: @json(url('admin/offline-courses/' . $offlineCourse->id . '/enrollments')),

        get effectiveDiscountAmount() {
            if (this.paymentType === 'free') return 0;
            if (this.applyDiscount) return this.discountAmount;
            if (this.workshopPromo.has_discount) return parseFloat(this.workshopPromo.discount_amount) || 0;
            return 0;
        },

        get discountAmount() {
            if (!this.applyDiscount || this.paymentType === 'free') return 0;
            const value = parseFloat(this.discountValue) || 0;
            if (value <= 0) return 0;
            if (this.discountType === 'percent') {
                const pct = Math.min(100, value);
                return Math.min(this.coursePrice, Math.round(this.coursePrice * pct / 100 * 100) / 100);
            }
            return Math.min(this.coursePrice, Math.round(value * 100) / 100);
        },

        get netPrice() {
            if (this.paymentType === 'free') return 0;
            return Math.max(0, Math.round((this.coursePrice - this.effectiveDiscountAmount) * 100) / 100);
        },

        formatMoney(amount) {
            return (parseFloat(amount) || 0).toFixed(2);
        },

        filterStudents() {
            const q = this.emailSearch.trim().toLowerCase();
            const select = document.getElementById('studentSelect');
            if (!select) return;

            while (select.options.length > 1) select.remove(1);

            const filtered = q === '' ? allOptions : allOptions.filter(o =>
                o.email.toLowerCase().includes(q) || o.name.toLowerCase().includes(q)
            );

            filtered.forEach(o => {
                const opt = new Option(o.text, o.value);
                opt.dataset.email = o.email;
                opt.dataset.name = o.name;
                select.add(opt);
            });

            if (filtered.length === 1) {
                select.value = filtered[0].value;
                this.fetchWorkshopPromo(filtered[0].value);
            }
        },

        async fetchWorkshopPromo(userId) {
            this.workshopPromo = { has_discount: false, discount_amount: 0, promo_code: '' };
            if (!userId) return;
            try {
                const url = new URL(this.promoPreviewUrl, window.location.origin);
                url.searchParams.set('user_id', userId);
                url.searchParams.set('offline_course_id', this.offlineCourseId);
                const res = await fetch(url, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
                const data = await res.json();
                if (data.has_discount) {
                    this.workshopPromo = data;
                    this.applyDiscount = false;
                }
            } catch (e) { /* ignore */ }
        },

        openPaymentModal(enrollment) {
            this.paymentEnrollment = enrollment;
            this.paymentMethodModal = 'cash';
            this.paymentAction = this.enrollmentsBaseUrl + '/' + enrollment.id + '/payment';
            this.showPaymentModal = true;
        },

        openEditModal(enrollment) {
            this.editEnrollment = enrollment;
            this.editForm = {
                total_amount: parseFloat(enrollment.total_amount) || 0,
                discount_amount: parseFloat(enrollment.discount_amount) || 0,
                paid_amount: parseFloat(enrollment.paid_amount) || 0,
                payment_notes: enrollment.payment_notes || '',
                status: enrollment.status || 'active',
            };
            this.editAction = this.enrollmentsBaseUrl + '/' + enrollment.id + '/financial';
            this.showEditModal = true;
        },

        openRefundModal(enrollment) {
            this.refundEnrollment = enrollment;
            this.refundAmount = parseFloat(enrollment.paid_amount) || 0;
            this.refundAction = this.enrollmentsBaseUrl + '/' + enrollment.id + '/refund';
            this.showRefundModal = true;
        }
    };
}

document.addEventListener('DOMContentLoaded', function () {
    const paymentTypeEl = document.querySelector('select[name="payment_type"]');
    const paymentMethodEl = document.getElementById('payment_method');
    const walletWrapEl = document.getElementById('wallet_wrap');
    const walletSelectEl = document.getElementById('wallet_id');

    function toggleWalletField() {
        const requiresPayment = paymentTypeEl && paymentTypeEl.value !== 'free';
        const isWallet = paymentMethodEl && paymentMethodEl.value === 'wallet';
        if (walletWrapEl) {
            walletWrapEl.style.display = requiresPayment && isWallet ? '' : 'none';
        }
        if (walletSelectEl) {
            walletSelectEl.required = requiresPayment && isWallet;
            if (!requiresPayment || !isWallet) {
                walletSelectEl.value = '';
            }
        }
    }

    if (paymentTypeEl) paymentTypeEl.addEventListener('change', toggleWalletField);
    if (paymentMethodEl) paymentMethodEl.addEventListener('change', toggleWalletField);
    toggleWalletField();
});
</script>
@endsection
