<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InstructorProfile;
use Illuminate\Http\Request;

class InstructorPersonalBrandingController extends Controller
{
    public function index(Request $request)
    {
        $query = InstructorProfile::with(['user', 'reviewedByUser']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $profiles = $query->latest('updated_at')->paginate(15)->withQueryString();

        $counts = [
            'pending' => InstructorProfile::pending()->count(),
            'approved' => InstructorProfile::approved()->count(),
            'rejected' => InstructorProfile::where('status', InstructorProfile::STATUS_REJECTED)->count(),
            'draft' => InstructorProfile::where('status', InstructorProfile::STATUS_DRAFT)->count(),
        ];

        return view('admin.marketing.personal-branding.index', compact('profiles', 'counts'));
    }

    public function show(InstructorProfile $personal_branding)
    {
        $personal_branding->load(['user', 'reviewedByUser']);
        return view('admin.marketing.personal-branding.show', compact('personal_branding'));
    }

    public function approve(InstructorProfile $personal_branding)
    {
        if ($personal_branding->status !== InstructorProfile::STATUS_PENDING_REVIEW) {
            return back()->with('error', 'يمكن الموافقة فقط على الملفات قيد المراجعة.');
        }
        $personal_branding->update([
            'status' => InstructorProfile::STATUS_APPROVED,
            'reviewed_at' => now(),
            'reviewed_by' => auth()->id(),
            'rejection_reason' => null,
        ]);
        return back()->with('success', 'تمت الموافقة على الملف التعريفي للمدرب ونشره على الموقع.');
    }

    public function reject(Request $request, InstructorProfile $personal_branding)
    {
        if ($personal_branding->status !== InstructorProfile::STATUS_PENDING_REVIEW) {
            return back()->with('error', 'يمكن رفض فقط الملفات قيد المراجعة.');
        }
        $personal_branding->update([
            'status' => InstructorProfile::STATUS_REJECTED,
            'reviewed_at' => now(),
            'reviewed_by' => auth()->id(),
            'rejection_reason' => $request->input('rejection_reason'),
        ]);
        return back()->with('success', 'تم رفض الملف التعريفي. يمكن للمدرب تعديله وإعادة الإرسال.');
    }

    /**
     * إعادة الملف للمراجعة (من معتمد أو مرفوض).
     */
    public function sendBackForReview(InstructorProfile $personal_branding)
    {
        if (!in_array($personal_branding->status, [InstructorProfile::STATUS_APPROVED, InstructorProfile::STATUS_REJECTED])) {
            return back()->with('error', 'يمكن إعادة المراجعة فقط للملفات المعتمدة أو المرفوضة.');
        }
        $personal_branding->update([
            'status' => InstructorProfile::STATUS_PENDING_REVIEW,
            'reviewed_at' => null,
            'reviewed_by' => null,
            'rejection_reason' => null,
        ]);
        return back()->with('success', 'تم إعادة الملف التعريفي إلى قيد المراجعة.');
    }
}
