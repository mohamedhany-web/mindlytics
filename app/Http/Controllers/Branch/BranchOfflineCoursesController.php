<?php

namespace App\Http\Controllers\Branch;

use App\Http\Controllers\Controller;
use App\Models\OfflineCourse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BranchOfflineCoursesController extends Controller
{
    public function index(Request $request): View
    {
        $branch = auth()->user()->branch;
        abort_unless($branch, 404);

        $q = OfflineCourse::query()
            ->where('branch_id', $branch->id)
            ->orderByDesc('id');

        if ($request->filled('q')) {
            $s = '%'.trim((string) $request->input('q')).'%';
            $q->where('title', 'like', $s);
        }

        $courses = $q->paginate(25)->withQueryString();

        return view('branch-office.courses-offline', compact('branch', 'courses'));
    }
}
