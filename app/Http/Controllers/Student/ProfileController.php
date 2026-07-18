<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProfileController extends Controller
{
    /**
     * عرض البروفايل
     */
    public function index()
    {
        $user = auth()->user();
        $profileImageUrl = $user->profile_image_url;

        return view('student.profile.index', compact('user', 'profileImageUrl'));
    }

    /**
     * تحديث البروفايل
     */
    public function update(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20|unique:users,phone,' . $user->id,
            'email' => 'nullable|email|max:255|unique:users,email,' . $user->id,
            'current_password' => 'nullable|string',
            'password' => 'nullable|string|min:8|confirmed',
            'profile_image' => 'nullable|image|max:2048',
        ], [
            'name.required' => __('student.profile_name_required'),
            'phone.required' => __('student.profile_phone_required'),
            'phone.unique' => __('student.profile_phone_unique'),
            'email.email' => __('student.profile_email_invalid'),
            'email.unique' => __('student.profile_email_unique'),
            'current_password.required' => __('student.profile_current_password_required'),
            'password.min' => __('student.profile_password_min'),
            'password.confirmed' => __('student.profile_password_confirmed'),
            'profile_image.image' => __('student.profile_image_type'),
            'profile_image.max' => __('student.profile_image_max'),
        ]);

        // التحقق من كلمة المرور الحالية عند تغيير كلمة المرور
        if ($request->filled('password')) {
            if (!$request->filled('current_password') || !Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => __('student.profile_current_password_wrong')]);
            }
        }

        $data = [
            'name' => $request->name,
            'phone' => $request->phone,
        ];

        if ($request->filled('email')) {
            $data['email'] = $request->email;
        }

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        if ($request->hasFile('profile_image')) {
            if ($user->profile_image) {
                if (Storage::disk('public')->exists($user->profile_image)) {
                    Storage::disk('public')->delete($user->profile_image);
                }
                if (File::exists(public_path($user->profile_image))) {
                    File::delete(public_path($user->profile_image));
                }
            }
            $data['profile_image'] = $request->file('profile_image')->store('profile-photos', 'public');
        }

        $user->update($data);

        return back()->with('success', __('student.profile_update_success'));
    }
}