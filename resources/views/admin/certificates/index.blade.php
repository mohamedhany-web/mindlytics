@extends('layouts.admin')

@section('title', 'الشهادات')
@section('header', 'الشهادات')

@push('styles')
@include('components.certificate-styles')
<style>
    .template-gallery {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 1.5rem;
    }

    .template-card {
        background: white;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        cursor: pointer;
        border: 3px solid transparent;
    }

    .template-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 12px 24px rgba(0, 0, 0, 0.15);
    }

    .template-card.active {
        border-color: #3b82f6;
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.2);
    }

    .template-preview-container {
        height: 200px;
        position: relative;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .template-preview-mini {
        width: 100%;
        height: 100%;
        transform: scale(0.4);
        transform-origin: center;
        pointer-events: none;
    }

    .template-info {
        padding: 1rem;
        background: linear-gradient(to bottom, rgba(255, 255, 255, 0.95), white);
    }

    .template-name {
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 0.25rem;
    }

    .template-description {
        font-size: 0.75rem;
        color: #6b7280;
    }

    .template-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        padding: 0.25rem 0.5rem;
        background: #eff6ff;
        color: #1e40af;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 600;
        margin-top: 0.5rem;
    }

    .preview-demo {
        position: absolute;
        inset: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.875rem;
        color: rgba(255, 255, 255, 0.9);
        font-weight: 600;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    }
</style>
@endpush

