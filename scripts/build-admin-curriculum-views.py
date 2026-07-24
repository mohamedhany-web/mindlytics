from pathlib import Path

src = Path('resources/views/instructor/curriculum/index.blade.php')
dst = Path('resources/views/admin/curriculum/builder.blade.php')
text = src.read_text(encoding='utf-8')

reps = [
    ("@extends('layouts.app')", "@extends('layouts.admin')"),
    ('instructor.curriculum.partials', 'admin.curriculum.partials'),
    ("route('instructor.lectures.index')", "route('admin.lectures.index')"),
    ("route('instructor.courses.index')", "route('admin.curriculum.hub')"),
    ("route('instructor.learning-patterns.index', $course)", "route('admin.advanced-courses.show', $course)"),
    ('instructor.courses.curriculum.assignments.store', 'admin.advanced-courses.curriculum.assignments.store'),
    ('instructor.courses.curriculum.exams.store', 'admin.advanced-courses.curriculum.exams.store'),
    ('instructor.courses.sections.order', 'admin.advanced-courses.sections.order'),
    ('instructor.lectures.store', 'admin.lectures.store'),
    ("url('/instructor/lectures')", "url('/admin/lectures')"),
    ('url("instructor/curriculum-items")', 'url("admin/curriculum-items")'),
    ('/instructor/', '/admin/'),
    ("__('instructor.build_curriculum')", "'بناء المنهج'"),
    ("__('instructor.lectures')", "'المحاضرات'"),
    ("__('instructor.back')", "'رجوع'"),
]
for a, b in reps:
    text = text.replace(a, b)

text = text.replace(
    '/admin/courses/{{ $course->id }}/sections',
    '/admin/advanced-courses/{{ $course->id }}/sections',
)

# Insert unlock policy banner after header block opening content
banner = '''
    <!-- سياسة فتح الفيديوهات (أدمن) -->
    <div class="rounded-2xl p-5 bg-white border border-slate-200 shadow-sm">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            <div>
                <h2 class="text-lg font-bold text-slate-800">قيود الفيديوهات للطلاب</h2>
                <p class="text-sm text-slate-500 mt-1">تحكم في فتح كل محاضرات الكورس بدون قيود التسلسل، أو الإبقاء على إعدادات المدرب العادية.</p>
            </div>
            <form method="POST" action="{{ route('admin.advanced-courses.unlock-policy', $course) }}" class="flex flex-wrap items-center gap-3">
                @csrf
                @method('PUT')
                <input type="hidden" name="admin_unlock_all_videos" value="0">
                <label class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50 cursor-pointer">
                    <input type="checkbox" name="admin_unlock_all_videos" value="1" class="rounded text-teal-600"
                           {{ !empty($course->admin_unlock_all_videos) ? 'checked' : '' }}
                           onchange="this.form.submit()">
                    <span class="text-sm font-semibold text-slate-700">فتح كل الفيديوهات بدون قيود</span>
                </label>
                <span class="text-xs font-medium {{ !empty($course->admin_unlock_all_videos) ? 'text-teal-700' : 'text-slate-500' }}">
                    {{ !empty($course->admin_unlock_all_videos) ? 'الوضع الحالي: مفتوح بالكامل' : 'الوضع الحالي: قيود عادية' }}
                </span>
            </form>
        </div>
    </div>
'''

needle = '<div class="space-y-6">\n    <!-- الهيدر -->'
if needle in text:
    text = text.replace(needle, '<div class="space-y-6">\n' + banner + '\n    <!-- الهيدر -->', 1)

dst.write_text(text, encoding='utf-8')
print('builder', dst.stat().st_size)

sec_src = Path('resources/views/instructor/curriculum/partials/section.blade.php')
sec_dst = Path('resources/views/admin/curriculum/partials/section.blade.php')
st = sec_src.read_text(encoding='utf-8')
st = st.replace('instructor.curriculum.partials', 'admin.curriculum.partials')
st = st.replace('/instructor/', '/admin/')
st = st.replace("route('instructor.learning-patterns.create', $course)", "route('admin.advanced-courses.show', $course)")
st = st.replace("route('instructor.assignments.edit', $item->item)", "route('admin.assignments.edit', $item->item)")
st = st.replace("route('instructor.exams.edit', $item->item)", "route('admin.exams.edit', $item->item)")
st = st.replace("route('instructor.learning-patterns.edit', [$course, $item->item])", "route('admin.advanced-courses.show', $course)")
sec_dst.write_text(st, encoding='utf-8')
print('section ok')
