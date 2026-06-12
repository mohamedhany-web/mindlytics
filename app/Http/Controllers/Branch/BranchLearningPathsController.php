<?php

namespace App\Http\Controllers\Branch;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BranchLearningPathsController extends Controller
{
    public function index(Request $request): View
    {
        $branch = auth()->user()->branch;
        abort_unless($branch, 404);

        $q = AcademicYear::query()
            ->where('branch_id', $branch->id)
            ->withCount('academicSubjects')
            ->ordered();

        if ($request->filled('q')) {
            $s = '%'.trim((string) $request->input('q')).'%';
            $q->where(function ($sub) use ($s) {
                $sub->where('name', 'like', $s)->orWhere('code', 'like', $s);
            });
        }

        $paths = $q->paginate(25)->withQueryString();

        return view('branch-office.learning-paths', compact('branch', 'paths'));
    }
}
