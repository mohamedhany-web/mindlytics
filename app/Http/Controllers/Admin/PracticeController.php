<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LearningPattern;
use Illuminate\Http\Request;

class PracticeController extends Controller
{
    public function index(Request $request)
    {
        $query = LearningPattern::query()
            ->with([
                'course:id,title',
                'instructor:id,name',
            ])
            ->latest('id');

        if ($request->filled('course_id')) {
            $query->where('advanced_course_id', $request->integer('course_id'));
        }

        if ($request->filled('type')) {
            $query->where('type', $request->string('type'));
        }

        if ($request->filled('q')) {
            $q = trim((string) $request->input('q'));
            $query->where(function ($sub) use ($q) {
                $sub->where('title', 'like', "%{$q}%")
                    ->orWhere('description', 'like', "%{$q}%");
            });
        }

        $patterns = $query->paginate(20)->withQueryString();
        $availableTypes = LearningPattern::getAvailableTypes();

        return view('admin.practice.index', compact('patterns', 'availableTypes'));
    }

    public function show(LearningPattern $pattern)
    {
        $pattern->load([
            'course',
            'instructor',
        ]);

        $typeInfo = $pattern->getTypeInfo();

        return view('admin.practice.show', compact('pattern', 'typeInfo'));
    }
}

