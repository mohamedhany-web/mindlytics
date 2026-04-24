

<?php $__env->startSection('title', 'الجروبات الأوفلاين والأونلاين - Mindlytics'); ?>

<?php
    $allGroups = collect();

    foreach ($offlineGroups as $group) {
        $allGroups->push([
            'id' => $group->id,
            'name' => $group->name,
            'course' => $group->course->title ?? 'بدون عنوان',
            'instructor' => $group->course->instructor->name ?? 'غير محدد',
            'location' => $group->locationModel->name ?? $group->location ?? 'غير محدد',
            'mode' => 'offline',
            'mode_label' => 'أوفلاين',
            'seats' => $group->effectiveAvailableSeats('offline'),
            'price' => (float) ($group->course->price ?? 0),
            'booking_open' => (bool) (($group->course?->isOfflineBookingScheduleOpen() ?? false) && $group->canAcceptPublicBooking('offline')),
            'start_at' => optional($group->course?->booking_opens_at)?->format('Y-m-d H:i'),
            'end_at' => optional($group->course?->bookingClosesAtEffective())?->format('Y-m-d H:i'),
            'url' => filled($group->public_slug) ? route('public.offline-groups.show', $group->public_slug) : '#',
        ]);
    }

    foreach ($onlineGroups as $group) {
        $allGroups->push([
            'id' => $group->id,
            'name' => $group->name,
            'course' => $group->course->title ?? 'بدون عنوان',
            'instructor' => $group->course->instructor->name ?? 'غير محدد',
            'location' => 'أونلاين مباشر',
            'mode' => 'online',
            'mode_label' => 'أونلاين',
            'seats' => $group->effectiveAvailableSeats('online'),
            'price' => (float) ($group->course->price ?? 0),
            'booking_open' => (bool) (($group->course?->isOfflineBookingScheduleOpen() ?? false) && $group->canAcceptPublicBooking('online')),
            'start_at' => optional($group->course?->booking_opens_at)?->format('Y-m-d H:i'),
            'end_at' => optional($group->course?->bookingClosesAtEffective())?->format('Y-m-d H:i'),
            'url' => filled($group->online_slug) ? route('public.online-groups.show', $group->online_slug) : '#',
        ]);
    }

    $coursesFilter = $allGroups->pluck('course')->filter()->unique()->values()->all();
?>

<?php $__env->startPush('styles'); ?>
<style>
    .hero-section { background: linear-gradient(to bottom, #f0f9ff, #e0f2fe, #ffffff); }
    .group-card {
        transition: all 0.35s ease;
        border: 1px solid #e2e8f0;
    }
    .group-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 40px rgba(14, 116, 144, 0.16);
        border-color: rgba(59, 130, 246, 0.35);
    }
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div
    x-data='{
        groups: <?php echo json_encode($allGroups->values(), 15, 512) ?>,
        courses: <?php echo json_encode($coursesFilter, 15, 512) ?>,
        search: "",
        mode: "",
        bookingStatus: "",
        courseTitle: "",
        get filteredGroups() {
            return this.groups.filter((g) => {
                const keyword = this.search.toLowerCase().trim();
                const matchesKeyword =
                    !keyword ||
                    g.name.toLowerCase().includes(keyword) ||
                    g.course.toLowerCase().includes(keyword) ||
                    g.instructor.toLowerCase().includes(keyword);

                const matchesMode = !this.mode || g.mode === this.mode;
                const matchesCourse = !this.courseTitle || g.course === this.courseTitle;
                const matchesBooking =
                    !this.bookingStatus ||
                    (this.bookingStatus === "open" && g.booking_open) ||
                    (this.bookingStatus === "closed" && !g.booking_open);

                return matchesKeyword && matchesMode && matchesCourse && matchesBooking;
            });
        }
    }'
