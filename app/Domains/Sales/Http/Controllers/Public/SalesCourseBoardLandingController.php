<?php

namespace App\Domains\Sales\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Domains\Sales\Models\SalesCourseBoardEntry;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SalesCourseBoardLandingController extends Controller
{
    /**
     * روابط قديمة على /course-info/{slug} تتحوّل لصفحة الكورس المربوطة
     * عشان زر «التفاصيل» يفتح عرض الكورس مش لاندنج السيلز.
     */
    public function show(string $slug): View|RedirectResponse
    {
        $entry = SalesCourseBoardEntry::query()
            ->where('slug', $slug)
            ->where('is_active', true)
            ->where('landing_published', true)
            ->with('advancedCourse')
            ->firstOrFail();

        $course = $entry->advancedCourse;
        if ($course && $course->is_active) {
            return redirect()->route('public.course.show', $course->id);
        }

        return view('sales::public.course-board.show', compact('entry'));
    }
}
