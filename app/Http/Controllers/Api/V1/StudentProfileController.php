<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\ApiUserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class StudentProfileController extends Controller
{
    public function show(Request $request): ApiUserResource
    {
        return new ApiUserResource($request->user());
    }

    public function update(Request $request): JsonResponse
    {
        $user = $request->user();

        $data = $request->validate([
            'name' => ['sometimes', 'string', 'min:2', 'max:255'],
            'headline' => ['nullable', 'string', 'max:160'],
            'bio' => ['nullable', 'string', 'max:3000'],
            'skills' => ['nullable', 'array', 'max:50'],
            'skills.*' => ['string', 'min:1', 'max:40'],
        ]);

        if (array_key_exists('skills', $data) && is_array($data['skills'])) {
            $skills = collect($data['skills'])
                ->map(fn ($s) => trim((string) $s))
                ->filter()
                ->unique()
                ->take(50)
                ->values()
                ->all();
            $data['skills'] = $skills;
        }

        $user->update($data);

        return response()->json([
            'message' => 'تم تحديث البروفايل',
            'user' => new ApiUserResource($user->fresh()),
        ]);
    }

    public function uploadPhoto(Request $request): JsonResponse
    {
        $user = $request->user();

        $request->validate([
            'photo' => ['required', 'image', 'max:4096'],
        ], [
            'photo.required' => 'الصورة مطلوبة',
            'photo.image' => 'الملف يجب أن يكون صورة',
            'photo.max' => 'حجم الصورة يجب ألا يتجاوز 4 ميجابايت',
        ]);

        if ($request->hasFile('photo')) {
            $diskName = student_mobile_disk();

            if ($user->profile_image) {
                $oldDisk = $user->profile_image_disk ?: 'public';
                try {
                    if (Storage::disk($oldDisk)->exists($user->profile_image)) {
                        Storage::disk($oldDisk)->delete($user->profile_image);
                    }
                } catch (\Throwable) {
                    //
                }
                if (($user->profile_image_disk ?? null) === null && File::exists(public_path('storage/'.$user->profile_image))) {
                    File::delete(public_path('storage/'.$user->profile_image));
                }
            }

            $path = $request->file('photo')->storePublicly('profile-photos', ['disk' => $diskName]);
            $user->update([
                'profile_image' => $path,
                'profile_image_disk' => $diskName,
            ]);
        }

        return response()->json([
            'message' => 'تم تحديث الصورة',
            'user' => new ApiUserResource($user->fresh()),
        ]);
    }
}