@section('content')
<div class="space-y-6">
    <!-- الهيدر -->
    <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
        <div class="flex justify-between items-center flex-wrap gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">الشهادات</h1>
                <p class="text-gray-600 mt-1">إدارة شهادات الطلاب وتصميمات الشهادات</p>
            </div>
            <div class="flex gap-3">
            <a href="{{ route('admin.certificates.create') }}" 
                   class="bg-gradient-to-r from-sky-600 to-sky-700 hover:from-sky-700 hover:to-sky-800 text-white px-4 py-2 rounded-lg font-medium transition-colors shadow-lg shadow-sky-500/30 inline-flex items-center gap-2">
                    <i class="fas fa-plus"></i>
                    <span>إصدار شهادة جديدة</span>
                </a>
            </div>
        </div>
    </div>

    <!-- التبويبات -->
    <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden" x-data="{ activeTab: 'list' }">
        <div class="border-b border-gray-200">
            <div class="flex">
                <button @click="activeTab = 'list'" 
                        :class="activeTab === 'list' ? 'border-b-2 border-blue-600 text-blue-600 font-semibold' : 'text-gray-600 hover:text-gray-900'"
                        class="px-6 py-4 text-sm font-medium transition-colors">
                    <i class="fas fa-list ml-2"></i>
                    قائمة الشهادات
                </button>
                <button @click="activeTab = 'templates'" 
                        :class="activeTab === 'templates' ? 'border-b-2 border-blue-600 text-blue-600 font-semibold' : 'text-gray-600 hover:text-gray-900'"
                        class="px-6 py-4 text-sm font-medium transition-colors">
                    <i class="fas fa-palette ml-2"></i>
                    تصميمات الشهادات
                </button>
            </div>
        </div>

        <!-- محتوى التبويبات -->
        <div>
            <!-- تبويب قائمة الشهادات -->
            <div x-show="activeTab === 'list'" x-transition class="p-6">
    <!-- الإحصائيات -->
    @if(isset($stats))
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                    <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-6 border border-blue-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="text-sm text-blue-700 font-medium mb-1">إجمالي الشهادات</div>
                                <div class="text-3xl font-black text-blue-900">{{ $stats['total'] ?? 0 }}</div>
                            </div>
                            <div class="w-16 h-16 bg-blue-200 rounded-xl flex items-center justify-center">
                                <i class="fas fa-certificate text-blue-600 text-2xl"></i>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl p-6 border border-green-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="text-sm text-green-700 font-medium mb-1">المُصدرة</div>
                                <div class="text-3xl font-black text-green-900">{{ $stats['issued'] ?? 0 }}</div>
                            </div>
                            <div class="w-16 h-16 bg-green-200 rounded-xl flex items-center justify-center">
                                <i class="fas fa-check-circle text-green-600 text-2xl"></i>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-xl p-6 border border-yellow-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="text-sm text-yellow-700 font-medium mb-1">المعلقة</div>
                                <div class="text-3xl font-black text-yellow-900">{{ $stats['pending'] ?? 0 }}</div>
                            </div>
                            <div class="w-16 h-16 bg-yellow-200 rounded-xl flex items-center justify-center">
                                <i class="fas fa-clock text-yellow-600 text-2xl"></i>
        </div>
        </div>
        </div>
    </div>
    @endif

    <!-- قائمة الشهادات -->
    @if(isset($certificates) && $certificates->count() > 0)
    <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-200">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">رقم الشهادة</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الطالب</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">العنوان</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الكورس</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الحالة</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">تاريخ الإصدار</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($certificates as $certificate)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 font-mono">{{ $certificate->certificate_number }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $certificate->user->name ?? 'غير معروف' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $certificate->title ?? $certificate->course_name ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $certificate->course->title ?? ($certificate->course_name ?? '-') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $status = $certificate->status ?? ($certificate->is_verified ? 'issued' : 'pending');
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if($status == 'issued') bg-green-100 text-green-800
                                @elseif($status == 'pending') bg-yellow-100 text-yellow-800
                                @elseif($status == 'revoked') bg-red-100 text-red-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                @if($status == 'issued') مُصدرة
                                @elseif($status == 'pending') معلقة
                                @elseif($status == 'revoked') ملغاة
                                @else {{ $status }}
                                @endif
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $certificate->issued_at ? $certificate->issued_at->format('Y-m-d') : ($certificate->issue_date ? $certificate->issue_date->format('Y-m-d') : '-') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="{{ route('admin.certificates.show', $certificate) }}" 
                                           class="inline-flex items-center gap-1 text-sky-600 hover:text-sky-900 font-medium transition-colors">
                                            <i class="fas fa-eye"></i>
                                            <span>عرض</span>
                                        </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $certificates->links() }}
        </div>
    </div>
    @else
    <div class="bg-white rounded-xl shadow-lg p-12 text-center border border-gray-200">
                    <div class="w-24 h-24 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-certificate text-gray-400 text-5xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">لا توجد شهادات</h3>
                    <p class="text-gray-600 mb-6">لم يتم إصدار أي شهادات حتى الآن</p>
                    <a href="{{ route('admin.certificates.create') }}" 
                       class="inline-flex items-center gap-2 bg-gradient-to-r from-sky-600 to-sky-700 hover:from-sky-700 hover:to-sky-800 text-white px-6 py-3 rounded-xl font-semibold transition-all duration-300 shadow-lg shadow-sky-500/30">
                        <i class="fas fa-plus"></i>
                        <span>إصدار شهادة جديدة</span>
                    </a>
                </div>
                @endif
            </div>

            <!-- تبويب تصميمات الشهادات -->
            <div x-show="activeTab === 'templates'" x-transition class="p-6">
                <div class="mb-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-2">قوالب الشهادات المتاحة</h2>
                    <p class="text-gray-600">اختر من بين التصميمات الاحترافية المتاحة للشهادات</p>
                </div>

                <div class="template-gallery">
                    <!-- Template 1: Classic -->
                    <div class="template-card" onclick="previewTemplate('classic')">
                        <div class="template-preview-container preview-classic">
                            <div class="preview-demo">كلاسيكي أنيق</div>
                        </div>
                        <div class="template-info">
                            <div class="template-name">كلاسيكي أنيق</div>
                            <div class="template-description">تصميم تقليدي أنيق بحدود زرقاء وتدرجات خفيفة</div>
                            <div class="template-badge">
                                <i class="fas fa-star"></i>
                                <span>الأكثر استخداماً</span>
                            </div>
                        </div>
                    </div>

                    <!-- Template 2: Modern -->
                    <div class="template-card" onclick="previewTemplate('modern')">
                        <div class="template-preview-container preview-modern">
                            <div class="preview-demo">حديث متدرج</div>
                        </div>
                        <div class="template-info">
                            <div class="template-name">حديث متدرج</div>
                            <div class="template-description">خلفية متدرجة متحركة بألوان زاهية وجذابة</div>
                            <div class="template-badge" style="background: #fef3c7; color: #92400e;">
                                <i class="fas fa-palette"></i>
                                <span>تصميم عصري</span>
                            </div>
                        </div>
                    </div>

                    <!-- Template 3: Premium -->
                    <div class="template-card" onclick="previewTemplate('premium')">
                        <div class="template-preview-container preview-premium">
                            <div class="preview-demo">بريميوم ذهبي</div>
                        </div>
                        <div class="template-info">
                            <div class="template-name">بريميوم ذهبي</div>
                            <div class="template-description">تصميم فاخر بحدود ذهبية متدرجة وأنماط زخرفية</div>
                            <div class="template-badge" style="background: #fef3c7; color: #92400e;">
                                <i class="fas fa-crown"></i>
                                <span>بريميوم</span>
                            </div>
                        </div>
                    </div>

                    <!-- Template 4: Tech -->
                    <div class="template-card" onclick="previewTemplate('tech')">
                        <div class="template-preview-container preview-tech">
                            <div class="preview-demo">تقني أزرق</div>
                        </div>
                        <div class="template-info">
                            <div class="template-name">تقني أزرق</div>
                            <div class="template-description">تصميم داكن بخلفية شبكية تقنية وحدود زرقاء متوهجة</div>
                            <div class="template-badge" style="background: #dbeafe; color: #1e40af;">
                                <i class="fas fa-code"></i>
                                <span>تقني</span>
                            </div>
                        </div>
                    </div>

                    <!-- Template 5: Minimal -->
                    <div class="template-card" onclick="previewTemplate('minimal')">
                        <div class="template-preview-container preview-minimal">
                            <div class="preview-demo" style="color: #1f2937;">بسيط نظيف</div>
                        </div>
                        <div class="template-info">
                            <div class="template-name">بسيط نظيف</div>
                            <div class="template-description">تصميم بسيط وأنيق بخلفية بيضاء وحدود داكنة</div>
                            <div class="template-badge" style="background: #f3f4f6; color: #374151;">
                                <i class="fas fa-minimize"></i>
                                <span>بسيط</span>
                            </div>
                        </div>
                    </div>

                    <!-- Template 6: Royal -->
                    <div class="template-card" onclick="previewTemplate('royal')">
                        <div class="template-preview-container preview-royal">
                            <div class="preview-demo">ملكي بنفسجي</div>
                        </div>
                        <div class="template-info">
                            <div class="template-name">ملكي بنفسجي</div>
                            <div class="template-description">تصميم ملكي فاخر بألوان بنفسجية متوهجة</div>
                            <div class="template-badge" style="background: #ede9fe; color: #6d28d9;">
                                <i class="fas fa-crown"></i>
                                <span>ملكي</span>
                            </div>
                        </div>
                    </div>

                    <!-- Template 7: Ocean -->
                    <div class="template-card" onclick="previewTemplate('ocean')">
                        <div class="template-preview-container preview-ocean">
                            <div class="preview-demo">أزرق محيطي</div>
                        </div>
                        <div class="template-info">
                            <div class="template-name">أزرق محيطي</div>
                            <div class="template-description">تدرجات زرقاء متحركة تشبه المحيط</div>
                            <div class="template-badge" style="background: #dbeafe; color: #1e40af;">
                                <i class="fas fa-water"></i>
                                <span>محيطي</span>
                            </div>
                        </div>
                    </div>

                    <!-- Template 8: Elegant -->
                    <div class="template-card" onclick="previewTemplate('elegant')">
                        <div class="template-preview-container preview-elegant">
                            <div class="preview-demo">أنيق داكن</div>
                        </div>
                        <div class="template-info">
                            <div class="template-name">أنيق داكن</div>
                            <div class="template-description">تصميم داكن أنيق بحدود ذهبية متوهجة</div>
                            <div class="template-badge" style="background: #fef3c7; color: #92400e;">
                                <i class="fas fa-gem"></i>
                                <span>أنيق</span>
                            </div>
                        </div>
                    </div>

                    <!-- Template 9: Nature -->
                    <div class="template-card" onclick="previewTemplate('nature')">
                        <div class="template-preview-container preview-nature">
                            <div class="preview-demo" style="color: #065f46;">طبيعي أخضر</div>
                        </div>
                        <div class="template-info">
                            <div class="template-name">طبيعي أخضر</div>
                            <div class="template-description">تصميم طبيعي بألوان خضراء هادئة</div>
                            <div class="template-badge" style="background: #d1fae5; color: #065f46;">
                                <i class="fas fa-leaf"></i>
                                <span>طبيعي</span>
                            </div>
                        </div>
                    </div>

                    <!-- Template 10: Sunset -->
                    <div class="template-card" onclick="previewTemplate('sunset')">
                        <div class="template-preview-container preview-sunset">
                            <div class="preview-demo" style="color: #92400e;">غروب برتقالي</div>
                        </div>
                        <div class="template-info">
                            <div class="template-name">غروب برتقالي</div>
                            <div class="template-description">تصميم دافئ بألوان غروب الشمس البرتقالية</div>
                            <div class="template-badge" style="background: #fed7aa; color: #9a3412;">
                                <i class="fas fa-sun"></i>
                                <span>دافئ</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- معاينة القالب -->
                <div id="template-preview-modal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4" onclick="closePreview()">
                    <div class="bg-white rounded-2xl shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-auto" onclick="event.stopPropagation()">
                        <div class="sticky top-0 bg-white border-b border-gray-200 p-4 flex justify-between items-center z-10">
                            <h3 class="text-xl font-bold text-gray-900">معاينة القالب</h3>
                            <button onclick="closePreview()" class="text-gray-400 hover:text-gray-600 transition-colors">
                                <i class="fas fa-times text-2xl"></i>
                            </button>
                        </div>
                        <div class="p-6">
                            <div id="template-preview-content" class="certificate-container">
                                <!-- سيتم إضافة معاينة القالب هنا -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // معاينة القالب
    function previewTemplate(templateName) {
        const modal = document.getElementById('template-preview-modal');
        const content = document.getElementById('template-preview-content');
        
        // بيانات تجريبية للمعاينة
        const demoData = {
            studentName: 'أحمد محمد علي',
            courseTitle: 'دورة البرمجة المتقدمة',
            courseName: 'Full Stack Development',
            certificateNumber: 'CERT-00000001',
            serialNumber: 'MIND-2024-ABC12345-0001',
            issueDate: '2024-01-15',
            verificationCode: 'CERT123456',
            description: 'هذه شهادة معاينة لعرض تصميم القالب'
        };

        // إنشاء HTML للشهادة
        content.innerHTML = `
            <div class="certificate-template template-${templateName} relative mx-auto" style="max-width: 900px; aspect-ratio: 1.414; padding: 60px; margin: 0 auto;">
                <div class="certificate-watermark">MINDLYTICS</div>
                <div class="certificate-corner corner-top-left" style="border-color: currentColor;"></div>
                <div class="certificate-corner corner-top-right" style="border-color: currentColor;"></div>
                <div class="certificate-corner corner-bottom-left" style="border-color: currentColor;"></div>
                <div class="certificate-corner corner-bottom-right" style="border-color: currentColor;"></div>
                <div class="certificate-seal" style="top: 40px; right: 40px;">
                    <i class="fas fa-certificate text-white text-4xl relative z-10"></i>
                </div>
                <div class="relative z-20 text-center h-full flex flex-col justify-center">
                    <div class="mb-8">
                        <div class="inline-flex items-center justify-center w-24 h-24 rounded-2xl bg-gradient-to-br from-blue-600 via-blue-500 to-blue-700 text-white shadow-2xl mb-6">
                            <span class="text-4xl font-black">M</span>
                        </div>
                        <h3 class="text-2xl font-black text-gray-800 mb-2" style="color: inherit;">Mindlytics</h3>
                        <p class="text-sm font-semibold text-gray-600" style="color: inherit;">أكاديمية البرمجة</p>
                    </div>
                    <div class="mb-12">
                        <h1 class="text-5xl md:text-6xl font-black mb-6" style="color: inherit; text-shadow: 2px 2px 4px rgba(0,0,0,0.1);">شهادة إتمام</h1>
                        <div class="w-32 h-1 mx-auto mb-6" style="background: linear-gradient(90deg, transparent, currentColor, transparent);"></div>
                    </div>
                    <div class="mb-10">
                        <p class="text-lg font-medium text-gray-600 mb-4" style="color: inherit;">هذه الشهادة تمنح إلى</p>
                        <h2 class="text-4xl md:text-5xl font-black mb-4" style="color: inherit; text-shadow: 2px 2px 4px rgba(0,0,0,0.1);">${demoData.studentName}</h2>
                    </div>
                    <div class="mb-10">
                        <p class="text-xl font-semibold text-gray-700 mb-2" style="color: inherit;">لإتمامه بنجاح</p>
                        <h3 class="text-3xl md:text-4xl font-bold mb-4" style="color: inherit;">${demoData.courseTitle}</h3>
                        <p class="text-lg text-gray-600" style="color: inherit;">في: ${demoData.courseName}</p>
                    </div>
                    <div class="mt-auto pt-8 border-t-2" style="border-color: rgba(0,0,0,0.1);">
                        <div class="grid grid-cols-2 gap-6 text-sm">
                            <div>
                                <p class="font-semibold text-gray-700 mb-1" style="color: inherit;">رقم الشهادة</p>
                                <p class="text-gray-600 font-mono" style="color: inherit;">${demoData.certificateNumber}</p>
                            </div>
                            <div>
                                <p class="font-semibold text-gray-700 mb-1" style="color: inherit;">تاريخ الإصدار</p>
                                <p class="text-gray-600" style="color: inherit;">${demoData.issueDate}</p>
                            </div>
                        </div>
                        ${demoData.serialNumber ? `
                        <div class="mt-4">
                            <p class="text-xs text-gray-500" style="color: inherit;">
                                السيريال: <span class="font-mono font-semibold">${demoData.serialNumber}</span>
                            </p>
                        </div>
                        ` : ''}
                    </div>
                    <div class="mt-12 grid grid-cols-2 gap-12">
                        <div>
                            <div class="signature-line"></div>
                            <p class="text-sm font-semibold text-gray-700 mt-2" style="color: inherit;">المدير العام</p>
                            <p class="text-xs text-gray-500" style="color: inherit;">Mindlytics Academy</p>
                        </div>
                        <div>
                            <div class="signature-line"></div>
                            <p class="text-sm font-semibold text-gray-700 mt-2" style="color: inherit;">رئيس الأكاديمية</p>
                            <p class="text-xs text-gray-500" style="color: inherit;">Mindlytics Academy</p>
                        </div>
                    </div>
                </div>
                <div class="absolute inset-0 shimmer-effect pointer-events-none opacity-30"></div>
            </div>
        `;

        modal.classList.remove('hidden');
    }

    function closePreview() {
        document.getElementById('template-preview-modal').classList.add('hidden');
    }

    // إغلاق المعاينة عند الضغط على ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closePreview();
        }
    });
</script>
@endpush
@endsection
