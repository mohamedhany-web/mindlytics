<?php

namespace App\Services\Scholarship;

use App\Models\AdvancedCourse;
use App\Models\ScholarshipProgram;
use App\Support\BranchContext;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ScholarshipProgramService
{
    /**
     * @param  array{name: string, description?: string|null, instructor_id: int, is_active?: bool, starts_at?: string|null, ends_at?: string|null, slug?: string|null}  $data
     */
    public function create(array $data): ScholarshipProgram
    {
        return DB::transaction(function () use ($data) {
            $slug = ! empty($data['slug'])
                ? Str::slug($data['slug'])
                : ScholarshipProgram::generateUniqueSlug($data['name']);

            if (ScholarshipProgram::query()->where('slug', $slug)->exists()) {
                $slug = ScholarshipProgram::generateUniqueSlug($data['name']);
            }

            $program = ScholarshipProgram::create([
                'name' => $data['name'],
                'slug' => $slug,
                'description' => $data['description'] ?? null,
                'instructor_id' => (int) $data['instructor_id'],
                'is_active' => $data['is_active'] ?? true,
                'starts_at' => $data['starts_at'] ?? null,
                'ends_at' => $data['ends_at'] ?? null,
                'created_by' => Auth::id(),
            ]);

            $branchId = app(BranchContext::class)->id();

            $course = AdvancedCourse::create([
                'branch_id' => $branchId,
                'instructor_id' => $program->instructor_id,
                'title' => $program->name,
                'description' => $program->description ?? ('كورس منحة: ' . $program->name),
                'price' => 0,
                'discount_amount' => 0,
                'is_active' => true,
                'is_featured' => false,
                'is_free' => true,
                'is_scholarship_only' => true,
                'scholarship_program_id' => $program->id,
                'level' => 'beginner',
            ]);

            $program->update(['advanced_course_id' => $course->id]);

            return $program->fresh(['instructor', 'course']);
        });
    }

    /**
     * @param  array{name?: string, description?: string|null, instructor_id?: int, is_active?: bool, starts_at?: string|null, ends_at?: string|null}  $data
     */
    public function update(ScholarshipProgram $program, array $data): ScholarshipProgram
    {
        return DB::transaction(function () use ($program, $data) {
            $program->fill([
                'name' => $data['name'] ?? $program->name,
                'description' => $data['description'] ?? $program->description,
                'instructor_id' => isset($data['instructor_id']) ? (int) $data['instructor_id'] : $program->instructor_id,
                'is_active' => array_key_exists('is_active', $data) ? (bool) $data['is_active'] : $program->is_active,
                'starts_at' => array_key_exists('starts_at', $data) ? $data['starts_at'] : $program->starts_at,
                'ends_at' => array_key_exists('ends_at', $data) ? $data['ends_at'] : $program->ends_at,
            ]);
            $program->save();

            if ($program->course) {
                $program->course->update([
                    'title' => $program->name,
                    'description' => $program->description ?? $program->course->description,
                    'instructor_id' => $program->instructor_id,
                ]);
            }

            return $program->fresh(['instructor', 'course']);
        });
    }
}
