@extends('layouts.admin')

@section('title', 'تعديل الكورس البرمجي')
@section('header', 'تعديل الكورس')

@section('content')
<div class="px-4 py-8" x-data="courseBuilder({
        tracks: @json($trackOptions ?? []),
        selectedTrack: '{{ old('academic_year_id', $advancedCourse->academic_year_id ?? '') }}',
        selectedSubject: '{{ old('academic_subject_id', $advancedCourse->academic_subject_id ?? '') }}',
        selectedSkills: @json($selectedSkills ?? []),
    })" x-init="init()">
    <div class="w-full max-w-full space-y-8">
        <div class="bg-gradient-to-br from-indigo-500 via-sky-500 to-emerald-500 rounded-3xl p-6 sm:p-8 shadow-xl text-white relative overflow-hidden">
            <div class="absolute inset-y-0 left-0 w-40 bg-white/10 blur-3xl pointer-events-none"></div>
            <div class="relative z-10 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                <div class="space-y-4">
                    <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/15 text-sm font-semibold">
                        <i class="fas fa-edit"></i>
                        تعديل الكورس
                    </div>
                    <h1 class="text-3xl sm:text-4xl font-bold">تعديل بيانات الكورس البرمجي</h1>
                    <p class="text-sm text-white/80 max-w-2xl">
                        قم بتحديث معلومات الكورس والمحتوى التدريبي والمهارات المستهدفة.
                    </p>
                </div>
                <a href="{{ route('admin.advanced-courses.index') }}" class="inline-flex items-center justify-center gap-2 rounded-2xl border border-white/40 px-5 py-2 text-sm font-semibold hover:bg-white/10 transition">
                    <i class="fas fa-arrow-right"></i>
                    العودة للكورسات
                </a>
            </div>
        </div>

        <form action="{{ route('admin.advanced-courses.update', $advancedCourse) }}" method="POST" enctype="multipart/form-data" class="space-y-8">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
                <div class="xl:col-span-2 space-y-6">
                    <div class="bg-white rounded-3xl shadow-xl border border-gray-100/60 overflow-hidden">
                        <div class="border-b border-gray-100 px-6 sm:px-8 py-5">
                            <h2 class="text-lg font-semibold text-gray-900">المعلومات الأساسية</h2>
                            <p class="text-xs text-gray-500 mt-1">املأ تفاصيل الكورس الأساسية. المسار التعليمي ومجموعة المهارات اختياريان.</p>
                        </div>
                        <div class="p-6 sm:p-8 space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="md:col-span-2 space-y-2">
                                    <label class="block text-sm font-semibold text-gray-700">عنوان الكورس *</label>
                                    <input type="text" name="title" value="{{ old('title', $advancedCourse->title) }}" required
                                           class="w-full rounded-2xl border border-gray-200 bg-white/70 px-4 py-3 text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/20 transition"
                                           placeholder="مثال: أساسيات تطوير واجهات الويب">
                                    @error('title') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div class="space-y-2">
                                    <label class="block text-sm font-semibold text-gray-700">المسار التعليمي</label>
                                    <select name="academic_year_id" x-model="formTrack" @change="refreshSubjects"
                                            class="w-full rounded-2xl border border-gray-200 bg-white/70 px-4 py-3 text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/20 transition">
                                        <option value="">بدون مسار (كورس مستقل)</option>
                                        @foreach($academicYears as $year)
                                            <option value="{{ $year->id }}" 
                                                    {{ old('academic_year_id', $advancedCourse->academic_year_id) == $year->id ? 'selected' : '' }}
                                                    x-bind:selected="String({{ $year->id }}) === String(formTrack)">
                                                {{ $year->name }}
                                            </option>
                                        @endforeach
                                        @if(isset($trackOptions) && count($trackOptions) > 0)
                                            <template x-for="track in tracks" :key="track.id">
                                                <option :value="track.id" x-text="track.name" :selected="String(track.id) === String(formTrack)"></option>
                                            </template>
                                        @endif
                                    </select>
                                    @error('academic_year_id') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div class="space-y-2">
                                    <label class="block text-sm font-semibold text-gray-700">مجموعة المهارات (اختياري)</label>
                                    <select name="academic_subject_id" x-model="formSubject" x-ref="subjectSelect"
                                            class="w-full rounded-2xl border border-gray-200 bg-white/70 px-4 py-3 text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/20 transition">
                                        <option value="">اختر المجموعة</option>
                                        @foreach($academicSubjects as $subject)
                                            <option value="{{ $subject->id }}" 
                                                    data-year-id="{{ $subject->academic_year_id }}"
                                                    {{ old('academic_subject_id', $advancedCourse->academic_subject_id) == $subject->id ? 'selected' : '' }}>
                                                {{ $subject->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <p class="text-xs text-gray-500 mt-1" x-show="!formTrack">
                                        <i class="fas fa-info-circle ml-1"></i>
                                        يرجى اختيار المسار التعليمي أولاً
                                    </p>
                                    @error('academic_subject_id') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div class="space-y-2">
                                    <label class="block text-sm font-semibold text-gray-700">المدرّس المسؤول</label>
                                    <select name="instructor_id"
                                            class="w-full rounded-2xl border border-gray-200 bg-white/70 px-4 py-3 text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/20 transition">
                                        <option value="">بدون مدرّس محدد</option>
                                        @foreach($instructors as $instructor)
                                            <option value="{{ $instructor->id }}" {{ old('instructor_id', $advancedCourse->instructor_id) == $instructor->id ? 'selected' : '' }}>
                                                {{ $instructor->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('instructor_id') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div class="space-y-2">
                                    <label class="block text-sm font-semibold text-gray-700">مستوى الكورس</label>
                                    <select name="level"
                                            class="w-full rounded-2xl border border-gray-200 bg-white/70 px-4 py-3 text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/20 transition">
                                        <option value="beginner" {{ old('level', $advancedCourse->level) == 'beginner' ? 'selected' : '' }}>مبتدئ</option>
                                        <option value="intermediate" {{ old('level', $advancedCourse->level) == 'intermediate' ? 'selected' : '' }}>متوسط</option>
                                        <option value="advanced" {{ old('level', $advancedCourse->level) == 'advanced' ? 'selected' : '' }}>متقدم</option>
                                    </select>
                                </div>

                                <div class="space-y-2">
                                    <label class="block text-sm font-semibold text-gray-700">لغة البرمجة</label>
                                    <input list="programming_languages" name="programming_language" value="{{ old('programming_language', $advancedCourse->programming_language) }}"
                                           class="w-full rounded-2xl border border-gray-200 bg-white/70 px-4 py-3 text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/20 transition"
                                           placeholder="مثال: JavaScript">
                                    <datalist id="programming_languages">
                                        @foreach($languages as $language)
                                            <option value="{{ $language }}"></option>
                                        @endforeach
                                    </datalist>
                                </div>

                                <div class="space-y-2">
                                    <label class="block text-sm font-semibold text-gray-700">الإطار / التقنية</label>
                                    <input list="frameworks" name="framework" value="{{ old('framework', $advancedCourse->framework) }}"
                                           class="w-full rounded-2xl border border-gray-200 bg-white/70 px-4 py-3 text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/20 transition"
                                           placeholder="مثال: React">
                                    <datalist id="frameworks">
                                        @foreach($frameworks as $framework)
                                            <option value="{{ $framework }}"></option>
                                        @endforeach
                                    </datalist>
                                </div>

                                <div class="space-y-2">
                                    <label class="block text-sm font-semibold text-gray-700">التصنيف</label>
                                    <input list="categories" name="category" value="{{ old('category', $advancedCourse->category) }}"
                                           class="w-full rounded-2xl border border-gray-200 bg-white/70 px-4 py-3 text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/20 transition"
                                           placeholder="مثال: تطوير الويب">
                                    <datalist id="categories">
                                        @foreach($categories as $category)
                                            <option value="{{ $category }}"></option>
                                        @endforeach
                                    </datalist>
                                </div>

                                <div class="space-y-2">
                                    <label class="block text-sm font-semibold text-gray-700">مدة الكورس (ساعات)</label>
                                    <input type="number" name="duration_hours" value="{{ old('duration_hours', $advancedCourse->duration_hours ?? 0) }}" min="0"
                                           class="w-full rounded-2xl border border-gray-200 bg-white/70 px-4 py-3 text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/20 transition"
                                           placeholder="عدد الساعات">
                                </div>

                                <div class="space-y-2">
                                    <label class="block text-sm font-semibold text-gray-700">مدة إضافية (دقائق)</label>
                                    <input type="number" name="duration_minutes" value="{{ old('duration_minutes', $advancedCourse->duration_minutes ?? 0) }}" min="0" max="59"
                                           class="w-full rounded-2xl border border-gray-200 bg-white/70 px-4 py-3 text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/20 transition"
                                           placeholder="دقائق إضافية">
                                </div>

                                <div class="space-y-2">
                                    <label class="block text-sm font-semibold text-gray-700">سعر الكورس (جنيه)</label>
                                    <input type="number" name="price" value="{{ old('price', $advancedCourse->price ?? 0) }}" min="0" step="0.01"
                                           class="w-full rounded-2xl border border-gray-200 bg-white/70 px-4 py-3 text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/20 transition"
                                           placeholder="0 للمواد المجانية">
                                </div>
                            </div>

                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-gray-700">وصف الكورس</label>
                                <textarea name="description" rows="4"
                                          class="w-full rounded-2xl border border-gray-200 bg-white/70 px-4 py-3 text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/20 transition"
                                          placeholder="اشرح محتوى الكورس وقيمته للطلاب.">{{ old('description', $advancedCourse->description) }}</textarea>
                            </div>

                            <div class="md:col-span-2 space-y-2">
                                <label class="block text-sm font-semibold text-gray-700">
                                    <i class="fas fa-video text-indigo-600 ml-1"></i>
                                    رابط فيديو المقدمة
                                </label>
                                <input type="url" name="video_url" value="{{ old('video_url', $advancedCourse->video_url) }}"
                                       class="w-full rounded-2xl border border-gray-200 bg-white/70 px-4 py-3 text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/20 transition"
                                       placeholder="https://www.youtube.com/watch?v=... أو https://youtu.be/...">
                                <p class="mt-1 text-xs text-gray-500">
                                    رابط فيديو مقدمة الكورس الذي سيتم عرضه في صفحة الكورس. يدعم روابط YouTube و Vimeo.
                                </p>
                                @error('video_url') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                            </div>

                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-gray-700">أهداف الكورس</label>
                                <textarea name="objectives" rows="3"
                                          class="w-full rounded-2xl border border-gray-200 bg-white/70 px-4 py-3 text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/20 transition"
                                          placeholder="الأهداف التعليمية للكورس">{{ old('objectives', $advancedCourse->objectives) }}</textarea>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-2">
                                    <label class="block text-sm font-semibold text-gray-700">تاريخ البداية</label>
                                    <input type="date" name="starts_at" value="{{ old('starts_at', $advancedCourse->starts_at ? $advancedCourse->starts_at->format('Y-m-d') : '') }}"
                                           class="w-full rounded-2xl border border-gray-200 bg-white/70 px-4 py-3 text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/20 transition">
                                </div>
                                <div class="space-y-2">
                                    <label class="block text-sm font-semibold text-gray-700">تاريخ النهاية</label>
                                    <input type="date" name="ends_at" value="{{ old('ends_at', $advancedCourse->ends_at ? $advancedCourse->ends_at->format('Y-m-d') : '') }}"
                                           class="w-full rounded-2xl border border-gray-200 bg-white/70 px-4 py-3 text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/20 transition">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-3xl shadow-xl border border-gray-100/60 overflow-hidden">
                        <div class="border-b border-gray-100 px-6 sm:px-8 py-5">
                            <h2 class="text-lg font-semibold text-gray-900">المهارات والمخرجات</h2>
                        </div>
                        <div class="p-6 sm:p-8 space-y-6">
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-gray-700">المهارات الرئيسية</label>
                                <p class="text-xs text-gray-500 mb-3">
                                    اختر مهارات موجودة أو أضف مهارات جديدة لدعم الفريق أثناء تصميم مسار الكورس.
                                </p>
                                <div class="flex flex-wrap gap-2 mb-3">
                                    @php
                                        $allSkills = \App\Models\AdvancedCourse::whereNotNull('skills')
                                            ->pluck('skills')
                                            ->flatMap(function($value) {
                                                if (is_array($value)) {
                                                    return $value;
                                                }
                                                $decoded = is_string($value) ? json_decode($value, true) : null;
                                                return is_array($decoded) ? $decoded : [];
                                            })
                                            ->unique()
                                            ->values();
                                    @endphp
                                    @foreach($allSkills as $skill)
                                        <button type="button" class="px-3 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-600 border border-slate-200 hover:border-indigo-400 transition"
                                                @click="addSkill('{{ $skill }}')">
                                            {{ $skill }}
                                        </button>
                                    @endforeach
                                </div>
                                <div class="flex items-center gap-2">
                                    <input id="customSkill" type="text" class="flex-1 rounded-2xl border border-gray-200 bg-white/70 px-4 py-2 text-sm text-gray-900 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/20 transition" placeholder="اكتب مهارة جديدة">
                                    <button type="button" class="inline-flex items-center gap-2 rounded-2xl bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 text-sm font-semibold transition"
                                            @click="addSkill(document.getElementById('customSkill').value); document.getElementById('customSkill').value='';">
                                        <i class="fas fa-plus"></i>
                                        إضافة
                                    </button>
                                </div>
                                <div class="flex flex-wrap gap-2 mt-3" x-show="selectedSkills.length">
                                    <template x-for="(skill, index) in selectedSkills" :key="skill">
                                        <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-indigo-100 text-indigo-700 text-xs font-semibold">
                                            <span x-text="skill"></span>
                                            <button type="button" class="text-indigo-600 hover:text-indigo-800" @click="removeSkill(index)">
                                                <i class="fas fa-times"></i>
                                            </button>
                                            <input type="hidden" name="skills[]" :value="skill">
                                        </span>
                                    </template>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-2">
                                    <label class="block text-sm font-semibold text-gray-700">المتطلبات المسبقة</label>
                                    <textarea name="prerequisites" rows="3"
                                              class="w-full rounded-2xl border border-gray-200 bg-white/70 px-4 py-3 text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/20 transition"
                                              placeholder="ما الذي يجب أن يعرفه الطالب قبل بدء الكورس؟">{{ old('prerequisites', $advancedCourse->prerequisites) }}</textarea>
                                </div>
                                <div class="space-y-2">
                                    <label class="block text-sm font-semibold text-gray-700">ما الذي سيتعلمه الطالب؟</label>
                                    <textarea name="what_you_learn" rows="3"
                                              class="w-full rounded-2xl border border-gray-200 bg-white/70 px-4 py-3 text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/20 transition"
                                              placeholder="المخرجات التعليمية والمهارات المكتسبة">{{ old('what_you_learn', $advancedCourse->what_you_learn) }}</textarea>
                                </div>
                            </div>

                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-gray-700">متطلبات إضافية</label>
                                <textarea name="requirements" rows="3"
                                          class="w-full rounded-2xl border border-gray-200 bg-white/70 px-4 py-3 text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/20 transition"
                                          placeholder="أدوات أو برامج يحتاجها الطلاب خلال الدراسة.">{{ old('requirements', $advancedCourse->requirements) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="bg-white rounded-3xl shadow-xl border border-gray-100/60 overflow-hidden">
                        <div class="border-b border-gray-100 px-6 sm:px-8 py-5">
                            <h2 class="text-lg font-semibold text-gray-900">إعدادات العرض</h2>
                        </div>
                        <div class="p-6 sm:p-8 space-y-4 text-sm text-gray-700">
                            <label class="flex items-center justify-between">
                                <span class="font-medium">تفعيل الكورس فوراً</span>
                                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $advancedCourse->is_active) ? 'checked' : '' }} class="w-5 h-5 text-emerald-600 border-gray-300 rounded focus:ring-emerald-500">
                            </label>
                            <label class="flex items-center justify-between">
                                <span class="font-medium">وضع الكورس ضمن الكورسات المميزة</span>
                                <input type="checkbox" name="is_featured" value="1" {{ old('is_featured', $advancedCourse->is_featured) ? 'checked' : '' }} class="w-5 h-5 text-amber-500 border-gray-300 rounded focus:ring-amber-500">
                            </label>
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-gray-700">لغة المحتوى</label>
                                <select name="language"
                                        class="w-full rounded-2xl border border-gray-200 bg-white/70 px-4 py-3 text-gray-900 shadow-sm focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/20 transition">
                                    <option value="ar" {{ old('language', $advancedCourse->language ?? 'ar') == 'ar' ? 'selected' : '' }}>العربية</option>
                                    <option value="en" {{ old('language', $advancedCourse->language) == 'en' ? 'selected' : '' }}>الإنجليزية</option>
                                </select>
                            </div>
                            <div class="space-y-2">
                                <label class="block text-sm font-semibold text-gray-700">رفع صورة للكورس</label>
                                @if($advancedCourse->thumbnail)
                                    <div class="mb-3">
                                        <img src="{{ asset('storage/' . $advancedCourse->thumbnail) }}" alt="صورة الكورس الحالية" 
                                             class="w-full h-32 object-cover rounded-xl border border-gray-200">
                                        <p class="text-xs text-gray-500 mt-1">الصورة الحالية</p>
                                    </div>
                                @endif
                                <input type="file" name="thumbnail" accept="image/*"
                                       class="w-full rounded-2xl border border-gray-200 bg-white/70 px-4 py-2 text-gray-900 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/20 transition">
                                <p class="text-xs text-gray-500">PNG أو JPG بحد أقصى 2MB.</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-3xl shadow-xl border border-gray-100/60 overflow-hidden">
                        <div class="px-6 sm:px-8 py-5 border-b border-gray-100">
                            <h2 class="text-lg font-semibold text-gray-900">ملخص سريع</h2>
                        </div>
                        <div class="p-6 sm:p-8 space-y-3 text-sm text-gray-600">
                            <div class="flex items-center justify-between">
                                <span>المسار التعليمي</span>
                                <span class="font-semibold" x-text="summaryTrack"></span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span>مجموعة المهارات</span>
                                <span class="font-semibold" x-text="summarySubject"></span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span>عدد المهارات المحددة</span>
                                <span class="font-semibold" x-text="selectedSkills.length"></span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span>الحالة</span>
                                <span class="font-semibold">{{ old('is_active', $advancedCourse->is_active) ? 'نشط' : 'مسودة' }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-3xl shadow-xl border border-gray-100/60 overflow-hidden">
                        <div class="p-6 sm:p-8 space-y-3">
                            <button type="submit" class="w-full inline-flex items-center justify-center gap-2 rounded-2xl bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 text-sm font-semibold shadow-lg shadow-indigo-500/20 transition">
                                <i class="fas fa-save"></i>
                                حفظ التعديلات
                            </button>
                            <a href="{{ route('admin.advanced-courses.show', $advancedCourse) }}" class="w-full inline-flex items-center justify-center gap-2 rounded-2xl bg-green-600 hover:bg-green-700 text-white px-6 py-3 text-sm font-semibold transition">
                                <i class="fas fa-eye"></i>
                                عرض الكورس
                            </a>
                            <a href="{{ route('admin.advanced-courses.index') }}" class="w-full inline-flex items-center justify-center gap-2 rounded-2xl border border-gray-200 px-6 py-3 text-sm font-semibold text-gray-700 hover:bg-gray-50 transition">
                                إلغاء
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function courseBuilder({tracks, selectedTrack, selectedSubject, selectedSkills}) {
    return {
        tracks,
        formTrack: selectedTrack ? String(selectedTrack) : '',
        formSubject: selectedSubject ? String(selectedSubject) : '',
        availableSubjects: [],
        selectedSkills: selectedSkills || [],
        get summaryTrack() {
            const track = this.tracks.find(t => String(t.id) === String(this.formTrack));
            return track ? track.name : 'غير محدد';
        },
        get summarySubject() {
            const subject = this.availableSubjects.find(s => String(s.id) === String(this.formSubject));
            return subject ? subject.name : 'غير محدد';
        },
        init() {
            // التأكد من أن tracks موجودة وليست فارغة
            if (!this.tracks || this.tracks.length === 0) {
                console.warn('No tracks available');
                return;
            }
            if (this.formTrack) {
                this.refreshSubjects();
            }
        },
        refreshSubjects() {
            const track = this.tracks.find(t => String(t.id) === String(this.formTrack));
            this.availableSubjects = track ? track.subjects || [] : [];
            
            // إخفاء/إظهار الخيارات في الـ select بناءً على المسار المحدد
            if (this.$refs.subjectSelect) {
                const select = this.$refs.subjectSelect;
                const options = select.querySelectorAll('option[data-year-id]');
                const availableIds = this.availableSubjects.map(s => String(s.id));
                
                options.forEach(option => {
                    const yearId = option.getAttribute('data-year-id');
                    const subjectId = String(option.value);
                    
                    if (!this.formTrack) {
                        // إذا لم يتم اختيار مسار، اعرض جميع الخيارات
                        option.style.display = '';
                    } else {
                        // اعرض فقط الخيارات التي تنتمي للمسار المحدد
                        if (availableIds.includes(subjectId)) {
                            option.style.display = '';
                        } else {
                            option.style.display = 'none';
                        }
                    }
                });
            }
            
            // التحقق من أن القيمة المحددة مسبقاً لا تزال صالحة
            const hasSelected = this.availableSubjects.find(s => String(s.id) === String(this.formSubject));
            if (!hasSelected) {
                // إذا لم تكن القيمة المحددة صالحة، اختر الأولى المتاحة أو اتركها فارغة
                if (this.availableSubjects.length > 0) {
                    this.formSubject = String(this.availableSubjects[0].id);
                } else {
                    this.formSubject = '';
                }
            }
        },
        addSkill(value) {
            const skill = (value || '').trim();
            if (!skill) return;
            if (!this.selectedSkills.includes(skill)) {
                this.selectedSkills.push(skill);
            }
        },
        removeSkill(index) {
            this.selectedSkills.splice(index, 1);
        }
    }
}
</script>
@endpush
@endsection
