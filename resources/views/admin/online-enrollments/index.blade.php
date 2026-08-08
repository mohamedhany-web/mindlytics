@extends('layouts.admin')

@section('title', 'إدارة تسجيل الطلاب - الأونلاين')
@section('header', 'إدارة تسجيل الطلاب - الأونلاين')

@section('content')
<div class="space-y-6">
    <!-- إحصائيات سريعة -->
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-5 gap-6">
        <!-- إجمالي التسجيلات -->
        <div class="dashboard-card rounded-2xl p-5 sm:p-6 card-hover-effect relative overflow-hidden group border-2 border-blue-200/50 hover:border-blue-300/70 shadow-xl hover:shadow-2xl transition-all duration-300" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(240, 249, 255, 0.95) 50%, rgba(224, 242, 254, 0.9) 100%);">
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-sm font-semibold text-gray-600 mb-1">إجمالي التسجيلات</p>
                        <p class="text-3xl font-black text-gray-900">{{ number_format($stats['total']) }}</p>
                    </div>
                    <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center text-white shadow-lg">
                        <i class="fas fa-users text-2xl"></i>
                    </div>
                </div>
                <p class="text-xs text-blue-600">جميع تسجيلات الطلاب</p>
            </div>
        </div>

        <!-- في الانتظار -->
        <a href="{{ route('admin.online-enrollments.index', ['status' => 'pending']) }}" class="dashboard-card rounded-2xl p-5 sm:p-6 card-hover-effect relative overflow-hidden group border-2 border-yellow-200/50 hover:border-yellow-300/70 shadow-xl hover:shadow-2xl transition-all duration-300" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(255, 251, 235, 0.95) 50%, rgba(254, 243, 199, 0.9) 100%);">
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-sm font-semibold text-gray-600 mb-1">في الانتظار</p>
                        <p class="text-3xl font-black text-yellow-700">{{ number_format($stats['pending']) }}</p>
                    </div>
                    <div class="w-16 h-16 bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-2xl flex items-center justify-center text-white shadow-lg">
                        <i class="fas fa-clock text-2xl"></i>
                    </div>
                </div>
                <p class="text-xs text-yellow-600">بحاجة للتفعيل</p>
            </div>
        </a>

        <!-- نشط -->
        <a href="{{ route('admin.online-enrollments.index', ['status' => 'active']) }}" class="dashboard-card rounded-2xl p-5 sm:p-6 card-hover-effect relative overflow-hidden group border-2 border-green-200/50 hover:border-green-300/70 shadow-xl hover:shadow-2xl transition-all duration-300" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(240, 253, 250, 0.95) 50%, rgba(209, 250, 229, 0.9) 100%);">
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-sm font-semibold text-gray-600 mb-1">نشط</p>
                        <p class="text-3xl font-black text-green-700">{{ number_format($stats['active']) }}</p>
                    </div>
                    <div class="w-16 h-16 bg-gradient-to-br from-green-500 to-green-600 rounded-2xl flex items-center justify-center text-white shadow-lg">
                        <i class="fas fa-check-circle text-2xl"></i>
                    </div>
                </div>
                <p class="text-xs text-green-600">مفعل ويتعلم</p>
            </div>
        </a>

        <!-- أنهى المنهج (للتقدم 100% — جاهز للشهادة) -->
        <a href="{{ route('admin.online-enrollments.index', ['completion' => 'finished']) }}" class="dashboard-card rounded-2xl p-5 sm:p-6 card-hover-effect relative overflow-hidden group border-2 border-emerald-200/50 hover:border-emerald-300/70 shadow-xl hover:shadow-2xl transition-all duration-300 {{ request('completion') === 'finished' ? 'ring-2 ring-emerald-500' : '' }}" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(236, 253, 245, 0.95) 50%, rgba(209, 250, 229, 0.9) 100%);">
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-sm font-semibold text-gray-600 mb-1">أنهى المنهج</p>
                        <p class="text-3xl font-black text-emerald-700">{{ number_format($stats['finished_curriculum'] ?? 0) }}</p>
                    </div>
                    <div class="w-16 h-16 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-2xl flex items-center justify-center text-white shadow-lg">
                        <i class="fas fa-certificate text-2xl"></i>
                    </div>
                </div>
                <p class="text-xs text-emerald-700">تقدّم 100% — جاهز للشهادة</p>
            </div>
        </a>

        <!-- مكتمل (حالة إدارية) -->
        <a href="{{ route('admin.online-enrollments.index', ['status' => 'completed']) }}" class="dashboard-card rounded-2xl p-5 sm:p-6 card-hover-effect relative overflow-hidden group border-2 border-purple-200/50 hover:border-purple-300/70 shadow-xl hover:shadow-2xl transition-all duration-300" style="background: linear-gradient(135deg, rgba(255, 255, 255, 0.98) 0%, rgba(250, 245, 255, 0.95) 50%, rgba(243, 232, 255, 0.9) 100%);">
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-sm font-semibold text-gray-600 mb-1">حالة مكتمل</p>
                        <p class="text-3xl font-black text-purple-700">{{ number_format($stats['completed']) }}</p>
                    </div>
                    <div class="w-16 h-16 bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl flex items-center justify-center text-white shadow-lg">
                        <i class="fas fa-graduation-cap text-2xl"></i>
                    </div>
                </div>
                <p class="text-xs text-purple-600">status = completed</p>
            </div>
        </a>
    </div>

    <!-- تفعيل سريع بالبريد الإلكتروني -->
    <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-4">
            <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2">
                <i class="fas fa-bolt text-amber-500"></i>
                تفعيل سريع للكورس عن طريق البريد الإلكتروني
            </h3>
            <p class="text-xs sm:text-sm text-gray-500">
                أدخل بريد الطالب واختر الكورس، وسيتم إنشاء/تفعيل التسجيل مباشرة مع إرسال بريد تفعيل.
            </p>
        </div>

        <form method="POST" action="{{ route('admin.online-enrollments.quick-activate') }}" class="space-y-4"
              x-data="quickActivateForm(@js($courses->map(fn($c) => [
                  'id' => $c->id,
                  'price' => $c->originalPrice(),
                  'effective_price' => $c->effectivePrice(),
                  'course_discount' => $c->courseDiscountAmount(),
              ])))">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div>
                <label for="quick_email" class="block text-sm font-medium text-gray-700 mb-2">بريد الطالب</label>
                <input type="email" name="email" id="quick_email"
                       value="{{ old('email') }}"
                       placeholder="student@example.com"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                @error('quick_activate_email')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
                @error('email')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="quick_course" class="block text-sm font-medium text-gray-700 mb-2">الكورس</label>
                <select name="advanced_course_id" id="quick_course" x-model="courseId" @change="onCourseChange()"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">اختر الكورس</option>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}" {{ old('advanced_course_id') == $course->id ? 'selected' : '' }}>
                            {{ $course->title }} ({{ number_format($course->effectivePrice(), 0) }} ج.م@if($course->hasCourseDiscount()) — كان {{ number_format($course->originalPrice(), 0) }}@endif)
                        </option>
                    @endforeach
                </select>
                @error('advanced_course_id')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">سعر الكورس</label>
                <input type="text" readonly :value="listPrice.toFixed(2) + ' ج.م'"
                       class="w-full px-3 py-2 border border-gray-200 rounded-lg bg-gray-50 text-gray-700">
            </div>

            <div class="md:col-span-2 lg:col-span-4 rounded-xl border border-emerald-200 bg-emerald-50/60 p-4">
                <label class="inline-flex items-start gap-3 cursor-pointer">
                    <input type="checkbox" name="activate_as_free" value="1"
                           x-model="activateAsFree" @change="onFreeToggle()"
                           class="mt-1 rounded border-emerald-400 text-emerald-600 focus:ring-emerald-500">
                    <span>
                        <span class="block text-sm font-bold text-emerald-900">تفعيل مجاني (مخفي عن المدرب)</span>
                        <span class="block text-xs text-emerald-800/80 mt-1">يفتح الكورس للطالب بدون فاتورة وبدون نسبة للمدرب، ولن يظهر اسمه أو بياناته في لوحة المدرب.</span>
                    </span>
                </label>
            </div>

            <div x-show="!activateAsFree" x-cloak>
                <label for="quick_discount" class="block text-sm font-medium text-gray-700 mb-2">الخصم (ج.م)</label>
                <input type="number" name="discount_amount" id="quick_discount" step="0.01" min="0"
                       x-model.number="discount" @input="updateFinal()"
                       value="{{ old('discount_amount', 0) }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>

            <div x-show="!activateAsFree" x-cloak>
                <label for="quick_final" class="block text-sm font-medium text-gray-700 mb-2">المبلغ المدفوع (بعد الخصم)</label>
                <input type="number" name="final_price" id="quick_final" step="0.01" min="0"
                       x-model.number="finalPrice" @input="discount = Math.max(0, listPrice - finalPrice)"
                       value="{{ old('final_price') }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500">
                <p class="text-[11px] text-gray-500 mt-1">يُستخدم لحساب نسبة المدرب + إنشاء فاتورة</p>
            </div>

            <div x-show="!activateAsFree" x-cloak>
                <label for="quick_payment_method" class="block text-sm font-medium text-gray-700 mb-2">طريقة الدفع</label>
                <select name="payment_method" id="quick_payment_method" x-model="paymentMethod"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                    <option value="cash">نقدي</option>
                    <option value="bank_transfer">تحويل بنكي</option>
                    <option value="online">أونلاين</option>
                    <option value="wallet">محفظة</option>
                    <option value="other">أخرى</option>
                </select>
            </div>

            <div x-show="!activateAsFree && paymentMethod === 'wallet'" x-cloak>
                <label for="quick_wallet_id" class="block text-sm font-medium text-gray-700 mb-2">المحفظة</label>
                <select name="wallet_id" id="quick_wallet_id" x-bind:required="!activateAsFree && paymentMethod === 'wallet'"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">اختر المحفظة</option>
                    @foreach($wallets as $wallet)
                        <option value="{{ $wallet->id }}" @selected((string) old('wallet_id') === (string) $wallet->id)>
                            {{ $wallet->name ?: \App\Models\Wallet::typeLabel($wallet->type) }}
                            ({{ number_format((float) $wallet->balance, 2) }} ج.م)
                        </option>
                    @endforeach
                </select>
                @error('wallet_id')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-end lg:col-span-2">
                <button type="submit"
                        class="w-full inline-flex items-center justify-center px-4 py-2 bg-gradient-to-r from-emerald-600 to-green-500 text-white rounded-lg hover:from-emerald-700 hover:to-green-600 shadow-md hover:shadow-lg transition-all duration-200">
                    <i class="fas fa-check-circle mr-2"></i>
                    <span x-text="activateAsFree ? 'تفعيل مجاني' : 'تفعيل + فاتورة'"></span>
                </button>
            </div>
            </div>
        </form>
        <script>
        function quickActivateForm(courses) {
            const map = Object.fromEntries((courses || []).map(c => [String(c.id), c]));
            return {
                courseId: '{{ old('advanced_course_id', '') }}',
                listPrice: 0,
                discount: Number('{{ old('discount_amount', 0) }}') || 0,
                finalPrice: Number('{{ old('final_price', '') }}') || 0,
                activateAsFree: {{ old('activate_as_free') ? 'true' : 'false' }},
                paymentMethod: '{{ old('payment_method', 'cash') }}',
                init() { this.onCourseChange(); },
                onFreeToggle() {
                    if (this.activateAsFree) {
                        this.discount = this.listPrice;
                        this.finalPrice = 0;
                    } else {
                        this.updateFinal();
                    }
                },
                onCourseChange() {
                    const course = map[String(this.courseId)] || null;
                    this.listPrice = course ? (Number(course.price) || 0) : 0;
                    if (course) {
                        this.discount = Number(course.course_discount) || 0;
                        this.finalPrice = Number(course.effective_price) || this.listPrice;
                    } else {
                        this.discount = 0;
                        this.finalPrice = 0;
                    }
                    if (this.activateAsFree) {
                        this.discount = this.listPrice;
                        this.finalPrice = 0;
                    }
                },
                updateFinal() {
                    if (this.activateAsFree) {
                        this.discount = this.listPrice;
                        this.finalPrice = 0;
                        return;
                    }
                    this.finalPrice = Math.max(0, this.listPrice - (Number(this.discount) || 0));
                },
            };
        }
        </script>
    </div>

    <!-- البحث والفلترة -->
    <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
            <h3 class="text-lg font-semibold text-gray-900">البحث والفلترة</h3>
            <div class="flex flex-wrap items-center gap-2">
                <form method="POST" action="{{ route('admin.online-enrollments.resync-progress', request()->query()) }}"
                      onsubmit="return confirm('إعادة حساب النسبة الفعلية لكل التسجيلات الظاهرة بالفلتر الحالي؟ قد تنخفض النسب المتضخّمة القديمة.');">
                    @csrf
                    <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-amber-600 text-white rounded-lg hover:bg-amber-700 transition-colors duration-200">
                        <i class="fas fa-sync-alt mr-2"></i>
                        تحديث النسب الفعلية
                    </button>
                </form>
                <a href="{{ route('admin.online-enrollments.export-pdf', request()->query()) }}"
                   class="inline-flex items-center px-4 py-2 bg-rose-600 text-white rounded-lg hover:bg-rose-700 transition-colors duration-200">
                    <i class="fas fa-file-pdf mr-2"></i>
                    طباعة PDF
                </a>
                <a href="{{ route('admin.online-enrollments.export', request()->query()) }}"
                   class="inline-flex items-center px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-colors duration-200">
                    <i class="fas fa-file-excel mr-2"></i>
                    استخراج Excel
                </a>
                <a href="{{ route('admin.online-enrollments.create') }}"
                   class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200">
                    <i class="fas fa-plus mr-2"></i>
                    تسجيل طالب جديد
                </a>
            </div>
        </div>
        
        <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700 mb-2">البحث</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}" 
                       placeholder="البحث بالاسم أو رقم الهاتف..."
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>

            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">الحالة</label>
                <select name="status" id="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">جميع الحالات</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>في الانتظار</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>نشط</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>مكتمل</option>
                    <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>معلق</option>
                </select>
            </div>

            <div>
                <label for="completion" class="block text-sm font-medium text-gray-700 mb-2">اكتمال المنهج</label>
                <select name="completion" id="completion" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                    <option value="">الكل</option>
                    <option value="finished" {{ request('completion') == 'finished' ? 'selected' : '' }}>أنهى المنهج (100%)</option>
                    <option value="in_progress" {{ request('completion') == 'in_progress' ? 'selected' : '' }}>لم يُنه بعد</option>
                </select>
            </div>

            <div>
                <label for="course_id" class="block text-sm font-medium text-gray-700 mb-2">الكورس</label>
                <select name="course_id" id="course_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">جميع الكورسات</option>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}" {{ request('course_id') == $course->id ? 'selected' : '' }}>
                            {{ $course->title }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex gap-2 items-end">
                <button type="submit" class="btn-primary flex-1">
                    <i class="fas fa-search mr-2"></i>
                    بحث
                </button>
                <a href="{{ route('admin.online-enrollments.index') }}" class="btn-secondary">
                    <i class="fas fa-refresh"></i>
                </a>
            </div>
        </form>
    </div>

    @if(request('completion') === 'finished')
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900 flex items-start gap-3">
            <i class="fas fa-certificate mt-0.5 text-emerald-600"></i>
            <div>
                <p class="font-bold">عرض الطلاب الذين أنهوا المنهج (تقدّم 100%)</p>
                <p class="text-emerald-800/90 mt-0.5">هؤلاء جاهزون لموديول إصدار الشهادة التلقائي. الحالة الإدارية قد تظل «نشط» حتى يبقى لهم وصول للكورس.</p>
            </div>
        </div>
    @endif

    <!-- البحث السريع بالهاتف -->
    <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">البحث السريع بالهاتف</h3>
        <div class="flex gap-4">
            <div class="flex-1">
                <input type="text" id="quickSearchPhone" placeholder="أدخل رقم هاتف الطالب أو ولي الأمر..."
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <button type="button" onclick="quickSearchByPhone()" 
                    class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors duration-200">
                <i class="fas fa-search mr-2"></i>
                بحث سريع
            </button>
        </div>
        <div id="quickSearchResult" class="mt-4 hidden">
            <!-- نتائج البحث السريع ستظهر هنا -->
        </div>
    </div>

    <!-- قائمة التسجيلات -->
    <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
        @if($enrollments->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الطالب</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الكورس</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الحالة</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">التقدم</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">تاريخ التسجيل</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($enrollments as $enrollment)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                        <i class="fas fa-user text-blue-600"></i>
                                    </div>
                                    <div class="mr-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $enrollment->student->name }}</div>
                                        @if($enrollment->hide_from_instructor)
                                            <span class="inline-flex mt-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">تفعيل مجاني — مخفي عن المدرب</span>
                                        @endif
                                        <div class="text-sm text-gray-500">{{ $enrollment->student->phone }}</div>
                                        @if($enrollment->student->parent_phone)
                                            <div class="text-xs text-gray-400">ولي الأمر: {{ $enrollment->student->parent_phone }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $enrollment->course->title }}</div>
                                <div class="text-sm text-gray-500">
                                    {{ $enrollment->course->academicYear->name ?? 'غير محدد' }} - 
                                    {{ $enrollment->course->academicSubject->name ?? 'غير محدد' }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                    {{ $enrollment->status_color == 'green' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $enrollment->status_color == 'yellow' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                    {{ $enrollment->status_color == 'blue' ? 'bg-blue-100 text-blue-800' : '' }}
                                    {{ $enrollment->status_color == 'red' ? 'bg-red-100 text-red-800' : '' }}">
                                    {{ $enrollment->status_text }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $pct = (float) ($enrollment->live_progress ?? $enrollment->progress ?? 0);
                                    $finished = $pct >= 100.0 || $enrollment->hasFinishedCurriculum();
                                    $avgWatch = $enrollment->avg_lecture_watch_percent;
                                    $stale = (bool) ($enrollment->progress_was_stale ?? false);
                                @endphp
                                <div class="flex items-center gap-2">
                                    <div class="w-24 bg-gray-200 rounded-full h-2">
                                        <div class="{{ $finished ? 'bg-emerald-500' : 'bg-blue-600' }} h-2 rounded-full" style="width: {{ min($pct, 100) }}%"></div>
                                    </div>
                                    <span class="text-sm font-semibold {{ $finished ? 'text-emerald-700' : 'text-gray-600' }}">{{ number_format($pct, 0) }}%</span>
                                </div>
                                @if($enrollment->live_total !== null)
                                    <p class="text-[10px] text-slate-500 mt-1">{{ $enrollment->live_completed }} / {{ $enrollment->live_total }} عناصر</p>
                                @endif
                                @if($avgWatch !== null)
                                    <p class="text-[10px] text-sky-700 mt-0.5">متوسط مشاهدة الفيديو: {{ number_format($avgWatch, 0) }}%</p>
                                @endif
                                @if($finished)
                                    <span class="inline-flex mt-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                        أنهى المنهج
                                        @if($enrollment->curriculum_completed_at)
                                            · {{ $enrollment->curriculum_completed_at->format('d/m/Y') }}
                                        @endif
                                    </span>
                                @elseif($stale)
                                    <span class="inline-flex mt-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">تم تصحيح نسبة قديمة</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $enrollment->enrolled_at->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('admin.online-enrollments.show', $enrollment) }}" 
                                       class="text-blue-600 hover:text-blue-900">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    
                                    @if($enrollment->status === 'pending')
                                        <form method="POST" action="{{ route('admin.online-enrollments.activate', $enrollment) }}" class="inline">
                                            @csrf
                                            <button type="submit" class="text-green-600 hover:text-green-900" 
                                                    onclick="return confirm('هل تريد تفعيل هذا التسجيل؟')"
                                                    title="تفعيل التسجيل">
                                                <i class="fas fa-play"></i>
                                            </button>
                                        </form>
                                    @elseif($enrollment->status === 'active')
                                        <form method="POST" action="{{ route('admin.online-enrollments.deactivate', $enrollment) }}" class="inline">
                                            @csrf
                                            <button type="submit" class="text-orange-600 hover:text-orange-900" 
                                                    onclick="return confirm('هل تريد إيقاف هذا التسجيل؟')"
                                                    title="إيقاف التسجيل">
                                                <i class="fas fa-pause"></i>
                                            </button>
                                        </form>
                                    @elseif($enrollment->status === 'suspended')
                                        <form method="POST" action="{{ route('admin.online-enrollments.activate', $enrollment) }}" class="inline">
                                            @csrf
                                            <button type="submit" class="text-emerald-600 hover:text-emerald-900" 
                                                    onclick="return confirm('هل تريد إعادة تفعيل هذا التسجيل وفتح الكورس للطالب مرة أخرى؟')"
                                                    title="إعادة تفعيل التسجيل">
                                                <i class="fas fa-redo"></i>
                                            </button>
                                        </form>
                                    @endif
                                    
                                    <form method="POST" action="{{ route('admin.online-enrollments.destroy', $enrollment) }}" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900" 
                                                onclick="return confirm('هل تريد حذف هذا التسجيل؟')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $enrollments->appends(request()->query())->links() }}
            </div>
        @else
            <div class="p-12 text-center">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-users text-gray-400 text-2xl"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">لا توجد تسجيلات</h3>
                <p class="text-gray-500 mb-4">لم يتم العثور على تسجيلات تطابق معايير البحث</p>
                <a href="{{ route('admin.online-enrollments.create') }}" 
                   class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200">
                    <i class="fas fa-plus mr-2"></i>
                    إضافة أول تسجيل
                </a>
            </div>
        @endif
    </div>
</div>

<script>
function quickSearchByPhone() {
    const phone = document.getElementById('quickSearchPhone').value.trim();
    const resultDiv = document.getElementById('quickSearchResult');
    
    if (!phone) {
        alert('يرجى إدخال رقم الهاتف');
        return;
    }
    
    // إظهار loader
    resultDiv.innerHTML = '<div class="text-center py-4"><i class="fas fa-spinner fa-spin text-blue-600"></i> جاري البحث...</div>';
    resultDiv.classList.remove('hidden');
    
    fetch(`{{ route('admin.online-enrollments.search-by-phone') }}?phone=${encodeURIComponent(phone)}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const student = data.student;
                resultDiv.innerHTML = `
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                        <h4 class="font-medium text-green-900 mb-2">تم العثور على الطالب:</h4>
                        <div class="text-sm">
                            <p><strong>الاسم:</strong> ${student.name}</p>
                            <p><strong>هاتف الطالب:</strong> ${student.phone}</p>
                            ${student.parent_phone ? `<p><strong>هاتف ولي الأمر:</strong> ${student.parent_phone}</p>` : ''}
                        </div>
                        <div class="mt-3">
                            <a href="{{ route('admin.online-enrollments.create') }}?student_id=${student.id}" 
                               class="inline-flex items-center px-3 py-1 bg-green-600 text-white text-sm rounded hover:bg-green-700">
                                <i class="fas fa-plus mr-1"></i>
                                تسجيل في كورس
                            </a>
                        </div>
                    </div>
                `;
            } else {
                resultDiv.innerHTML = `
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                        <h4 class="font-medium text-red-900">${data.error}</h4>
                    </div>
                `;
            }
        })
        .catch(error => {
            resultDiv.innerHTML = `
                <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                    <h4 class="font-medium text-red-900">حدث خطأ في البحث</h4>
                </div>
            `;
        });
}

// البحث عند الضغط على Enter
document.getElementById('quickSearchPhone').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        quickSearchByPhone();
    }
});
</script>
@endsection
