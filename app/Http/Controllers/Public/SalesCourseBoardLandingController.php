<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\SalesCourseBoardEntry;
use Illuminate\View\View;

class SalesCourseBoardLandingController extends Controller
{
    public function show(string $slug): View
    {
        $entry = SalesCourseBoardEntry::query()
            ->where('slug', $slug)
            ->where('is_active', true)
            ->where('landing_published', true)
            ->firstOrFail();

        return view('public.sales-course-board.show', compact('entry'));
    }
}
