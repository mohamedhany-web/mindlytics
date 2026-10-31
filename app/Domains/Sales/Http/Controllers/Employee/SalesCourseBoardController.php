<?php

namespace App\Domains\Sales\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Domains\Sales\Models\SalesCourseBoardEntry;
use Illuminate\View\View;

class SalesCourseBoardController extends Controller
{
    public function index(): View
    {
        $entries = SalesCourseBoardEntry::query()
            ->active()
            ->ordered()
            ->get();

        $updatedAt = SalesCourseBoardEntry::query()->max('updated_at');

        return view('sales::employee.course-board.index', compact('entries', 'updatedAt'));
    }
}
