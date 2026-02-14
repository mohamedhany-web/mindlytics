<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\InstructorProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PersonalBrandingController extends Controller
{
    public function edit()
    {
        $user = auth()->user();
        if (!$user->isInstructor()) {
            abort(403);
        }
        $profile = InstructorProfile::firstOrCreate(
            ['user_id' => $user->id],
            ['status' => InstructorProfile::STATUS_DRAFT]
        );
        return view('instructor.personal-branding.edit', compact('profile'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();
        if (!$user->isInstructor()) {
            abort(403);
        }
        $profile = InstructorProfile::firstOrCreate(
            ['user_id' => $user->id],
            ['status' => InstructorProfile::STATUS_DRAFT]
        );

        $data = $request->validate([
            'headline' => 'nullable|string|max:255',
            'bio' => 'nullable|string|max:5000',
            'experience' => 'nullable|string|max:50000',
            'skills' => 'nullable|string|max:5000',
            'photo' => 'nullable|image|max:2048',
        ], [
            'experience.max' => 'الخبرات في المجال يجب ألا تتجاوز 50 ألف حرف. إن احتجت مساحة أكبر تواصل مع الإدارة.',
            'skills.max' => 'المهارات يجب ألا تتجاوز 5 آلاف حرف.',
            'photo.image' => 'الملف الذي تم رفعه يجب أن يكون صورة',
            'photo.max' => 'حجم الصورة يجب ألا يتجاوز 2 ميجابايت',
        ]);

        if ($request->hasFile('photo')) {
            if ($profile->photo_path && Storage::disk('public')->exists($profile->photo_path)) {
                Storage::disk('public')->delete($profile->photo_path);
            }
            $data['photo_path'] = $request->file('photo')->store('instructor-profiles', 'public');
        }

        unset($data['photo']);
        $profile->update($data);

        return back()->with('success', 'تم حفظ الملف التعريفي.');
    }

    public function submit()
    {
        $user = auth()->user();
        if (!$user->isInstructor()) {
            abort(403);
        }
        $profile = InstructorProfile::where('user_id', $user->id)->firstOrFail();
        if ($profile->status !== InstructorProfile::STATUS_DRAFT && $profile->status !== InstructorProfile::STATUS_REJECTED) {
            return back()->with('error', 'الملف مقدم مسبقاً أو معتمد.');
        }
        $profile->update([
            'status' => InstructorProfile::STATUS_PENDING_REVIEW,
            'submitted_at' => now(),
            'rejection_reason' => null,
        ]);
        return back()->with('success', 'تم إرسال الملف التعريفي للمراجعة. سيتم إعلامك بعد مراجعته من الإدارة.');
    }
}
