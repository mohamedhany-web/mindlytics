<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdvancedCourse;
use App\Models\CourseCommunityComment;
use App\Models\CourseCommunityPost;
use App\Models\CourseCommunityPostImage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * مراقبة منشورات مجتمع الكورسات في تطبيق الطلاب + نشر من لوحة الإدارة.
 */
class AdminCourseCommunityController extends Controller
{
    public function index(Request $request): View
    {
        $q = CourseCommunityPost::query()
            ->with(['user:id,name,email', 'course:id,title', 'images'])
            ->withCount('comments');

        if ($request->filled('course_id')) {
            $q->where('course_id', (int) $request->input('course_id'));
        }
        if ($search = trim((string) $request->input('q'))) {
            $q->where('body', 'like', '%'.$search.'%');
        }

        $posts = $q->latest('is_pinned')->latest('id')->paginate(25)->withQueryString();
        $courses = AdvancedCourse::query()->orderBy('title')->get(['id', 'title']);

        return view('admin.mobile-app.course-community.index', [
            'posts' => $posts,
            'courses' => $courses,
        ]);
    }

    public function create(): View
    {
        $courses = AdvancedCourse::query()->orderBy('title')->get(['id', 'title', 'title_en']);

        return view('admin.mobile-app.course-community.create', [
            'courses' => $courses,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'course_id' => ['required', 'exists:advanced_courses,id'],
            'body' => ['nullable', 'string', 'max:4000'],
            'is_pinned' => ['sometimes', 'boolean'],
            'images' => ['nullable', 'array', 'max:10'],
            'images.*' => ['file', 'image', 'max:8192'],
        ]);

        $body = trim((string) $request->input('body', ''));
        $files = $request->file('images', []);
        if (! is_array($files)) {
            $files = [];
        }
        if ($body === '' && count($files) === 0) {
            return back()->withErrors(['body' => __('admin.course_community_need_body_or_image')])->withInput();
        }

        $post = CourseCommunityPost::create([
            'course_id' => (int) $request->input('course_id'),
            'user_id' => $request->user()->id,
            'body' => $body,
            'is_pinned' => $request->boolean('is_pinned'),
            'edited_at' => null,
        ]);

        $diskName = student_mobile_disk();
        foreach (array_values($files) as $index => $uploaded) {
            if (! $uploaded || ! $uploaded->isValid()) {
                continue;
            }
            $path = $uploaded->storePublicly(
                'community/posts/'.$post->id,
                ['disk' => $diskName]
            );
            CourseCommunityPostImage::create([
                'post_id' => $post->id,
                'path' => $path,
                'disk' => $diskName,
                'sort_order' => $index,
            ]);
        }

        return redirect()
            ->route('admin.mobile-app.course-community.posts.show', $post)
            ->with('success', __('admin.course_community_post_created'));
    }

    public function show(CourseCommunityPost $post): View
    {
        $post->load([
            'user:id,name,email',
            'course:id,title,title_en',
            'images',
            'comments' => fn ($q) => $q->with('user:id,name,email')->orderBy('id'),
        ]);

        return view('admin.mobile-app.course-community.show', [
            'post' => $post,
        ]);
    }

    public function destroyPost(CourseCommunityPost $post): RedirectResponse
    {
        $post->delete();

        return redirect()
            ->route('admin.mobile-app.course-community.index')
            ->with('success', __('admin.course_community_post_deleted'));
    }

    public function destroyComment(CourseCommunityPost $post, CourseCommunityComment $comment): RedirectResponse
    {
        if ($comment->post_id !== $post->id) {
            abort(404);
        }
        $comment->delete();

        return back()->with('success', __('admin.course_community_comment_deleted'));
    }

    public function togglePin(CourseCommunityPost $post): RedirectResponse
    {
        $post->update(['is_pinned' => ! $post->is_pinned]);

        return back()->with('success', $post->is_pinned
            ? __('admin.course_community_pinned')
            : __('admin.course_community_unpinned'));
    }
}
