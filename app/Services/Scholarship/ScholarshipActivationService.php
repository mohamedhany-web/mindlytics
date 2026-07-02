<?php

namespace App\Services\Scholarship;

use App\Mail\CourseEnrollmentActivatedMail;
use App\Models\ScholarshipRegistration;
use App\Models\StudentCourseEnrollment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class ScholarshipActivationService
{
    public function activate(ScholarshipRegistration $registration): StudentCourseEnrollment
    {
        if ($registration->status === ScholarshipRegistration::STATUS_REJECTED) {
            throw new \InvalidArgumentException('لا يمكن تفعيل تسجيل مرفوض.');
        }

        $program = $registration->program()->with('course')->firstOrFail();
        $course = $program->course;
        if (! $course) {
            throw new \RuntimeException('كورس المنحة غير موجود.');
        }

        return DB::transaction(function () use ($registration, $program, $course) {
            $enrollment = StudentCourseEnrollment::updateOrCreate(
                [
                    'user_id' => $registration->user_id,
                    'advanced_course_id' => $course->id,
                ],
                [
                    'status' => 'active',
                    'enrolled_at' => now(),
                    'activated_at' => now(),
                    'activated_by' => Auth::id(),
                    'original_price' => 0,
                    'discount_amount' => 0,
                    'final_price' => 0,
                    'payment_method' => 'free',
                    'enrollment_type' => 'scholarship',
                    'hide_from_instructor' => false,
                    'scholarship_registration_id' => $registration->id,
                ],
            );

            $registration->update([
                'status' => ScholarshipRegistration::STATUS_ACTIVATED,
                'activated_at' => now(),
                'activated_by' => Auth::id(),
                'student_course_enrollment_id' => $enrollment->id,
            ]);

            $freshEnrollment = $enrollment->fresh(['student', 'course']);

            try {
                if ($freshEnrollment->student?->email) {
                    Mail::to($freshEnrollment->student->email)
                        ->send(new CourseEnrollmentActivatedMail($freshEnrollment));
                }
            } catch (\Throwable $e) {
                report($e);
            }

            return $freshEnrollment;
        });
    }

    public function deactivate(ScholarshipRegistration $registration): void
    {
        DB::transaction(function () use ($registration) {
            if ($registration->enrollment) {
                $registration->enrollment->update(['status' => 'suspended']);
            }

            $registration->update([
                'status' => ScholarshipRegistration::STATUS_DEACTIVATED,
            ]);
        });
    }

    public function reject(ScholarshipRegistration $registration, ?string $notes = null): void
    {
        DB::transaction(function () use ($registration, $notes) {
            if ($registration->enrollment && $registration->enrollment->status === 'active') {
                $registration->enrollment->update(['status' => 'suspended']);
            }

            $registration->update([
                'status' => ScholarshipRegistration::STATUS_REJECTED,
                'notes' => $notes ?? $registration->notes,
            ]);
        });
    }
}
