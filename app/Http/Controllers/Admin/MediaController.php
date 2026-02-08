<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MediaGallery;
use Illuminate\Http\Request;

class MediaController extends Controller
{
    public function index(Request $request)
    {
        $query = MediaGallery::query();

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $media = $query->orderBy('created_at', 'desc')->paginate(24);
        $categories = MediaGallery::distinct()->pluck('category')->filter()->values();

        return view('admin.media.index', compact('media', 'categories'));
    }

    public function create()
    {
        $categories = MediaGallery::distinct()->pluck('category')->filter()->values();
        return view('admin.media.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'type' => 'required|in:image,video',
            'category' => 'nullable|string|max:100',
            'file' => 'required|file',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:1024',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
        ]);

        if ($request->hasFile('file')) {
            try {
                $file = $request->file('file');
                
                // التحقق من وجود الملف وصحته
                if (!$file || !$file->isValid()) {
                    return redirect()->back()
                        ->withInput()
                        ->withErrors(['file' => 'الملف غير صالح أو تالف']);
                }
                
                $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $subFolder = $validated['type'] === 'image' ? 'images' : 'videos';
                $filePath = 'images/media/' . $subFolder . '/' . $fileName;
                
                // إنشاء المجلد إذا لم يكن موجوداً
                $destinationPath = public_path('images/media/' . $subFolder);
                if (!file_exists($destinationPath)) {
                    if (!mkdir($destinationPath, 0755, true) && !is_dir($destinationPath)) {
                        return redirect()->back()
                            ->withInput()
                            ->withErrors(['file' => 'فشل في إنشاء مجلد التخزين']);
                    }
                }
                
                // استخدام copy بدلاً من move_uploaded_file
                $tempPath = $file->getPathname();
                if (!file_exists($tempPath) || !is_readable($tempPath)) {
                    return redirect()->back()
                        ->withInput()
                        ->withErrors(['file' => 'الملف المؤقت غير موجود أو غير قابل للقراءة']);
                }
                
                $newPath = $destinationPath . DIRECTORY_SEPARATOR . $fileName;
                
                // نسخ الملف بدلاً من نقله
                if (!copy($tempPath, $newPath)) {
                    return redirect()->back()
                        ->withInput()
                        ->withErrors(['file' => 'فشل في نسخ الملف']);
                }
                
                // التحقق من أن الملف تم نسخه بنجاح
                if (!file_exists($newPath)) {
                    return redirect()->back()
                        ->withInput()
                        ->withErrors(['file' => 'فشل في التحقق من الملف المنسوخ']);
                }
                
                $validated['file_path'] = $filePath;
                $validated['file_name'] = $file->getClientOriginalName();
                $validated['mime_type'] = $file->getMimeType();
                $validated['file_size'] = filesize($newPath);
            } catch (\Exception $e) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['file' => 'حدث خطأ أثناء رفع الملف: ' . $e->getMessage()]);
            }
        }

        if ($request->hasFile('thumbnail')) {
            try {
                $thumbnail = $request->file('thumbnail');
                
                // التحقق من وجود الملف وصحته
                if (!$thumbnail || !$thumbnail->isValid()) {
                    return redirect()->back()
                        ->withInput()
                        ->withErrors(['thumbnail' => 'الصورة المصغرة غير صالحة أو تالفة']);
                }
                
                $thumbnailName = time() . '_thumb_' . uniqid() . '.' . $thumbnail->getClientOriginalExtension();
                $thumbnailPath = 'images/media/thumbnails/' . $thumbnailName;
                
                // إنشاء المجلد إذا لم يكن موجوداً
                $destinationPath = public_path('images/media/thumbnails');
                if (!file_exists($destinationPath)) {
                    if (!mkdir($destinationPath, 0755, true) && !is_dir($destinationPath)) {
                        return redirect()->back()
                            ->withInput()
                            ->withErrors(['thumbnail' => 'فشل في إنشاء مجلد الصور المصغرة']);
                    }
                }
                
                // استخدام copy بدلاً من move_uploaded_file
                $tempPath = $thumbnail->getPathname();
                if (!file_exists($tempPath) || !is_readable($tempPath)) {
                    return redirect()->back()
                        ->withInput()
                        ->withErrors(['thumbnail' => 'الصورة المصغرة المؤقتة غير موجودة أو غير قابلة للقراءة']);
                }
                
                $newPath = $destinationPath . DIRECTORY_SEPARATOR . $thumbnailName;
                
                // نسخ الملف بدلاً من نقله
                if (!copy($tempPath, $newPath)) {
                    return redirect()->back()
                        ->withInput()
                        ->withErrors(['thumbnail' => 'فشل في نسخ الصورة المصغرة']);
                }
                
                $validated['thumbnail_path'] = $thumbnailPath;
            } catch (\Exception $e) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['thumbnail' => 'حدث خطأ أثناء رفع الصورة المصغرة: ' . $e->getMessage()]);
            }
        }

        if (!isset($validated['is_active'])) {
            $validated['is_active'] = true;
        }

        $validated['uploaded_by'] = auth()->id();

        MediaGallery::create($validated);

        return redirect()->route('admin.media.index')
            ->with('success', 'تم إضافة الملف بنجاح');
    }

    public function show(MediaGallery $medium)
    {
        $media = $medium;
        return view('admin.media.show', compact('media', 'medium'));
    }

    public function edit(MediaGallery $medium)
    {
        $media = $medium;
        $categories = MediaGallery::distinct()->pluck('category')->filter()->values();
        return view('admin.media.edit', compact('media', 'medium', 'categories'));
    }

    public function update(Request $request, MediaGallery $medium)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'type' => 'required|in:image,video',
            'category' => 'nullable|string|max:100',
            'file' => 'nullable|file',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:1024',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
        ]);

        if ($request->hasFile('file')) {
            try {
                // حذف الملف القديم إن وجد
                if ($medium->file_path && file_exists(public_path($medium->file_path))) {
                    @unlink(public_path($medium->file_path));
                }
                
                $file = $request->file('file');
                
                // التحقق من وجود الملف وصحته
                if (!$file || !$file->isValid()) {
                    return redirect()->back()
                        ->withInput()
                        ->withErrors(['file' => 'الملف غير صالح أو تالف']);
                }
                
                $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $subFolder = $validated['type'] === 'image' ? 'images' : 'videos';
                $filePath = 'images/media/' . $subFolder . '/' . $fileName;
                
                // إنشاء المجلد إذا لم يكن موجوداً
                $destinationPath = public_path('images/media/' . $subFolder);
                if (!file_exists($destinationPath)) {
                    if (!mkdir($destinationPath, 0755, true) && !is_dir($destinationPath)) {
                        return redirect()->back()
                            ->withInput()
                            ->withErrors(['file' => 'فشل في إنشاء مجلد التخزين']);
                    }
                }
                
                // استخدام copy بدلاً من move_uploaded_file
                $tempPath = $file->getPathname();
                if (!file_exists($tempPath) || !is_readable($tempPath)) {
                    return redirect()->back()
                        ->withInput()
                        ->withErrors(['file' => 'الملف المؤقت غير موجود أو غير قابل للقراءة']);
                }
                
                $newPath = $destinationPath . DIRECTORY_SEPARATOR . $fileName;
                
                // نسخ الملف بدلاً من نقله
                if (!copy($tempPath, $newPath)) {
                    return redirect()->back()
                        ->withInput()
                        ->withErrors(['file' => 'فشل في نسخ الملف']);
                }
                
                // التحقق من أن الملف تم نسخه بنجاح
                if (!file_exists($newPath)) {
                    return redirect()->back()
                        ->withInput()
                        ->withErrors(['file' => 'فشل في التحقق من الملف المنسوخ']);
                }
                
                $validated['file_path'] = $filePath;
                $validated['file_name'] = $file->getClientOriginalName();
                $validated['mime_type'] = $file->getMimeType();
                $validated['file_size'] = filesize($newPath);
            } catch (\Exception $e) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['file' => 'حدث خطأ أثناء رفع الملف: ' . $e->getMessage()]);
            }
        }

        if ($request->hasFile('thumbnail')) {
            try {
                // حذف الصورة المصغرة القديمة إن وجدت
                if ($medium->thumbnail_path && file_exists(public_path($medium->thumbnail_path))) {
                    @unlink(public_path($medium->thumbnail_path));
                }
                
                $thumbnail = $request->file('thumbnail');
                
                // التحقق من وجود الملف وصحته
                if (!$thumbnail || !$thumbnail->isValid()) {
                    return redirect()->back()
                        ->withInput()
                        ->withErrors(['thumbnail' => 'الصورة المصغرة غير صالحة أو تالفة']);
                }
                
                $thumbnailName = time() . '_thumb_' . uniqid() . '.' . $thumbnail->getClientOriginalExtension();
                $thumbnailPath = 'images/media/thumbnails/' . $thumbnailName;
                
                // إنشاء المجلد إذا لم يكن موجوداً
                $destinationPath = public_path('images/media/thumbnails');
                if (!file_exists($destinationPath)) {
                    if (!mkdir($destinationPath, 0755, true) && !is_dir($destinationPath)) {
                        return redirect()->back()
                            ->withInput()
                            ->withErrors(['thumbnail' => 'فشل في إنشاء مجلد الصور المصغرة']);
                    }
                }
                
                // استخدام copy بدلاً من move_uploaded_file
                $tempPath = $thumbnail->getPathname();
                if (!file_exists($tempPath) || !is_readable($tempPath)) {
                    return redirect()->back()
                        ->withInput()
                        ->withErrors(['thumbnail' => 'الصورة المصغرة المؤقتة غير موجودة أو غير قابلة للقراءة']);
                }
                
                $newPath = $destinationPath . DIRECTORY_SEPARATOR . $thumbnailName;
                
                // نسخ الملف بدلاً من نقله
                if (!copy($tempPath, $newPath)) {
                    return redirect()->back()
                        ->withInput()
                        ->withErrors(['thumbnail' => 'فشل في نسخ الصورة المصغرة']);
                }
                
                $validated['thumbnail_path'] = $thumbnailPath;
            } catch (\Exception $e) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['thumbnail' => 'حدث خطأ أثناء رفع الصورة المصغرة: ' . $e->getMessage()]);
            }
        }

        $medium->update($validated);

        return redirect()->route('admin.media.index')
            ->with('success', 'تم تحديث الملف بنجاح');
    }

    public function destroy(MediaGallery $medium)
    {
        // حذف الملف من المجلد
        if ($medium->file_path && file_exists(public_path($medium->file_path))) {
            unlink(public_path($medium->file_path));
        }
        
        // حذف الصورة المصغرة من المجلد
        if ($medium->thumbnail_path && file_exists(public_path($medium->thumbnail_path))) {
            unlink(public_path($medium->thumbnail_path));
        }

        $medium->delete();

        return redirect()->route('admin.media.index')
            ->with('success', 'تم حذف الملف بنجاح');
    }
}

