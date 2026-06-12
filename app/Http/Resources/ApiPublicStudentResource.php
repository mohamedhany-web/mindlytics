<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** بروفايل طالب عام للموبايل — بدون بريد أو هاتف */
class ApiPublicStudentResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'headline' => $this->headline,
            'bio' => $this->bio,
            'skills' => is_array($this->skills) ? $this->skills : [],
            'profile_image_url' => $this->profile_image_url,
        ];
    }
}
