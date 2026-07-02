<?php

namespace App\Http\Controllers\Instructor\Scholarship;

use App\Http\Controllers\Controller;
use App\Models\ScholarshipRegistration;
use App\Services\Scholarship\ScholarshipActivationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class RegistrationController extends Controller
{
    public function activate(ScholarshipRegistration $registration, ScholarshipActivationService $service): RedirectResponse
    {
        $this->authorizeInstructorRegistration($registration);

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
        $this->authorizeInstructorRegistration($registration);

        $service->deactivate($registration);

        return back()->with('success', 'تم إلغاء تفعيل الطالب في المنحة.');
    }

    public function reject(Request $request, ScholarshipRegistration $registration, ScholarshipActivationService $service): RedirectResponse
    {
        $this->authorizeInstructorRegistration($registration);

        $request->validate(['notes' => 'nullable|string|max:1000']);

        $service->reject($registration, $request->notes);

        return back()->with('success', 'تم رفض التسجيل.');
    }

    private function authorizeInstructorRegistration(ScholarshipRegistration $registration): void
    {
        $registration->loadMissing('program');

        if ((int) $registration->program?->instructor_id !== (int) auth()->id()) {
            abort(403, 'غير مصرح لك بإدارة هذا التسجيل.');
        }
    }
}
