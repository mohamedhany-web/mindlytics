<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\SalesCourseBoardEntry;
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

        return view('employee.sales.course-board.index', compact('entries', 'updatedAt'));
    }
}
