<?php

namespace App\Services;

use App\Models\Achievement;
use App\Models\PortfolioProject;
use App\Models\User;
use App\Models\UserAchievement;
use Illuminate\Support\Facades\DB;

class JourneyAchievementService
{
    public const CODE_FIRST_PROJECT = 'journey_first_project';

    public const CODE_FIVE_PROJECTS = 'journey_5_projects';

    public const CODE_TEN_PROJECTS = 'journey_10_projects';

    public const CODE_MENTOR_CHOICE = 'journey_mentor_choice';

    public const CODE_CAPSTONE = 'journey_capstone';

    public const CODE_DIPLOMA = 'journey_diploma';

    public const CODE_RECORDED = 'journey_recorded';

    public const CODE_PROFILE_PUBLIC = 'journey_profile_public';

    /**
     * @return list<array{code:string,name:string,description:string,icon:string,points_reward:int,sort_order:int}>
     */
    public static function definitions(): array
    {
        return [
            [
                'code' => self::CODE_FIRST_PROJECT,
                'name' => 'أول مشروع موثّق',
                'description' => 'نشرت أول مشروع معتمد من مدرب Mindlytics.',
                'icon' => 'fa-flag',
                'points_reward' => 50,
                'sort_order' => 10,
            ],
            [
                'code' => self::CODE_FIVE_PROJECTS,
                'name' => '5 مشاريع موثّقة',
                'description' => 'أكملت 5 مشاريع معتمدة ومنشورة في رحلتك.',
                'icon' => 'fa-layer-group',
                'points_reward' => 120,
                'sort_order' => 20,
            ],
            [
                'code' => self::CODE_TEN_PROJECTS,
                'name' => '10 مشاريع موثّقة',
                'description' => 'وصلت إلى 10 مشاريع موثّقة — دليل قوي للشركات.',
                'icon' => 'fa-trophy',
                'points_reward' => 250,
                'sort_order' => 30,
            ],
            [
                'code' => self::CODE_MENTOR_CHOICE,
                'name' => 'اختيار المدرب',
                'description' => 'مشروعك اختير Featured بواسطة Mindlytics.',
                'icon' => 'fa-star',
                'points_reward' => 150,
                'sort_order' => 40,
            ],
            [
                'code' => self::CODE_CAPSTONE,
                'name' => 'Capstone مكتمل',
                'description' => 'نشرت مشروع التخرج (Capstone) بنجاح.',
                'icon' => 'fa-graduation-cap',
                'points_reward' => 200,
                'sort_order' => 50,
            ],
            [
                'code' => self::CODE_DIPLOMA,
                'name' => 'رحلة دبلوم',
                'description' => 'نشرت مشروعاً موثّقاً ضمن دبلوم (مسار / أونلاين / أوفلاين).',
                'icon' => 'fa-chalkboard-teacher',
                'points_reward' => 80,
                'sort_order' => 60,
            ],
            [
                'code' => self::CODE_RECORDED,
                'name' => 'رحلة كورس مسجّل',
                'description' => 'نشرت مشروعاً موثّقاً من كورس مسجّل.',
                'icon' => 'fa-play-circle',
                'points_reward' => 80,
                'sort_order' => 70,
            ],
            [
                'code' => self::CODE_PROFILE_PUBLIC,
                'name' => 'الملف جاهز للمشاركة',
                'description' => 'جعلت ملف رحلتك عاماً للشركات.',
                'icon' => 'fa-share-nodes',
                'points_reward' => 40,
                'sort_order' => 80,
            ],
        ];
    }

    public function ensureDefinitions(): void
    {
        foreach (self::definitions() as $def) {
            Achievement::query()->updateOrCreate(
                ['code' => $def['code']],
                [
                    'name' => $def['name'],
                    'description' => $def['description'],
                    'icon' => $def['icon'],
                    'type' => 'custom',
                    'requirements' => ['source' => 'journey'],
                    'points_reward' => $def['points_reward'],
                    'is_active' => true,
                    'sort_order' => $def['sort_order'],
                ]
            );
        }
    }

    public function syncForPublishedProject(PortfolioProject $project): array
    {
        $user = $project->user;
        if (! $user) {
            return [];
        }

        $granted = [];
        $publishedCount = PortfolioProject::published()->where('user_id', $user->id)->count();

        if ($publishedCount >= 1) {
            $granted[] = $this->grant($user, self::CODE_FIRST_PROJECT, [
                'project_id' => $project->id,
                'published_count' => $publishedCount,
            ]);
        }
        if ($publishedCount >= 5) {
            $granted[] = $this->grant($user, self::CODE_FIVE_PROJECTS, [
                'published_count' => $publishedCount,
            ]);
        }
        if ($publishedCount >= 10) {
            $granted[] = $this->grant($user, self::CODE_TEN_PROJECTS, [
                'published_count' => $publishedCount,
            ]);
        }
        if ($project->is_capstone) {
            $granted[] = $this->grant($user, self::CODE_CAPSTONE, [
                'project_id' => $project->id,
            ]);
        }
        if ($project->program_type === PortfolioProject::PROGRAM_DIPLOMA) {
            $granted[] = $this->grant($user, self::CODE_DIPLOMA, [
                'project_id' => $project->id,
            ]);
        }
        if ($project->program_type === PortfolioProject::PROGRAM_RECORDED) {
            $granted[] = $this->grant($user, self::CODE_RECORDED, [
                'project_id' => $project->id,
            ]);
        }
        if ($project->is_featured) {
            $granted[] = $this->grant($user, self::CODE_MENTOR_CHOICE, [
                'project_id' => $project->id,
            ]);
        }

        return array_values(array_filter($granted));
    }

    public function grantFeatured(PortfolioProject $project): ?UserAchievement
    {
        if (! $project->user) {
            return null;
        }

        return $this->grant($project->user, self::CODE_MENTOR_CHOICE, [
            'project_id' => $project->id,
            'featured' => true,
        ]);
    }

    public function grantProfilePublic(User $user): ?UserAchievement
    {
        return $this->grant($user, self::CODE_PROFILE_PUBLIC, [
            'visibility' => 'public',
        ]);
    }

    public function grant(User $user, string $code, array $metadata = []): ?UserAchievement
    {
        $achievement = Achievement::query()
            ->where('code', $code)
            ->where('is_active', true)
            ->first();

        if (! $achievement) {
            return null;
        }

        $existing = UserAchievement::query()
            ->where('user_id', $user->id)
            ->where('achievement_id', $achievement->id)
            ->whereNull('course_id')
            ->first();

        if ($existing) {
            return null;
        }

        return DB::transaction(function () use ($user, $achievement, $metadata) {
            return UserAchievement::create([
                'user_id' => $user->id,
                'achievement_id' => $achievement->id,
                'course_id' => null,
                'earned_at' => now(),
                'progress' => 100,
                'points_earned' => (int) ($achievement->points_reward ?? 0),
                'metadata' => array_merge(['source' => 'journey'], $metadata),
            ]);
        });
    }

    /**
     * @return \Illuminate\Support\Collection<int, UserAchievement>
     */
    public function forUser(User $user)
    {
        return UserAchievement::query()
            ->with('achievement')
            ->where('user_id', $user->id)
            ->whereHas('achievement', function ($q) {
                $q->where('requirements->source', 'journey')
                    ->orWhere('code', 'like', 'journey_%');
            })
            ->latest('earned_at')
            ->get();
    }
}