>
    <section class="hero-section relative overflow-hidden min-h-[60vh] flex items-center pt-24">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full py-14">
            <div class="text-center">
                <h1 class="text-4xl sm:text-5xl md:text-6xl font-black mb-5 text-gray-900">
                    الجروبات المتاحة <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-green-500">للحجز</span>
                </h1>
                <p class="text-lg md:text-xl text-gray-700 max-w-3xl mx-auto mb-8">
                    نفس تجربة صفحة الكورسات: بطاقات واضحة + فلاتر سريعة + حالة فتح الحجز حسب فترة الكورس.
                </p>

                <div class="max-w-6xl mx-auto bg-white/95 backdrop-blur-xl rounded-2xl p-4 md:p-5 border border-slate-200 shadow-xl">
                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-3">
                        <div class="xl:col-span-2">
                            <input x-model="search" type="text" placeholder="ابحث باسم الجروب أو الكورس أو المدرب..."
                                   class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm md:text-base focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <select x-model="mode" class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm md:text-base focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">كل الأنواع</option>
                                <option value="offline">أوفلاين</option>
                                <option value="online">أونلاين</option>
                            </select>
                        </div>
                        <div>
                            <select x-model="bookingStatus" class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm md:text-base focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">كل حالات الحجز</option>
                                <option value="open">الحجز مفتوح</option>
                                <option value="closed">الحجز مغلق</option>
                            </select>
                        </div>
                    </div>

                    <div class="mt-3">
                        <select x-model="courseTitle" class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm md:text-base focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">كل الكورسات</option>
                            <template x-for="course in courses" :key="course">
                                <option :value="course" x-text="course"></option>
                            </template>
                        </select>
                    </div>

                    <div class="mt-4 text-sm text-slate-600">
                        النتائج: <span class="font-black text-blue-700" x-text="filteredGroups.length"></span> جروب
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-12 md:py-16 bg-gradient-to-b from-gray-50 via-white to-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5 lg:gap-6">
                <template x-for="(group, index) in filteredGroups" :key="group.mode + '-' + group.id">
                    <div class="group-card bg-white rounded-2xl overflow-hidden h-full flex flex-col">
                        <div class="h-40 bg-gradient-to-br from-blue-600 via-blue-500 to-green-500 relative overflow-hidden">
                            <div class="absolute inset-0 bg-black/10"></div>
                            <div class="absolute top-3 right-3">
                                <span class="text-[11px] font-bold px-2.5 py-1 rounded-full"
                                      :class="group.mode === 'online' ? 'bg-emerald-100 text-emerald-700' : 'bg-blue-100 text-blue-700'"
                                      x-text="group.mode_label"></span>
                            </div>
                            <div class="absolute top-3 left-3">
                                <span class="text-[11px] font-bold px-2.5 py-1 rounded-full"
                                      :class="group.booking_open ? 'bg-green-100 text-green-700' : 'bg-rose-100 text-rose-700'"
                                      x-text="group.booking_open ? 'الحجز مفتوح' : 'الحجز مغلق'"></span>
                            </div>
                            <div class="absolute bottom-4 right-4 left-4 text-white">
                                <h3 class="text-xl font-black line-clamp-2" x-text="group.name"></h3>
                                <p class="text-blue-100 text-sm mt-1 line-clamp-2" x-text="group.course"></p>
                            </div>
                        </div>

                        <div class="p-4 md:p-5 flex flex-col flex-grow">
                            <ul class="space-y-2 text-sm text-slate-700 mb-4">
                                <li><i class="fas fa-user-tie text-blue-600 ml-1"></i> <span x-text="group.instructor"></span></li>
                                <li><i class="fas fa-location-dot text-rose-500 ml-1"></i> <span x-text="group.location"></span></li>
                                <li><i class="fas fa-money-bill-wave text-amber-600 ml-1"></i> السعر: <span class="font-bold" x-text="(group.price > 0 ? Math.round(group.price) + ' ج.م' : 'مجاني')"></span></li>
                            </ul>

                            <div class="bg-slate-50 border border-slate-200 rounded-xl p-3 text-xs text-slate-600 mb-4">
                                <div><strong>بداية الحجز:</strong> <span x-text="group.start_at || 'مفتوح دائماً'"></span></div>
                                <div class="mt-1"><strong>نهاية الحجز:</strong> <span x-text="group.end_at || 'غير محددة'"></span></div>
                            </div>

                            <a :href="group.url"
                               class="mt-auto inline-flex items-center justify-center gap-2 w-full bg-gradient-to-r from-blue-600 to-green-500 text-white px-4 py-3 rounded-xl font-bold hover:from-blue-700 hover:to-green-600 transition">
                                <i class="fas fa-calendar-check"></i>
                                عرض التفاصيل والحجز
                            </a>
                        </div>
                    </div>
                </template>
            </div>

            <div x-show="filteredGroups.length === 0" x-cloak class="text-center py-16">
                <div class="max-w-md mx-auto bg-white rounded-2xl border border-slate-200 p-8">
                    <i class="fas fa-search text-4xl text-slate-300 mb-4"></i>
                    <h3 class="text-xl font-bold text-slate-900 mb-2">لا توجد نتائج مطابقة</h3>
                    <p class="text-slate-600">جرّب تغيير الفلاتر أو إزالة جزء من كلمات البحث.</p>
                </div>
            </div>
        </div>
    </section>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.public', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\mindly tics\Mindlytics\resources\views/public/groups.blade.php ENDPATH**/ ?>