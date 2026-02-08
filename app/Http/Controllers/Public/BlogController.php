<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function index(Request $request)
    {
        $query = BlogPost::published()->with('author');

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('content', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('tag')) {
            $query->whereJsonContains('tags', $request->tag);
        }

        $posts = $query->orderBy('published_at', 'desc')->paginate(12);
        
        $featuredPosts = BlogPost::published()->featured()->limit(3)->get();
        $tags = BlogPost::published()
            ->whereNotNull('tags')
            ->get()
            ->pluck('tags')
            ->flatten()
            ->unique()
            ->values();

        return view('public.blog.index', compact('posts', 'featuredPosts', 'tags'));
    }

    public function show($slug)
    {
        $post = BlogPost::published()->where('slug', $slug)->firstOrFail();
        
        // زيادة عدد المشاهدات
        $post->increment('views_count');
        
        // مقالات ذات صلة
        $relatedPosts = BlogPost::published()
            ->where('id', '!=', $post->id)
            ->where(function($query) use ($post) {
                if ($post->tags) {
                    foreach ($post->tags as $tag) {
                        $query->orWhereJsonContains('tags', $tag);
                    }
                }
            })
            ->limit(3)
            ->get();

        return view('public.blog.show', compact('post', 'relatedPosts'));
    }
}
