<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Internship;
use App\Models\InternshipApplication;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\View\View;

class InternshipApplicationController extends Controller
{
    public function index(Request $request): View
    {
        $query = InternshipApplication::query()
            ->with(['internship:id,title,slug', 'reviewer:id,name'])
            ->latest();

        if ($request->filled('status') && array_key_exists($request->status, InternshipApplication::statuses())) {
            $query->where('status', $request->status);
        }
        if ($request->filled('internship_id')) {
            $query->where('internship_id', $request->internship_id);
        }
        if ($request->filled('search')) {
            $s = trim((string) $request->search);
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                    ->orWhere('email', 'like', "%{$s}%")
                    ->orWhere('phone', 'like', "%{$s}%")
                    ->orWhere('university', 'like', "%{$s}%");
            });
        }

        $applications = $query->paginate(20)->withQueryString();
        $internships = Internship::query()->orderBy('title')->get(['id', 'title']);

        $stats = [
            'total' => InternshipApplication::count(),
            'pending' => InternshipApplication::where('status', InternshipApplication::STATUS_PENDING)->count(),
            'reviewed' => InternshipApplication::where('status', InternshipApplication::STATUS_REVIEWED)->count(),
            'accepted' => InternshipApplication::where('status', InternshipApplication::STATUS_ACCEPTED)->count(),
            'rejected' => InternshipApplication::where('status', InternshipApplication::STATUS_REJECTED)->count(),
        ];

        return view('admin.internships.applications.index', compact('applications', 'internships', 'stats'));
    }

    public function show(InternshipApplication $application): View
    {
        $application->load(['internship', 'reviewer:id,name']);

        return view('admin.internships.applications.show', compact('application'));
    }

    public function updateStatus(Request $request, InternshipApplication $application): RedirectResponse
    {
        $data = $request->validate([
            'status' => 'required|in:pending,reviewed,accepted,rejected',
            'admin_notes' => 'nullable|string|max:5000',
        ], [
            'status.in' => 'حالة الطلب غير صالحة.',
        ]);

        $application->update([
            'status' => $data['status'],
            'admin_notes' => $data['admin_notes'] ?? $application->admin_notes,
            'reviewed_at' => now(),
            'reviewed_by' => auth()->id(),
        ]);

        return back()->with('success', 'تم تحديث حالة طلب التدريب.');
    }

    public function destroy(InternshipApplication $application): RedirectResponse
    {
        if ($application->cv_path) {
            $full = public_path($application->cv_path);
            if (File::exists($full)) {
                File::delete($full);
            }
        }

        $application->delete();

        return redirect()
            ->route('admin.internship-applications.index')
            ->with('success', 'تم حذف طلب التقديم.');
    }
}
