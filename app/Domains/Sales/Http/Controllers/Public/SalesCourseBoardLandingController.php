<?php

namespace App\Domains\Sales\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Domains\Sales\Models\SalesCourseBoardEntry;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SalesCourseBoardLandingController extends Controller
{
    /**
     * /course-info/{slug} يتحوّل لصفحة الكورس (ربط يدوي أو مطابقة بالاسم).
     */
    public function show(string $slug): View|RedirectResponse
    {
        $entry = SalesCourseBoardEntry::query()
            ->where('slug', $slug)
            ->where('is_active', true)
            ->where('landing_published', true)
            ->with('advancedCourse')
            ->firstOrFail();

        $course = $entry->resolvedPublicCourse();
        if ($course) {
            return redirect()->route('public.course.show', $course->id);
        }

        return view('sales::public.course-board.show', compact('entry'));
    }
}
