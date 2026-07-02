<?php

namespace App\Services\Scholarship;

use App\Models\ScholarshipProgram;
use App\Models\ScholarshipRegistration;
use App\Models\User;
use App\Support\BranchContext;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ScholarshipRegistrationService
{
    /**
     * تسجيل طالب جديد عبر رابط المنحة فقط — بدون إحالة/ورشة/طابور التسجيل العام.
     *
     * @param  array{name: string, country_code?: string, phone: string, email: string, password: string}  $data
     */
    public function registerNewUser(ScholarshipProgram $program, array $data): ScholarshipRegistration
    {
        $branchId = app(BranchContext::class)->id();

        $user = User::create([
            'name' => $data['name'],
            'phone' => $data['phone'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => 'student',
            'is_active' => true,
            'branch_id' => $branchId,
        ]);

        Auth::login($user);

        return $this->attachUserToProgram($program, $user);
    }

    public function attachUserToProgram(ScholarshipProgram $program, User $user): ScholarshipRegistration
    {
        $registration = ScholarshipRegistration::firstOrCreate(
            [
                'scholarship_program_id' => $program->id,
                'user_id' => $user->id,
            ],
            [
                'status' => ScholarshipRegistration::STATUS_REGISTERED,
                'registered_at' => now(),
            ],
        );

        if ($registration->status === ScholarshipRegistration::STATUS_REJECTED) {
            $registration->update([
                'status' => ScholarshipRegistration::STATUS_REGISTERED,
                'registered_at' => now(),
            ]);
        }

        return $registration->fresh(['user', 'program']);
    }
}
