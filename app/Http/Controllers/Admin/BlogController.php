<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class BlogController extends Controller
{
    public function index(Request $request)
    {
        $query = BlogPost::query();

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('content', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('status')) {
            if ($request->status === 'published') {
                $query->where('status', 'published');
            } elseif ($request->status === 'draft') {
                $query->where('status', 'draft');
            }
        }

        $posts = $query->with('author')->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.blog.index', compact('posts'));
    }

    public function create()
    {
        return view('admin.blog.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:blog_posts,slug',
            'excerpt' => 'nullable|string|max:500',
            'content' => 'required|string',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|in:draft,published',
            'is_featured' => 'boolean',
            'tags' => 'nullable|array',
            'tags_input' => 'nullable|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
        ]);

        // معالجة الوسوم من tags_input
        if ($request->filled('tags_input')) {
            $tags = array_map('trim', explode(',', $request->tags_input));
            $tags = array_filter($tags);
            $validated['tags'] = array_values($tags);
        } elseif ($request->filled('tags')) {
            $validated['tags'] = $request->tags;
        } else {
            $validated['tags'] = [];
        }

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        if ($request->hasFile('featured_image')) {
            try {
                $image = $request->file('featured_image');
                
                // التحقق من وجود الملف وصحته
                if (!$image || !$image->isValid()) {
                    return redirect()->back()
                        ->withInput()
                        ->withErrors(['featured_image' => 'الصورة غير صالحة أو تالفة']);
                }
                
                $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                $imagePath = 'images/blog/' . $imageName;
                
                // إنشاء المجلد إذا لم يكن موجوداً
                $destinationPath = public_path('images/blog');
                if (!file_exists($destinationPath)) {
                    if (!mkdir($destinationPath, 0755, true) && !is_dir($destinationPath)) {
                        return redirect()->back()
                            ->withInput()
                            ->withErrors(['featured_image' => 'فشل في إنشاء مجلد الصور']);
                    }
                }
                
                // استخدام copy بدلاً من move_uploaded_file
                $tempPath = $image->getPathname();
                if (!file_exists($tempPath) || !is_readable($tempPath)) {
                    return redirect()->back()
                        ->withInput()
                        ->withErrors(['featured_image' => 'الصورة المؤقتة غير موجودة أو غير قابلة للقراءة']);
                }
                
                $newPath = $destinationPath . DIRECTORY_SEPARATOR . $imageName;
                
                // نسخ الملف بدلاً من نقله
                if (!copy($tempPath, $newPath)) {
                    return redirect()->back()
                        ->withInput()
                        ->withErrors(['featured_image' => 'فشل في نسخ الصورة']);
                }
                
                // التحقق من أن الملف تم نسخه بنجاح
                if (!file_exists($newPath)) {
                    return redirect()->back()
                        ->withInput()
                        ->withErrors(['featured_image' => 'فشل في التحقق من الصورة المنسوخة']);
                }
                
                $validated['featured_image'] = $imagePath;
            } catch (\Exception $e) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['featured_image' => 'حدث خطأ أثناء رفع الصورة: ' . $e->getMessage()]);
            }
        }

        if ($validated['status'] === 'published') {
            $validated['published_at'] = now();
        }

        $validated['author_id'] = auth()->id();

        BlogPost::create($validated);

        return redirect()->route('admin.blog.index')
            ->with('success', 'تم إنشاء المقال بنجاح');
    }

    public function show(BlogPost $blog)
    {
        return view('admin.blog.show', compact('blog'));
    }

    public function edit(BlogPost $blog)
    {
        return view('admin.blog.edit', compact('blog'));
    }

    public function update(Request $request, BlogPost $blog)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:blog_posts,slug,' . $blog->id,
            'excerpt' => 'nullable|string|max:500',
            'content' => 'required|string',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|in:draft,published',
            'is_featured' => 'boolean',
            'tags' => 'nullable|array',
            'tags_input' => 'nullable|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
        ]);

        // معالجة الوسوم من tags_input
        if ($request->filled('tags_input')) {
            $tags = array_map('trim', explode(',', $request->tags_input));
            $tags = array_filter($tags);
            $validated['tags'] = array_values($tags);
        } elseif ($request->filled('tags')) {
            $validated['tags'] = $request->tags;
        } else {
            $validated['tags'] = [];
        }

        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        if ($request->hasFile('featured_image')) {
            try {
                // حذف الصورة القديمة إن وجدت
                if ($blog->featured_image && file_exists(public_path($blog->featured_image))) {
                    @unlink(public_path($blog->featured_image));
                }
                
                $image = $request->file('featured_image');
                
                // التحقق من وجود الملف وصحته
                if (!$image || !$image->isValid()) {
                    return redirect()->back()
                        ->withInput()
                        ->withErrors(['featured_image' => 'الصورة غير صالحة أو تالفة']);
                }
                
                $imageName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                $imagePath = 'images/blog/' . $imageName;
                
                // إنشاء المجلد إذا لم يكن موجوداً
                $destinationPath = public_path('images/blog');
                if (!file_exists($destinationPath)) {
                    if (!mkdir($destinationPath, 0755, true) && !is_dir($destinationPath)) {
                        return redirect()->back()
                            ->withInput()
                            ->withErrors(['featured_image' => 'فشل في إنشاء مجلد الصور']);
                    }
                }
                
                // استخدام copy بدلاً من move_uploaded_file
                $tempPath = $image->getPathname();
                if (!file_exists($tempPath) || !is_readable($tempPath)) {
                    return redirect()->back()
                        ->withInput()
                        ->withErrors(['featured_image' => 'الصورة المؤقتة غير موجودة أو غير قابلة للقراءة']);
                }
                
                $newPath = $destinationPath . DIRECTORY_SEPARATOR . $imageName;
                
                // نسخ الملف بدلاً من نقله
                if (!copy($tempPath, $newPath)) {
                    return redirect()->back()
                        ->withInput()
                        ->withErrors(['featured_image' => 'فشل في نسخ الصورة']);
                }
                
                // التحقق من أن الملف تم نسخه بنجاح
                if (!file_exists($newPath)) {
                    return redirect()->back()
                        ->withInput()
                        ->withErrors(['featured_image' => 'فشل في التحقق من الصورة المنسوخة']);
                }
                
                $validated['featured_image'] = $imagePath;
            } catch (\Exception $e) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['featured_image' => 'حدث خطأ أثناء رفع الصورة: ' . $e->getMessage()]);
            }
        }

        if ($validated['status'] === 'published' && !$blog->published_at) {
            $validated['published_at'] = now();
        }

        $blog->update($validated);

        return redirect()->route('admin.blog.index')
            ->with('success', 'تم تحديث المقال بنجاح');
    }

    public function destroy(BlogPost $blog)
    {
        // حذف الصورة من المجلد
        if ($blog->featured_image && file_exists(public_path($blog->featured_image))) {
            unlink(public_path($blog->featured_image));
        }

        $blog->delete();

        return redirect()->route('admin.blog.index')
            ->with('success', 'تم حذف المقال بنجاح');
    }
}

