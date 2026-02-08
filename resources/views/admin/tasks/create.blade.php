@extends('layouts.admin')

@section('title', 'إضافة مهمة جديدة')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-xl shadow-lg p-6">
            <h1 class="text-2xl font-bold text-gray-900 mb-6">إضافة مهمة جديدة</h1>

            <form action="{{ route('admin.tasks.store') }}" method="POST">
                @csrf
                
                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">المستخدم *</label>
                        <select name="user_id" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                            <option value="">اختر المستخدم</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('user_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">عنوان المهمة *</label>
                        <input type="text" name="title" value="{{ old('title') }}" required
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                        @error('title')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">الوصف</label>
                        <textarea name="description" rows="4"
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">الأولوية</label>
                            <select name="priority"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                                <option value="low" {{ old('priority', 'medium') == 'low' ? 'selected' : '' }}>منخفضة</option>
                                <option value="medium" {{ old('priority', 'medium') == 'medium' ? 'selected' : '' }}>متوسطة</option>
                                <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>عالية</option>
                                <option value="urgent" {{ old('priority') == 'urgent' ? 'selected' : '' }}>عاجلة</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">تاريخ الاستحقاق</label>
                            <input type="datetime-local" name="due_date" value="{{ old('due_date') }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                            @error('due_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">الربط</label>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <select name="related_type" id="related-type"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                                <option value="">لا يوجد ربط</option>
                                <option value="course">كورس</option>
                                <option value="lecture">محاضرة</option>
                                <option value="assignment">واجب</option>
                                <option value="exam">امتحان</option>
                            </select>
                            <select name="related_id" id="related-id"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                                <option value="">اختر...</option>
                            </select>
                        </div>
                    </div>

                    <div class="flex items-center">
                        <input type="checkbox" name="is_reminder" value="1" {{ old('is_reminder') ? 'checked' : '' }}
                               class="w-4 h-4 text-sky-600 border-gray-300 rounded focus:ring-sky-500">
                        <label class="mr-2 block text-sm text-gray-700">تذكير</label>
                    </div>

                    <div id="reminder-date" style="display: none;">
                        <label class="block text-sm font-medium text-gray-700 mb-2">تاريخ التذكير</label>
                        <input type="datetime-local" name="reminder_at" value="{{ old('reminder_at') }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500">
                    </div>

                    <div class="flex justify-end space-x-4 space-x-reverse">
                        <a href="{{ route('admin.tasks.index') }}" class="btn-secondary">
                            إلغاء
                        </a>
                        <button type="submit" class="btn-primary">
                            <i class="fas fa-save ml-2"></i>
                            حفظ
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.querySelector('input[name="is_reminder"]').addEventListener('change', function() {
    const reminderDate = document.getElementById('reminder-date');
    if (this.checked) {
        reminderDate.style.display = 'block';
    } else {
        reminderDate.style.display = 'none';
    }
});
</script>
@endsection

