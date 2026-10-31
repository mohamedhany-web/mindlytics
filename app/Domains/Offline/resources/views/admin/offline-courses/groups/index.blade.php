@extends('layouts.admin')

@section('title', 'مجموعات الكورس الأوفلاين')
@section('header', 'مجموعات الكورس الأوفلاين')

@section('content')
@php $isOnlineOnly = (bool) ($offlineCourse->online_only ?? false); @endphp
<div class="space-y-6" x-data="groupsPage()">
    <!-- الهيدر -->
    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
        <div class="flex flex-wrap justify-between items-center gap-4">
            <div>
                <nav class="text-sm text-gray-500 mb-1">
                    @if($isOnlineOnly)
                        <a href="{{ route('admin.online-management.index') }}" class="hover:text-blue-600">إدارة الأونلاين</a>
                    @else
                        <a href="{{ route('admin.offline-courses.index') }}" class="hover:text-blue-600">الكورسات الأوفلاين</a>
                    @endif
                    <span class="mx-2">/</span>
                    <a href="{{ route('admin.offline-courses.show', $offlineCourse) }}" class="hover:text-blue-600">{{ $offlineCourse->title }}</a>
                    <span class="mx-2">/</span>
                    <span class="text-gray-700 font-semibold">المجموعات</span>
                </nav>
                <h1 class="text-2xl font-bold text-gray-900">مجموعات: {{ $offlineCourse->title }}</h1>
                <p class="text-gray-600 mt-1">
                    @if($isOnlineOnly)
                        إدارة مجموعات وحجز الأونلاين فقط لهذا الكورس
                    @else
                        إدارة مجموعات الكورس الأوفلاين وجدول الجلسات
                    @endif
                </p>
            </div>
            <a href="{{ route('admin.offline-courses.show', $offlineCourse) }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg font-medium transition-colors inline-flex items-center">
                <i class="fas fa-arrow-right mr-2"></i>
                العودة للكورس
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- إضافة مجموعة -->
    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
        <h2 class="text-lg font-bold text-gray-900 mb-4"><i class="fas fa-plus-circle text-blue-600 ml-2"></i>إضافة مجموعة جديدة</h2>
        <form action="{{ route('admin.offline-courses.groups.store', $offlineCourse) }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">اسم المجموعة <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">المدرب <span class="text-red-500">*</span></label>
                <select name="instructor_id" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">اختر المدرب</option>
                    @foreach($instructors as $instructor)
                        <option value="{{ $instructor->id }}" {{ old('instructor_id') == $instructor->id ? 'selected' : '' }}>{{ $instructor->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                @if($isOnlineOnly)
                    <label class="block text-sm font-medium text-gray-700 mb-1">سعة الأونلاين (طلاب) <span class="text-red-500">*</span></label>
                    <input type="hidden" name="max_students" value="0">
                    <input type="number" name="max_students_online" value="{{ old('max_students_online', 30) }}" min="1" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                @else
                    <label class="block text-sm font-medium text-gray-700 mb-1">الحد الأقصى للطلاب <span class="text-red-500">*</span></label>
                    <input type="number" name="max_students" value="{{ old('max_students', 30) }}" min="1" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                @endif
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">المكان</label>
                <select name="location_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">اختر مكان أو اكتب يدوياً</option>
                    @foreach($locations as $loc)
                        <option value="{{ $loc->id }}">{{ $loc->name }} - {{ $loc->address }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">أو عنوان المكان يدوياً</label>
                <input type="text" name="location" value="{{ old('location') }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">مدة الجلسة (ساعات)</label>
                <input type="number" name="session_duration_hours" value="{{ old('session_duration_hours', 2) }}" step="0.5" min="0.5"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">تاريخ البدء</label>
                <input type="date" name="start_date" value="{{ old('start_date') }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">تاريخ الانتهاء</label>
                <input type="date" name="end_date" value="{{ old('end_date') }}"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">الوصف</label>
                <textarea name="description" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">{{ old('description') }}</textarea>
            </div>
            @unless($isOnlineOnly)
            <div class="md:col-span-2 lg:col-span-3 border-t border-gray-200 pt-4 mt-2">
                <p class="text-sm font-semibold text-gray-800 mb-3"><i class="fas fa-globe text-purple-600 ml-2"></i>حجز عبر رابط عام (مثل الورش)</p>
                <input type="hidden" name="public_booking_enabled" value="0">
                <label class="inline-flex items-center gap-2 cursor-pointer mb-3">
                    <input type="checkbox" name="public_booking_enabled" value="1" {{ old('public_booking_enabled') ? 'checked' : '' }} class="rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                    <span class="text-sm text-gray-700">تفعيل صفحة حجز علنية لهذه المجموعة</span>
                </label>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">المسار في الرابط (إنجليزي، شرطات فقط) — اختياري؛ يُولَّد تلقائياً إن تُرك فارغاً</label>
                    <input type="text" name="public_slug" value="{{ old('public_slug') }}" pattern="[a-z0-9]+(?:-[a-z0-9]+)*" placeholder="مثال: cairo-batch-1"
                           class="w-full max-w-lg px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 font-mono text-sm">
                </div>
            </div>
            @else
                <input type="hidden" name="public_booking_enabled" value="0">
            @endunless
            <div class="md:col-span-2 lg:col-span-3 border-t border-gray-200 pt-4 mt-2">
                <p class="text-sm font-semibold text-gray-800 mb-3"><i class="fas fa-video text-indigo-600 ml-2"></i>حجز أونلاين عبر رابط عام</p>
                <input type="hidden" name="online_booking_enabled" value="0">
                <label class="inline-flex items-center gap-2 cursor-pointer mb-3">
                    <input type="checkbox" name="online_booking_enabled" value="1" {{ old('online_booking_enabled', $isOnlineOnly ? '1' : '') ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                    <span class="text-sm text-gray-700">تفعيل صفحة حجز أونلاين لهذه المجموعة</span>
                </label>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">رابط الأونلاين (slug)</label>
                        <input type="text" name="online_slug" value="{{ old('online_slug') }}" pattern="[a-z0-9]+(?:-[a-z0-9]+)*" placeholder="مثال: cairo-batch-1-online"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 font-mono text-sm">
                    </div>
                    @unless($isOnlineOnly)
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">سعة الأونلاين</label>
                        <input type="number" name="max_students_online" value="{{ old('max_students_online', 0) }}" min="0"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                    </div>
                    @endunless
                </div>
            </div>
            <div class="md:col-span-2 lg:col-span-3">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-lg font-medium transition-colors">
                    <i class="fas fa-plus mr-2"></i>إضافة المجموعة
                </button>
            </div>
        </form>
    </div>

    <!-- قائمة المجموعات -->
    @foreach($groups as $group)
    <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
        <div class="p-6 border-b border-gray-200">
            <div class="flex flex-wrap justify-between items-start gap-4">
                <div>
                    <h3 class="text-xl font-bold text-gray-900">
                        <i class="fas fa-users text-purple-600 ml-2"></i>{{ $group->name }}
                    </h3>
                    <div class="flex flex-wrap gap-4 mt-2 text-sm text-gray-600">
                        <span><i class="fas fa-chalkboard-teacher text-blue-500 ml-1"></i> {{ $group->instructor->name ?? '—' }}</span>
                        <span><i class="fas fa-map-marker-alt text-red-500 ml-1"></i> {{ $group->locationModel?->name ?? $group->location ?? '—' }}</span>
                        @if($group->start_date)
                            <span><i class="fas fa-calendar text-indigo-500 ml-1"></i> {{ $group->start_date->format('Y-m-d') }} → {{ $group->end_date?->format('Y-m-d') ?? '—' }}</span>
                        @endif
                        @if($group->session_duration_hours)
                            <span><i class="fas fa-clock text-amber-500 ml-1"></i> {{ $group->session_duration_hours }} ساعة/جلسة</span>
                        @endif
                        <span><i class="fas fa-calendar-check text-teal-500 ml-1"></i> {{ $group->sessions_count ?? $group->sessions->count() }} جلسة — <span class="text-gray-500">موحّدة للقناتين</span></span>
                    </div>
                    @php
                        $pendBook = $group->pendingBookingsCount('offline');
                        $effRem = $group->effectiveAvailableSeats('offline');
                        $pendBookOnline = $group->pendingBookingsCount('online');
                        $effRemOnline = $group->effectiveAvailableSeats('online');
                    @endphp
                    <div class="grid grid-cols-1 {{ $isOnlineOnly ? '' : 'md:grid-cols-2' }} gap-4 mt-4">
                        @unless($isOnlineOnly)
                        <div class="rounded-xl border-2 border-emerald-200 bg-emerald-50/60 p-4">
                            <h4 class="font-bold text-emerald-900 text-sm mb-2"><i class="fas fa-building ml-1"></i> قناة الحضور (أوفلاين)</h4>
                            <p class="text-sm text-gray-800">
                                مسجّلون: <strong>{{ $group->offline_enrollments_count ?? 0 }}</strong>
                                — السعة: <strong>{{ $group->current_students ?? 0 }}/{{ $group->max_students }}</strong>
                            </p>
                            <p class="text-xs text-gray-600 mt-1">حجز عبر الرابط: معلق {{ $pendBook }} · متاح {{ $effRem }}</p>
                            <div class="mt-3 flex flex-wrap gap-2">
                                <a href="{{ route('admin.offline-courses.enrollments.index', [$offlineCourse, 'channel' => 'offline']) }}" class="text-xs font-semibold text-blue-700 hover:underline">تسجيلات الحضور</a>
                                <a href="{{ route('admin.offline-course-bookings.index', ['offline_course_id' => $offlineCourse->id]) }}" class="text-xs font-semibold text-amber-800 hover:underline">حجوزات أوفلاين</a>
                            </div>
                        </div>
                        @endunless
                        <div class="rounded-xl border-2 border-indigo-200 bg-indigo-50/60 p-4">
                            <h4 class="font-bold text-indigo-900 text-sm mb-2"><i class="fas fa-video ml-1"></i> قناة الأونلاين</h4>
                            <p class="text-sm text-gray-800">
                                مسجّلون: <strong>{{ $group->online_enrollments_count ?? 0 }}</strong>
                                — السعة: <strong>{{ $group->current_students_online ?? 0 }}/{{ $group->max_students_online }}</strong>
                            </p>
                            <p class="text-xs text-gray-600 mt-1">حجز أونلاين: معلق {{ $pendBookOnline }} · متاح {{ $effRemOnline }}</p>
                            <div class="mt-3 flex flex-wrap gap-2">
                                <a href="{{ route('admin.offline-courses.enrollments.index', [$offlineCourse, 'channel' => 'online']) }}" class="text-xs font-semibold text-indigo-800 hover:underline">تسجيلات الأونلاين</a>
                                <a href="{{ route('admin.online-course-bookings.index', ['offline_course_id' => $offlineCourse->id]) }}" class="text-xs font-semibold text-amber-800 hover:underline">حجوزات أونلاين</a>
                            </div>
                        </div>
                    </div>
                    @unless($isOnlineOnly)
                    @if($group->public_booking_enabled && $group->public_slug)
                        <div class="mt-3 p-3 bg-purple-50 border border-purple-200 rounded-lg text-sm">
                            <span class="font-semibold text-purple-900"><i class="fas fa-link ml-1"></i> رابط حجز المجموعة:</span>
                            <a href="{{ route('public.offline-groups.show', $group->public_slug) }}" target="_blank" rel="noopener" class="text-blue-600 hover:underline break-all mr-2">{{ route('public.offline-groups.show', $group->public_slug) }}</a>
                        </div>
                    @endif
                    @endunless
                    @if($group->online_booking_enabled && $group->online_slug)
                        <div class="mt-3 p-3 bg-indigo-50 border border-indigo-200 rounded-lg text-sm">
                            <span class="font-semibold text-indigo-900"><i class="fas fa-link ml-1"></i> رابط حجز الأونلاين:</span>
                            <a href="{{ route('public.online-groups.show', $group->online_slug) }}" target="_blank" rel="noopener" class="text-blue-600 hover:underline break-all mr-2">{{ route('public.online-groups.show', $group->online_slug) }}</a>
                        </div>
                    @endif
                </div>
                <div class="flex gap-2">
                    @php
                        $statusClass = ['active' => 'bg-green-100 text-green-800', 'completed' => 'bg-blue-100 text-blue-800', 'cancelled' => 'bg-red-100 text-red-800'];
                        $statusText = ['active' => 'نشط', 'completed' => 'منتهي', 'cancelled' => 'ملغي'];
                    @endphp
                    <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $statusClass[$group->status] ?? 'bg-gray-100 text-gray-800' }}">
                        {{ $statusText[$group->status] ?? $group->status }}
                    </span>
                    <button type="button" @click="openEditModal({{ json_encode($group) }})" class="text-yellow-600 hover:text-yellow-800 font-medium text-sm px-2">
                        <i class="fas fa-edit"></i> تعديل
                    </button>
                    <form action="{{ route('admin.offline-courses.groups.destroy', [$offlineCourse, $group]) }}" method="POST" class="inline" onsubmit="return confirm('هل أنت متأكد من حذف هذه المجموعة؟');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-800 font-medium text-sm px-2">
                            <i class="fas fa-trash"></i> حذف
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- جلسات المجموعة -->
        <div class="p-6">
            <div class="flex flex-wrap justify-between items-center gap-3 mb-4">
                <div>
                    <h4 class="font-bold text-gray-800"><i class="fas fa-calendar-alt text-indigo-600 ml-2"></i>جدول الجلسات ({{ $group->sessions->count() }})</h4>
                    <p class="text-xs text-gray-500 mt-1 mr-1">نفس المواعيد تُطبَّق على الحضور والأونلاين لهذه المجموعة.</p>
                </div>
                <div class="flex gap-2">
                    <button type="button" @click="openBulkModal({{ $group->id }})" class="bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1.5 rounded-lg text-sm font-medium transition-colors">
                        <i class="fas fa-magic mr-1"></i>إنشاء جلسات تلقائياً
                    </button>
                    <button type="button" @click="openSessionModal({{ $group->id }}, {{ $group->instructor_id }})" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1.5 rounded-lg text-sm font-medium transition-colors">
                        <i class="fas fa-plus mr-1"></i>إضافة جلسة
                    </button>
                </div>
            </div>

            @if($group->sessions->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-right font-medium text-gray-500">#</th>
                                <th class="px-4 py-2 text-right font-medium text-gray-500">العنوان</th>
                                <th class="px-4 py-2 text-right font-medium text-gray-500">التاريخ</th>
                                <th class="px-4 py-2 text-right font-medium text-gray-500">الوقت</th>
                                <th class="px-4 py-2 text-right font-medium text-gray-500">المدة</th>
                                <th class="px-4 py-2 text-right font-medium text-gray-500">المكان</th>
                                <th class="px-4 py-2 text-right font-medium text-gray-500">الحالة</th>
                                <th class="px-4 py-2 text-right font-medium text-gray-500">إجراءات</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($group->sessions->sortBy('session_date') as $i => $session)
                            <tr class="{{ $session->status === 'cancelled' ? 'bg-red-50 opacity-60' : '' }}">
                                <td class="px-4 py-2 text-gray-500">{{ $i + 1 }}</td>
                                <td class="px-4 py-2 font-medium text-gray-900">{{ $session->title ?: 'جلسة ' . ($i + 1) }}</td>
                                <td class="px-4 py-2 text-gray-700">{{ $session->session_date->format('Y-m-d') }}</td>
                                <td class="px-4 py-2 text-gray-700">{{ \Carbon\Carbon::parse($session->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($session->end_time)->format('h:i A') }}</td>
                                <td class="px-4 py-2 text-gray-700">{{ $session->duration_minutes }} دقيقة</td>
                                <td class="px-4 py-2 text-gray-700">{{ $session->location ?? '—' }}</td>
                                <td class="px-4 py-2">
                                    @php
                                        $sColors = ['scheduled' => 'bg-blue-100 text-blue-800', 'completed' => 'bg-green-100 text-green-800', 'cancelled' => 'bg-red-100 text-red-800'];
                                        $sTexts = ['scheduled' => 'مجدولة', 'completed' => 'منتهية', 'cancelled' => 'ملغية'];
                                    @endphp
                                    <span class="px-2 py-0.5 text-xs font-semibold rounded-full {{ $sColors[$session->status] ?? '' }}">{{ $sTexts[$session->status] ?? $session->status }}</span>
                                </td>
                                <td class="px-4 py-2">
                                    <form action="{{ route('admin.offline-courses.groups.sessions.destroy', [$offlineCourse, $group, $session]) }}" method="POST" class="inline" onsubmit="return confirm('حذف هذه الجلسة؟');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:text-red-700"><i class="fas fa-trash-alt"></i></button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-8 text-gray-400">
                    <i class="fas fa-calendar-times text-3xl mb-2"></i>
                    <p>لا توجد جلسات مجدولة. أنشئ جلسات يدوياً أو تلقائياً.</p>
                </div>
            @endif
        </div>
    </div>
    @endforeach

    @if($groups->count() === 0)
    <div class="bg-white rounded-xl shadow-lg p-12 text-center text-gray-500 border border-gray-200">
        <i class="fas fa-users-cog text-4xl text-gray-300 mb-3"></i>
        <p>لا توجد مجموعات لهذا الكورس. أضف مجموعة من النموذج أعلاه.</p>
    </div>
    @endif

    <!-- نافذة تعديل المجموعة -->
    <div x-show="showEditModal" x-cloak class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" @click.self="showEditModal = false">
        <div class="bg-white rounded-xl shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <div class="p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">تعديل المجموعة</h3>
                <form :action="editAction" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">اسم المجموعة</label>
                            <input type="text" name="name" x-model="editGroup.name" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">المدرب</label>
                            <select name="instructor_id" x-model="editGroup.instructor_id" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                @foreach($instructors as $instructor)
                                    <option value="{{ $instructor->id }}">{{ $instructor->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        @if($isOnlineOnly)
                            <input type="hidden" name="max_students" value="0">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">سعة الأونلاين</label>
                                <input type="number" name="max_students_online" x-model="editGroup.max_students_online" min="1" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                            </div>
                        @else
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">الحد الأقصى للطلاب</label>
                                <input type="number" name="max_students" x-model="editGroup.max_students" min="1" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            </div>
                        @endif
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">الحالة</label>
                            <select name="status" x-model="editGroup.status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                <option value="active">نشط</option>
                                <option value="completed">منتهي</option>
                                <option value="cancelled">ملغي</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">المكان</label>
                            <select name="location_id" x-model="editGroup.location_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                                <option value="">—</option>
                                @foreach($locations as $loc)
                                    <option value="{{ $loc->id }}">{{ $loc->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">أو العنوان يدوياً</label>
                            <input type="text" name="location" x-model="editGroup.location" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">مدة الجلسة (ساعات)</label>
                            <input type="number" name="session_duration_hours" x-model="editGroup.session_duration_hours" step="0.5" min="0.5" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">تاريخ البدء</label>
                            <input type="date" name="start_date" x-model="editGroup.start_date" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">تاريخ الانتهاء</label>
                            <input type="date" name="end_date" x-model="editGroup.end_date" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">الوصف</label>
                            <textarea name="description" x-model="editGroup.description" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"></textarea>
                        </div>
                        <div class="md:col-span-2 border-t border-gray-200 pt-4 mt-2">
                            @unless($isOnlineOnly)
                            <p class="text-sm font-semibold text-gray-800 mb-2"><i class="fas fa-link text-purple-600 ml-1"></i>رابط الحجز العام</p>
                            <input type="hidden" name="public_booking_enabled" value="0">
                            <label class="inline-flex items-center gap-2 cursor-pointer mb-3">
                                <input type="checkbox" name="public_booking_enabled" value="1" :checked="editGroup.public_booking_enabled == true || editGroup.public_booking_enabled == 1" class="rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                                <span class="text-sm text-gray-700">تفعيل صفحة الحجز العلنية</span>
                            </label>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">المسار (slug)</label>
                                <input type="text" name="public_slug" x-model="editGroup.public_slug" pattern="[a-z0-9]+(?:-[a-z0-9]+)*" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 font-mono text-sm" placeholder="يُولَّد تلقائياً إن فُعِّل الحجز وترك فارغاً">
                            </div>
                            <div class="mt-4 border-t border-gray-200 pt-4">
                            @else
                                <input type="hidden" name="public_booking_enabled" value="0">
                            @endunless
                                <p class="text-sm font-semibold text-gray-800 mb-2"><i class="fas fa-video text-indigo-600 ml-1"></i>رابط الحجز الأونلاين</p>
                                <input type="hidden" name="online_booking_enabled" value="0">
                                <label class="inline-flex items-center gap-2 cursor-pointer mb-3">
                                    <input type="checkbox" name="online_booking_enabled" value="1" :checked="editGroup.online_booking_enabled == true || editGroup.online_booking_enabled == 1" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                    <span class="text-sm text-gray-700">تفعيل صفحة الحجز الأونلاين</span>
                                </label>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">رابط الأونلاين (slug)</label>
                                        <input type="text" name="online_slug" x-model="editGroup.online_slug" pattern="[a-z0-9]+(?:-[a-z0-9]+)*" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 font-mono text-sm">
                                    </div>
                                    @unless($isOnlineOnly)
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">سعة الأونلاين</label>
                                        <input type="number" name="max_students_online" x-model="editGroup.max_students_online" min="0" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                                    </div>
                                    @endunless
                                </div>
                            @unless($isOnlineOnly)
                            </div>
                            @endunless
                        </div>
                    </div>
                    <div class="mt-4 flex justify-end gap-2">
                        <button type="button" @click="showEditModal = false" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded-lg font-medium">إلغاء</button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium">حفظ التعديلات</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- نافذة إضافة جلسة واحدة -->
    <div x-show="showSessionModal" x-cloak class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" @click.self="showSessionModal = false">
        <div class="bg-white rounded-xl shadow-xl max-w-lg w-full">
            <div class="p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4"><i class="fas fa-plus-circle text-green-600 ml-2"></i>إضافة جلسة</h3>
                <form :action="sessionAction" method="POST">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">عنوان الجلسة</label>
                            <input type="text" name="title" placeholder="اختياري" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">التاريخ <span class="text-red-500">*</span></label>
                                <input type="date" name="session_date" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">المكان</label>
                                <input type="text" name="location" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">وقت البدء <span class="text-red-500">*</span></label>
                                <input type="time" name="start_time" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">وقت الانتهاء <span class="text-red-500">*</span></label>
                                <input type="time" name="end_time" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">ملاحظات</label>
                            <textarea name="notes" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"></textarea>
                        </div>
                    </div>
                    <div class="mt-4 flex justify-end gap-2">
                        <button type="button" @click="showSessionModal = false" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded-lg font-medium">إلغاء</button>
                        <button type="submit" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium">إضافة الجلسة</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- نافذة إنشاء جلسات تلقائياً -->
    <div x-show="showBulkModal" x-cloak class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" @click.self="showBulkModal = false">
        <div class="bg-white rounded-xl shadow-xl max-w-lg w-full">
            <div class="p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4"><i class="fas fa-magic text-indigo-600 ml-2"></i>إنشاء جلسات تلقائياً</h3>
                <form :action="bulkAction" method="POST">
                    @csrf
                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">من تاريخ <span class="text-red-500">*</span></label>
                                <input type="date" name="start_date" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">إلى تاريخ <span class="text-red-500">*</span></label>
                                <input type="date" name="end_date" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">أيام الأسبوع <span class="text-red-500">*</span></label>
                            <div class="flex flex-wrap gap-2">
                                @php $dayNames = ['الأحد','الاثنين','الثلاثاء','الأربعاء','الخميس','الجمعة','السبت']; @endphp
                                @foreach($dayNames as $di => $dayName)
                                    <label class="inline-flex items-center gap-1 px-3 py-1.5 border rounded-lg cursor-pointer hover:bg-blue-50 transition-colors">
                                        <input type="checkbox" name="days[]" value="{{ $di }}" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                        <span class="text-sm">{{ $dayName }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">وقت البدء <span class="text-red-500">*</span></label>
                                <input type="time" name="start_time" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">وقت الانتهاء <span class="text-red-500">*</span></label>
                                <input type="time" name="end_time" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">المكان</label>
                            <input type="text" name="location" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                    <div class="mt-4 flex justify-end gap-2">
                        <button type="button" @click="showBulkModal = false" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded-lg font-medium">إلغاء</button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium">إنشاء الجلسات</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function groupsPage() {
    const courseId = {{ $offlineCourse->id }};
    const baseUrl = "{{ url('admin/offline-courses/' . $offlineCourse->id . '/groups') }}";

    return {
        showEditModal: false,
        showSessionModal: false,
        showBulkModal: false,
        editGroup: {},
        editAction: '',
        sessionAction: '',
        bulkAction: '',

        openEditModal(group) {
            this.editGroup = {
                ...group,
                start_date: group.start_date ? group.start_date.split('T')[0] : '',
                end_date: group.end_date ? group.end_date.split('T')[0] : '',
            };
            this.editAction = baseUrl + '/' + group.id;
            this.showEditModal = true;
        },

        openSessionModal(groupId, instructorId) {
            this.sessionAction = baseUrl + '/' + groupId + '/sessions';
            this.showSessionModal = true;
        },

        openBulkModal(groupId) {
            this.bulkAction = baseUrl + '/' + groupId + '/sessions/bulk';
            this.showBulkModal = true;
        }
    };
}
</script>
@endsection
