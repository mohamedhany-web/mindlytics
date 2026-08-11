<?php

namespace App\Services;

use App\Models\JourneyProfile;
use App\Models\User;

class JourneyProfileService
{
    public function ensureFor(User $user): JourneyProfile
    {
        $profile = $user->journeyProfile;
        if ($profile) {
            return $profile;
        }

        return JourneyProfile::create([
            'user_id' => $user->id,
            'slug' => JourneyProfile::generateSlug($user->name ?: ('student-' . $user->id)),
            'display_name' => $user->name,
            'headline' => $user->headline,
            'bio' => $user->bio,
            'visibility' => JourneyProfile::VISIBILITY_PRIVATE,
            'profile_completion' => 0,
            'is_active' => true,
        ]);
    }

    public function syncCompletion(JourneyProfile $profile): JourneyProfile
    {
        $profile->loadMissing('user');
        $profile->recalculateCompletion();
        $profile->save();

        return $profile;
    }

    public function makePublic(JourneyProfile $profile): JourneyProfile
    {
        $profile->visibility = JourneyProfile::VISIBILITY_PUBLIC;
        $profile->published_at = $profile->published_at ?: now();
        $profile->save();
        $this->syncCompletion($profile);

        return $profile;
    }
}
