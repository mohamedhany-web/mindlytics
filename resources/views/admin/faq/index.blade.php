@extends('layouts.admin')

@section('title', 'إدارة الأسئلة الشائعة - Mindlytics')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">إدارة الأسئلة الشائعة</h1>
                    <p class="mt-2 text-gray-600">إدارة الأسئلة الشائعة والردود</p>
                </div>
                <div>
                    <a href="{{ route('admin.faq.create') }}" 
                       class="inline-flex items-center px-4 py-2 bg-sky-600 text-white rounded-lg hover:bg-sky-700 transition-colors">
                        <i class="fas fa-plus mr-2"></i>
                        إضافة سؤال جديد
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white shadow rounded-lg mb-6">
            <form method="GET" action="{{ route('admin.faq.index') }}" class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">البحث</label>
                        <input type="text" name="search" value="{{ request('search') }}" 
                               placeholder="السؤال أو الإجابة"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md bg-white text-gray-900">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">الفئة</label>
                        <select name="category" class="w-full px-3 py-2 border border-gray-300 rounded-md bg-white text-gray-900">
                            <option value="">جميع الفئات</option>
                            @foreach($categories as $category)
                            <option value="{{ $category }}" {{ request('category') == $category ? 'selected' : '' }}>{{ $category }}</option>
                            @endforeach
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

        <div class="bg-white shadow rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">
                    الأسئلة ({{ $faqs->total() }})
                </h3>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">السؤال</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الفئة</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الترتيب</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الحالة</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($faqs as $faq)
                        <tr>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ $faq->question }}</div>
                                <div class="text-sm text-gray-500 mt-1">{{ Str::limit($faq->answer, 80) }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $faq->category ?? 'غير محدد' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $faq->order ?? 0 }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                    {{ $faq->is_active ? 'bg-green-100 text-green-800 ': ''bg-red-100 text-red-800 }}">']
                                    {{ $faq->is_active ? 'نشط' : 'غير نشط' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center space-x-2 space-x-reverse">
                                    <a href="{{ route('admin.faq.show', $faq) }}" class="text-blue-600 hover:text-blue-900">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.faq.edit', $faq) }}" class="text-sky-600 hover:text-sky-900">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.faq.destroy', $faq) }}" method="POST" class="inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا السؤال؟');">
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
                                لا توجد أسئلة
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($faqs->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $faqs->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection



