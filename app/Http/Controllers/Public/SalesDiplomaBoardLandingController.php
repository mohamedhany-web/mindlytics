<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\SalesDiplomaBoardEntry;
use App\Models\SalesDiplomaBoardVisit;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SalesDiplomaBoardLandingController extends Controller
{
    public function show(Request $request, string $slug): View
    {
        $entry = SalesDiplomaBoardEntry::query()
            ->where('slug', $slug)
            ->where('is_active', true)
            ->where('landing_published', true)
            ->firstOrFail();

        try {
            SalesDiplomaBoardVisit::capture($entry, $request);
        } catch (\Throwable) {
            // Never block the landing page if logging fails.
        }

        return view('public.sales-diploma-board.show', compact('entry'));
    }
}
