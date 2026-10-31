<?php

namespace App\Domains\Scholarship\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Domains\Scholarship\Models\ScholarshipProgram;
use App\Domains\Scholarship\Models\ScholarshipRegistration;
use App\Domains\Scholarship\Services\ScholarshipActivationService;
use App\Domains\Scholarship\Services\ScholarshipStatsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RegistrationController extends Controller
{
    public function index(Request $request, ScholarshipStatsService $stats): View
    {
        $query = ScholarshipRegistration::query()
            ->with(['user', 'program.instructor'])
            ->orderByDesc('registered_at');

        if ($request->filled('search')) {
            $s = trim((string) $request->search);
            $query->where(function ($q) use ($s) {
                $q->whereHas('user', function ($u) use ($s) {
                    $u->where('name', 'like', "%{$s}%")
                        ->orWhere('email', 'like', "%{$s}%")
                        ->orWhere('phone', 'like', "%{$s}%");
                });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('program_id')) {
            $query->where('scholarship_program_id', $request->program_id);
        }

        $registrations = $query->paginate(25)->withQueryString();
        $programs = ScholarshipProgram::orderBy('name')->get(['id', 'name']);
        $stats = $stats->registrationStats();

        return view('scholarship::admin.students.index', compact('registrations', 'programs', 'stats'));
    }

    public function activate(ScholarshipRegistration $registration, ScholarshipActivationService $service): RedirectResponse
    {
        $wasDeactivated = $registration->status === ScholarshipRegistration::STATUS_DEACTIVATED;

        try {
            $service->activate($registration);
        } catch (\Throwable $e) {
            return back()->with('error', 'تعذر التفعيل: ' . $e->getMessage());
        }

        return back()->with('success', $wasDeactivated
            ? 'تم إعادة تفعيل كورس المنحة للطالب.'
            : 'تم تفعيل كورس المنحة للطالب.');
    }

    public function deactivate(ScholarshipRegistration $registration, ScholarshipActivationService $service): RedirectResponse
    {
        $service->deactivate($registration);

        return back()->with('success', 'تم إلغاء تفعيل الطالب في المنحة.');
    }

    public function reject(Request $request, ScholarshipRegistration $registration, ScholarshipActivationService $service): RedirectResponse
    {
        $request->validate(['notes' => 'nullable|string|max:1000']);

        $service->reject($registration, $request->notes);

        return back()->with('success', 'تم رفض التسجيل.');
    }
}
