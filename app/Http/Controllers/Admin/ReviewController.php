<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CourseReview;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
        $query = CourseReview::with(['user', 'course'])
            ->orderBy('created_at', 'desc');

        if ($request->filled('rating')) {
            $query->where('rating', $request->rating);
        }

        if ($request->filled('status')) {
            if ($request->status == 'approved') {
                $query->where('is_approved', true);
            } elseif ($request->status == 'pending') {
                $query->where('is_approved', false);
            } elseif ($request->status == 'rejected') {
                $query->where('status', 'rejected');
            }
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('review', 'like', "%{$search}%")
                  ->orWhere('comment', 'like', "%{$search}%")
                  ->orWhereHas('user', function($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('course', function($cq) use ($search) {
                      $cq->where('title', 'like', "%{$search}%");
                  });
            });
        }

        $reviews = $query->paginate(20);

        $stats = [
            'total' => CourseReview::count(),
            'average_rating' => round(CourseReview::avg('rating'), 2),
            'approved' => CourseReview::where('is_approved', true)->count(),
            'pending' => CourseReview::where('is_approved', false)->count(),
        ];

        return view('admin.reviews.index', compact('reviews', 'stats'));
    }

    public function show(CourseReview $review)
    {
        $review->load(['user', 'course']);
        return view('admin.reviews.show', compact('review'));
    }

    public function update(Request $request, CourseReview $review)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,approved,rejected',
        ]);

        $review->update([
            'is_approved' => $validated['status'] === 'approved',
            'status' => $validated['status'],
        ]);

        return redirect()->route('admin.reviews.index')
            ->with('success', 'تم تحديث المراجعة بنجاح');
    }

    public function destroy(CourseReview $review)
    {
        $review->delete();
        return redirect()->route('admin.reviews.index')
            ->with('success', 'تم حذف المراجعة بنجاح');
    }
}
