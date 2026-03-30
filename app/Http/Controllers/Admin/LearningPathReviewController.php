<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LearningPathReview;
use Illuminate\Http\Request;

class LearningPathReviewController extends Controller
{
    public function index(Request $request)
    {
        $query = LearningPathReview::with(['user', 'learningPath'])
            ->orderBy('created_at', 'desc');

        if ($request->filled('rating')) {
            $query->where('rating', $request->rating);
        }

        if ($request->filled('status')) {
            if ($request->status === 'approved') {
                $query->where('is_approved', true);
            } elseif ($request->status === 'pending') {
                $query->where('is_approved', false);
            } elseif ($request->status === 'rejected') {
                $query->where('status', 'rejected');
            }
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('comment', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('learningPath', function ($pq) use ($search) {
                        $pq->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $reviews = $query->paginate(20)->withQueryString();

        $stats = [
            'total' => LearningPathReview::count(),
            'average_rating' => round((float) LearningPathReview::avg('rating'), 2),
            'approved' => LearningPathReview::where('is_approved', true)->count(),
            'pending' => LearningPathReview::where('is_approved', false)->count(),
        ];

        return view('admin.learning-path-reviews.index', compact('reviews', 'stats'));
    }

    public function show(LearningPathReview $learningPathReview)
    {
        $learningPathReview->load(['user', 'learningPath']);
        $review = $learningPathReview;

        return view('admin.learning-path-reviews.show', compact('review'));
    }

    public function update(Request $request, LearningPathReview $learningPathReview)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,approved,rejected',
        ]);

        $learningPathReview->update([
            'is_approved' => $validated['status'] === 'approved',
            'status' => $validated['status'],
        ]);

        return redirect()->route('admin.learning-path-reviews.index')
            ->with('success', 'تم تحديث المراجعة بنجاح');
    }

    public function destroy(LearningPathReview $learningPathReview)
    {
        $learningPathReview->delete();

        return redirect()->route('admin.learning-path-reviews.index')
            ->with('success', 'تم حذف المراجعة بنجاح');
    }
}

