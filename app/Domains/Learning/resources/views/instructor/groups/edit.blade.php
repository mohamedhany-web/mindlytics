@extends('layouts.app')

@section('title', __('instructor.edit_group_title') . ' - ' . $group->name)
@section('header', __('instructor.edit_group_title'))

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 border border-gray-200 dark:border-gray-700">
        <div class="mb-6">
            <nav class="text-sm text-gray-500 dark:text-gray-400 mb-2">
                <a href="{{ route('instructor.groups.index') }}" class="hover:text-sky-600">{{ __('instructor.groups') }}</a>
                <span class="mx-2">/</span>
                <a href="{{ route('instructor.groups.show', $group) }}" class="hover:text-sky-600">{{ $group->name }}</a>
                <span class="mx-2">/</span>
                <span>{{ __('common.edit') }}</span>
            </nav>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('instructor.edit_group_title') }}</h1>
        </div>

        <form action="{{ route('instructor.groups.update', $group) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- الكورس -->
            <div>
                <label for="course_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    {{ __('instructor.course_label') }} <span class="text-red-500">*</span>
                </label>
                <select name="course_id" id="course_id" required
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500 dark:bg-gray-700 dark:text-white">
                    <option value="">{{ __('instructor.choose_course_option') }}</option>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}" {{ old('course_id', $group->course_id) == $course->id ? 'selected' : '' }}>
                            {{ $course->title }}
                        </option>
                    @endforeach
                </select>
                @error('course_id')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- اسم المجموعة -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    {{ __('instructor.group_name_required') }} <span class="text-red-500">*</span>
                </label>
                <input type="text" name="name" id="name" value="{{ old('name', $group->name) }}" required
                       class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500 dark:bg-gray-700 dark:text-white"
                       placeholder="{{ __('instructor.group_name_required') }}">
                @error('name')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- الوصف -->
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    {{ __('instructor.description') }}
                </label>
                <textarea name="description" id="description" rows="4"
                          class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500 dark:bg-gray-700 dark:text-white"
                          placeholder="{{ __('instructor.group_description_placeholder') }}">{{ old('description', $group->description) }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- القائد -->
            <div>
                <label for="leader_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    {{ __('instructor.group_leader_label') }}
                </label>
                <select name="leader_id" id="leader_id"
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500 dark:bg-gray-700 dark:text-white">
                    <option value="">{{ __('instructor.no_leader_short') }}</option>
                    @foreach($group->members as $member)
                        <option value="{{ $member->id }}" {{ old('leader_id', $group->leader_id) == $member->id ? 'selected' : '' }}>
                            {{ $member->name }}
                        </option>
                    @endforeach
                </select>
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ __('instructor.can_choose_leader_hint') }}</p>
                @error('leader_id')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- الحد الأقصى للأعضاء -->
            <div>
                <label for="max_members" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    {{ __('instructor.max_members_label') }} <span class="text-red-500">*</span>
                </label>
                <input type="number" name="max_members" id="max_members" value="{{ old('max_members', $group->max_members) }}" 
                       min="2" max="50" required
                       class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500 dark:bg-gray-700 dark:text-white">
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ __('instructor.min_max_members_hint') }}</p>
                @error('max_members')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- الحالة -->
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    {{ __('common.status') }} <span class="text-red-500">*</span>
                </label>
                <select name="status" id="status" required
                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-sky-500 focus:border-sky-500 dark:bg-gray-700 dark:text-white">
                    <option value="active" {{ old('status', $group->status) == 'active' ? 'selected' : '' }}>{{ __('instructor.active') }}</option>
                    <option value="inactive" {{ old('status', $group->status) == 'inactive' ? 'selected' : '' }}>{{ __('instructor.inactive') }}</option>
                    <option value="archived" {{ old('status', $group->status) == 'archived' ? 'selected' : '' }}>{{ __('instructor.archived') }}</option>
                </select>
                @error('status')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <!-- الأزرار -->
            <div class="flex items-center justify-end gap-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                <a href="{{ route('instructor.groups.show', $group) }}" 
                   class="px-6 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    {{ __('common.cancel') }}
                </a>
                <button type="submit" 
                        class="px-6 py-2 bg-gradient-to-r from-sky-600 to-sky-700 hover:from-sky-700 hover:to-sky-800 text-white rounded-lg font-medium transition-colors shadow-lg shadow-sky-500/30">
                    <i class="fas fa-save ml-2"></i>
                    {{ __('instructor.save_changes') }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

