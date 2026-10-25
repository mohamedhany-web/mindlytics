@extends('layouts.admin')

@section('title', 'إدارة المدونة - Mindlytics')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">إدارة المدونة</h1>
                    <p class="mt-2 text-gray-600">إدارة مقالات المدونة</p>
                </div>
                <div>
                    <a href="{{ route('admin.blog.create') }}" 
                       class="inline-flex items-center px-4 py-2 bg-sky-600 text-white rounded-lg hover:bg-sky-700 transition-colors">
                        <i class="fas fa-plus mr-2"></i>
                        إضافة مقال جديد
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- فلاتر البحث -->
        <div class="bg-white shadow rounded-lg mb-6">
            <form method="GET" action="{{ route('admin.blog.index') }}" class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">البحث</label>
                        <input type="text" name="search" value="{{ request('search') }}" 
                               placeholder="العنوان أو المحتوى"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md bg-white text-gray-900">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">الحالة</label>
                        <select name="status" class="w-full px-3 py-2 border border-gray-300 rounded-md bg-white text-gray-900">
                            <option value="">جميع الحالات</option>
                            <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>منشور</option>
                            <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>مسودة</option>
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="w-full px-4 py-2 bg-sky-600 text-white rounded-md hover:bg-sky-700">
                            <i class="fas fa-search mr-2"></i>
                            بحث
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- جدول المقالات -->
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">
                    المقالات ({{ $posts->total() }})
                </h3>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">المقال</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">المؤلف</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الحالة</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">تاريخ النشر</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($posts as $post)
                        <tr>
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    @if($post->featured_image)
                                    <img src="{{ asset($post->featured_image) }}" alt="{{ $post->title }}" class="h-12 w-12 rounded-lg object-cover ml-3" onerror="this.style.display='none'">
                                    @else
                                    <div class="h-12 w-12 rounded-lg bg-gray-200 flex items-center justify-center ml-3">
                                        <i class="fas fa-image text-gray-400"></i>
                                    </div>
                                    @endif
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ $post->title }}</div>
                                        <div class="text-sm text-gray-500">{{ Str::limit($post->excerpt ?? Str::limit(strip_tags($post->content), 100), 60) }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $post->author->name ?? 'غير محدد' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                    {{ $post->status === 'published' ? 'bg-green-100 text-green-800 ': ''bg-yellow-100 text-yellow-800 }}">']
                                    {{ $post->status === 'published' ? 'منشور' : 'مسودة' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $post->published_at ? $post->published_at->format('Y-m-d') : 'غير منشور' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center space-x-2 space-x-reverse">
                                    <a href="{{ route('admin.blog.show', $post) }}" class="text-blue-600 hover:text-blue-900">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.blog.edit', $post) }}" class="text-sky-600 hover:text-sky-900">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.blog.destroy', $post) }}" method="POST" class="inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا المقال؟');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                لا توجد مقالات
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($posts->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $posts->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

