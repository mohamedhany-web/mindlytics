<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** JSON خفيف لتطبيق الموبايل — لا يُعرض حقول حساسة */
class ApiUserResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role,
            'phone' => $this->phone,
            'profile_image_url' => $this->profile_image_url,
            'bio' => $this->bio,
            'headline' => $this->headline,
            'skills' => is_array($this->skills) ? $this->skills : [],
        ];
    }
}
