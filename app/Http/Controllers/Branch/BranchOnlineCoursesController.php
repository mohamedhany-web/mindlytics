<?php

namespace App\Http\Controllers\Branch;

use App\Http\Controllers\Controller;
use App\Models\AdvancedCourse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BranchOnlineCoursesController extends Controller
{
    public function index(Request $request): View
    {
        $branch = auth()->user()->branch;
        abort_unless($branch, 404);

        $q = AdvancedCourse::query()
            ->where('branch_id', $branch->id)
            ->publicCatalog()
            ->with(['instructor', 'academicSubject', 'academicYear'])
            ->orderByDesc('id');

        if ($request->filled('q')) {
            $s = '%'.trim((string) $request->input('q')).'%';
            $q->where(function ($sub) use ($s) {
                $sub->where('title', 'like', $s)->orWhere('title_en', 'like', $s);
            });
        }

        $courses = $q->paginate(25)->withQueryString();

        return view('branch-office.courses-online', compact('branch', 'courses'));
    }
}
