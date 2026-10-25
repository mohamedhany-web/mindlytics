<?php

namespace App\Domains\Sales\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Domains\Sales\Models\SalesCourseBoardEntry;
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

        return view('sales::public.course-board.show', compact('entry'));
    }
}
