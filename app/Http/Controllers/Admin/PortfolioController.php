<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PortfolioProject;
use Illuminate\Http\Request;

class PortfolioController extends Controller
{
    /**
     * عرض كل مشاريع البورتفوليو (رقابة الجودة)
     */
    public function index(Request $request)
    {
        $query = PortfolioProject::with(['user:id,name,profile_image,email', 'academicYear:id,name', 'advancedCourse:id,title', 'reviewer:id,name']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('visible')) {
            if ($request->visible === '1') {
                $query->where('is_visible', true);
            } else {
                $query->where('is_visible', false);
            }
        }

        $projects = $query->latest()->paginate(20);
        return view('admin.portfolio.index', compact('projects'));
    }

    public function show(PortfolioProject $project)
    {
        $project->load(['user', 'academicYear', 'advancedCourse', 'reviewer']);
        return view('admin.portfolio.show', compact('project'));
    }

    /**
     * إظهار/إخفاء مشروع من البورتفوليو العام (الرقابة)
     */
    public function toggleVisibility(PortfolioProject $project)
    {
        $project->update(['is_visible' => !$project->is_visible]);
        $message = $project->is_visible ? 'تم إظهار المشروع في المعرض.' : 'تم إخفاء المشروع من المعرض.';
        return back()->with('success', $message);
    }
}
