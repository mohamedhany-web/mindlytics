<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\AdvancedCourse;
use App\Models\JourneyProfile;
use App\Models\JourneyShareEvent;
use App\Models\PortfolioProject;
use App\Models\User;
use App\Services\JourneyAchievementService;
use App\Services\JourneyProfileService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class JourneyDemoSeeder extends Seeder
{
    public function run(): void
    {
        app(JourneyAchievementService::class)->ensureDefinitions();

        $path = AcademicYear::query()->where('is_active', true)->ordered()->first()
            ?: AcademicYear::query()->first();
        $courses = AdvancedCourse::query()->orderBy('id')->take(6)->get();

        $this->ensurePlaceholderImages();

        $demos = [
            [
                'name' => 'محمد هاني',
                'phone' => '0501900001',
                'email' => 'demo.mohamed@mindlytics.local',
                'slug' => 'mohamed-hany',
                'headline' => 'Frontend Engineer · React & Tailwind',
                'career_goal' => 'البحث عن فرصة Frontend Junior/Mid في شركة منتج',
                'bio' => 'بدأت رحلتي في Mindlytics وأبني مشاريع حقيقية بمراجعة المدربين. أهتم بتجربة المستخدم والواجهات النظيفة.',
                'skills' => ['React', 'JavaScript', 'HTML', 'CSS', 'Tailwind'],
                'open_to_work' => true,
                'github' => 'https://github.com/demo-mohamed',
                'linkedin' => 'https://www.linkedin.com/in/demo-mohamed',
                'projects' => [
                    [
                        'title' => 'E-commerce Landing Page',
                        'type' => 'web_app',
                        'program' => 'recorded',
                        'tech' => ['React', 'Tailwind', 'Vite'],
                        'featured' => true,
                        'capstone' => false,
                        'desc' => 'صفحة هبوط لمتجر إلكتروني متجاوبة بالكامل مع سلة مبسطة.',
                        'learned' => 'فهمت إدارة الحالة البسيطة وبناء مكونات قابلة لإعادة الاستخدام.',
                        'challenges' => 'تحسين الأداء على الموبايل وضبط الـ accessibility.',
                    ],
                    [
                        'title' => 'Interactive Dashboard',
                        'type' => 'web_app',
                        'program' => 'diploma',
                        'tech' => ['JavaScript', 'Chart.js', 'CSS'],
                        'featured' => false,
                        'capstone' => false,
                        'desc' => 'لوحة مؤشرات تفاعلية لعرض مبيعات يومية وأسبوعية.',
                        'learned' => 'التعامل مع البيانات وتحويلها إلى تصورات بصرية.',
                        'challenges' => 'تنظيم الكود بدون إطار عمل ثقيل.',
                    ],
                    [
                        'title' => 'Capstone: Store Admin Panel',
                        'type' => 'web_app',
                        'program' => 'diploma',
                        'tech' => ['React', 'REST API', 'Tailwind'],
                        'featured' => true,
                        'capstone' => true,
                        'desc' => 'لوحة إدارة منتجات وطلبات مع مصادقة وCRUD كامل.',
                        'learned' => 'بناء تطبيق كامل من الواجهة حتى التكامل مع API.',
                        'challenges' => 'إدارة الصلاحيات وحالات التحميل والأخطاء.',
                    ],
                ],
            ],
            [
                'name' => 'سارة علي',
                'phone' => '0501900002',
                'email' => 'demo.sara@mindlytics.local',
                'slug' => 'sara-ali',
                'headline' => 'Data Analyst · Python & Visualization',
                'career_goal' => 'فرصة تحليل بيانات / Business Intelligence',
                'bio' => 'أحول البيانات إلى قرارات. مشاريعي موثّقة من مدربي Mindlytics.',
                'skills' => ['Python', 'Pandas', 'SQL', 'Power BI'],
                'open_to_work' => true,
                'github' => 'https://github.com/demo-sara',
                'linkedin' => null,
                'projects' => [
                    [
                        'title' => 'Sales Data Analysis',
                        'type' => 'script',
                        'program' => 'recorded',
                        'tech' => ['Python', 'Pandas', 'Matplotlib'],
                        'featured' => true,
                        'capstone' => false,
                        'desc' => 'تحليل مبيعات ربع سنوي مع تقارير ورؤى قابلة للتنفيذ.',
                        'learned' => 'تنظيف البيانات وبناء لوحات بصرية واضحة.',
                        'challenges' => 'التعامل مع بيانات ناقصة ومتكررة.',
                    ],
                    [
                        'title' => 'Customer Segmentation Notebook',
                        'type' => 'other',
                        'program' => 'diploma',
                        'tech' => ['Python', 'scikit-learn', 'SQL'],
                        'featured' => false,
                        'capstone' => true,
                        'desc' => 'تقسيم العملاء حسب السلوك والقيمة باستخدام Clustering.',
                        'learned' => 'اختيار السمات وتفسير نتائج النماذج.',
                        'challenges' => 'شرح النتائج لغير التقنيين.',
                    ],
                ],
            ],
            [
                'name' => 'أحمد كمال',
                'phone' => '0501900003',
                'email' => 'demo.ahmed@mindlytics.local',
                'slug' => 'ahmed-kamal',
                'headline' => 'Backend Developer · Laravel & APIs',
                'career_goal' => 'Backend Engineer في فريق منتج SaaS',
                'bio' => 'أركز على تصميم APIs نظيفة وقواعد بيانات قابلة للتوسع.',
                'skills' => ['Laravel', 'PHP', 'MySQL', 'REST API'],
                'open_to_work' => false,
                'github' => 'https://github.com/demo-ahmed',
                'linkedin' => 'https://www.linkedin.com/in/demo-ahmed',
                'projects' => [
                    [
                        'title' => 'Authentication API',
                        'type' => 'api',
                        'program' => 'recorded',
                        'tech' => ['Laravel', 'Sanctum', 'MySQL'],
                        'featured' => false,
                        'capstone' => false,
                        'desc' => 'واجهة مصادقة مع JWT/Sanctum وصلاحيات أدوار.',
                        'learned' => 'أمن المصادقة وإدارة الجلسات.',
                        'challenges' => 'اختبارات الحالات الحدية وإعادة التعيين.',
                    ],
                    [
                        'title' => 'Inventory Microservice',
                        'type' => 'api',
                        'program' => 'diploma',
                        'tech' => ['Laravel', 'Queues', 'Redis'],
                        'featured' => true,
                        'capstone' => true,
                        'desc' => 'خدمة مخزون مع طوابير وإشعارات نقص الكمية.',
                        'learned' => 'المعالجة غير المتزامنة وتصميم الأحداث.',
                        'challenges' => 'اتساق البيانات تحت الضغط.',
                    ],
                ],
            ],
            [
                'name' => 'نور حسن',
                'phone' => '0501900004',
                'email' => 'demo.nour@mindlytics.local',
                'slug' => 'nour-hassan',
                'headline' => 'UI/UX Designer · Product Thinking',
                'career_goal' => 'Product Designer في ستارت أب',
                'bio' => 'أصمم تجارب واضحة مدعومة بأبحاث مستخدمين ومشاريع موثّقة.',
                'skills' => ['Figma', 'UI Design', 'Prototyping', 'User Research'],
                'open_to_work' => true,
                'github' => null,
                'linkedin' => 'https://www.linkedin.com/in/demo-nour',
                'projects' => [
                    [
                        'title' => 'EdTech App Redesign',
                        'type' => 'design',
                        'program' => 'diploma',
                        'tech' => ['Figma', 'Design System'],
                        'featured' => true,
                        'capstone' => false,
                        'desc' => 'إعادة تصميم تطبيق تعليمي مع نظام تصميم موحّد.',
                        'learned' => 'بناء Design System وتحسين قابلية الاستخدام.',
                        'challenges' => 'الموازنة بين الجمال والوضوح.',
                    ],
                    [
                        'title' => 'Hiring Portal Prototype',
                        'type' => 'design',
                        'program' => 'recorded',
                        'tech' => ['Figma', 'Prototyping'],
                        'featured' => false,
                        'capstone' => true,
                        'desc' => 'نموذج تفاعلي لمنصة توظيف تعرض رحلات الطلاب.',
                        'learned' => 'تدفقات المستخدم للموارد البشرية.',
                        'challenges' => 'تبسيط المعلومات الكثيفة.',
                    ],
                ],
            ],
            [
                'name' => 'يوسف مراد',
                'phone' => '0501900005',
                'email' => 'demo.youssef@mindlytics.local',
                'slug' => 'youssef-murad',
                'headline' => 'Full-Stack Builder · Laravel + React',
                'career_goal' => 'Full-stack role with product ownership',
                'bio' => 'أبني ميزات كاملة من قاعدة البيانات حتى الواجهة، مع أدلة مراجعة من المدربين.',
                'skills' => ['Laravel', 'React', 'MySQL', 'Git'],
                'open_to_work' => true,
                'github' => 'https://github.com/demo-youssef',
                'linkedin' => null,
                'projects' => [
                    [
                        'title' => 'Learning Progress Tracker',
                        'type' => 'web_app',
                        'program' => 'recorded',
                        'tech' => ['Laravel', 'Blade', 'Alpine.js'],
                        'featured' => false,
                        'capstone' => false,
                        'desc' => 'متتبع تقدّم طلاب مع إنجازات ومعرض مشاريع.',
                        'learned' => 'ربط رحلة التعلم بالأدلة المرئية.',
                        'challenges' => 'نمذجة الحالات وسير المراجعة.',
                    ],
                    [
                        'title' => 'Capstone: Freelance Hub',
                        'type' => 'web_app',
                        'program' => 'diploma',
                        'tech' => ['React', 'Laravel', 'MySQL'],
                        'featured' => true,
                        'capstone' => true,
                        'desc' => 'منصة مصغرة لعرض المستقلين ومشاريعهم الموثّقة.',
                        'learned' => 'بناء منتج كامل جاهز للعرض على الشركات.',
                        'challenges' => 'الأداء والبحث والتصفية.',
                    ],
                    [
                        'title' => 'CLI Deploy Helper',
                        'type' => 'cli',
                        'program' => 'recorded',
                        'tech' => ['PHP', 'Shell'],
                        'featured' => false,
                        'capstone' => false,
                        'desc' => 'أداة سطر أوامر لتبسيط خطوات النشر المحلية.',
                        'learned' => 'أتمتة المهام المتكررة.',
                        'challenges' => 'التعامل مع بيئات مختلفة.',
                    ],
                ],
            ],
        ];

        $achievements = app(JourneyAchievementService::class);
        $profilesService = app(JourneyProfileService::class);
        $placeholders = [
            'portfolio-images/demo-1.svg',
            'portfolio-images/demo-2.svg',
            'portfolio-images/demo-3.svg',
        ];

        foreach ($demos as $index => $demo) {
            $user = User::query()->firstOrCreate(
                ['phone' => $demo['phone']],
                [
                    'name' => $demo['name'],
                    'email' => $demo['email'],
                    'password' => Hash::make('password123'),
                    'role' => 'student',
                    'is_active' => true,
                    'bio' => $demo['bio'],
                    'headline' => $demo['headline'],
                    'skills' => $demo['skills'],
                ]
            );

            $user->fill([
                'name' => $demo['name'],
                'email' => $demo['email'],
                'bio' => $demo['bio'],
                'headline' => $demo['headline'],
                'skills' => $demo['skills'],
                'is_active' => true,
                'role' => 'student',
            ])->save();

            $profile = $profilesService->ensureFor($user);
            $profile->fill([
                'slug' => JourneyProfile::generateSlug($demo['slug'], $profile->id),
                'display_name' => $demo['name'],
                'headline' => $demo['headline'],
                'bio' => $demo['bio'],
                'career_goal' => $demo['career_goal'],
                'github_url' => $demo['github'],
                'linkedin_url' => $demo['linkedin'],
                'visibility' => JourneyProfile::VISIBILITY_PUBLIC,
                'is_open_to_work' => $demo['open_to_work'],
                'is_active' => true,
                'published_at' => now()->subDays(20 - $index),
            ]);
            // Force exact demo slug if free
            if (! JourneyProfile::query()->where('slug', $demo['slug'])->where('id', '!=', $profile->id)->exists()) {
                $profile->slug = $demo['slug'];
            }
            $profile->save();
            $profilesService->syncCompletion($profile);
            $achievements->grantProfilePublic($user);

            foreach ($demo['projects'] as $pIndex => $projectData) {
                $course = $courses->isNotEmpty() ? $courses[$pIndex % $courses->count()] : null;
                $isDiploma = ($projectData['program'] ?? 'recorded') === 'diploma';

                // Fallback: if diploma requested but no learning path, keep diploma label without FK.
                $academicYearId = ($isDiploma && $path) ? $path->id : null;
                $advancedCourseId = (! $isDiploma && $course) ? $course->id : (($course && ! $isDiploma) ? $course->id : null);
                if (! $isDiploma && ! $advancedCourseId && $path) {
                    // still mark recorded even without course FK
                    $academicYearId = null;
                }
                if ($isDiploma && ! $academicYearId && $course) {
                    $advancedCourseId = $course->id;
                    $isDiploma = false;
                }

                $project = PortfolioProject::query()->updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'title' => $projectData['title'],
                    ],
                    [
                        'academic_year_id' => $academicYearId,
                        'advanced_course_id' => $isDiploma ? null : $advancedCourseId,
                        'program_type' => $isDiploma ? PortfolioProject::PROGRAM_DIPLOMA : PortfolioProject::PROGRAM_RECORDED,
                        'project_type' => $projectData['type'],
                        'is_capstone' => (bool) $projectData['capstone'],
                        'description' => $projectData['desc'],
                        'technologies' => $projectData['tech'],
                        'what_i_learned' => $projectData['learned'],
                        'challenges' => $projectData['challenges'],
                        'project_url' => 'https://example.com/demo/' . Str::slug($projectData['title']),
                        'github_url' => $demo['github'] ? rtrim($demo['github'], '/') . '/' . Str::slug($projectData['title']) : null,
                        'image_path' => $placeholders[$pIndex % count($placeholders)],
                        'status' => PortfolioProject::STATUS_PUBLISHED,
                        'is_visible' => true,
                        'is_featured' => (bool) $projectData['featured'],
                        'instructor_notes' => 'Strong delivery. Clear documentation and solid problem solving.',
                        'rubric_code_quality' => 8,
                        'rubric_ui_ux' => 9,
                        'rubric_functionality' => 8,
                        'rubric_problem_solving' => 9,
                        'rubric_documentation' => 8,
                        'rubric_average' => 8.40,
                        'reviewed_at' => now()->subDays(15 - $pIndex),
                        'published_at' => now()->subDays(14 - $pIndex - $index),
                    ]
                );

                $achievements->syncForPublishedProject($project->fresh(['user']));

                // Seed share/view events for analytics
                foreach (['project_view', 'og_fetch', 'linkedin', 'copy'] as $i => $channel) {
                    for ($n = 0; $n < (3 - $i + ($projectData['featured'] ? 2 : 0)); $n++) {
                        JourneyShareEvent::create([
                            'user_id' => $user->id,
                            'shareable_type' => PortfolioProject::class,
                            'shareable_id' => $project->id,
                            'channel' => $channel,
                            'card_type' => $project->is_featured ? 'featured' : 'project_verified',
                            'created_at' => now()->subDays(rand(0, 20))->subHours(rand(0, 20)),
                            'updated_at' => now(),
                        ]);
                    }
                }
            }

            JourneyShareEvent::create([
                'user_id' => $user->id,
                'shareable_type' => JourneyProfile::class,
                'shareable_id' => $profile->id,
                'channel' => 'profile_view',
                'card_type' => 'profile',
                'created_at' => now()->subDays(rand(0, 10)),
                'updated_at' => now(),
            ]);
            JourneyShareEvent::create([
                'user_id' => $user->id,
                'shareable_type' => JourneyProfile::class,
                'shareable_id' => $profile->id,
                'channel' => 'linkedin',
                'card_type' => 'profile',
                'created_at' => now()->subDays(rand(0, 8)),
                'updated_at' => now(),
            ]);
        }

        $this->command?->info('Journey demo data ready: 5 public profiles + verified projects.');
        $this->command?->info('Open /portfolio and /portfolio/talent — demo logins use password123 (phones 0501900001+).');
    }

    private function ensurePlaceholderImages(): void
    {
        $dir = public_path('portfolio-images');
        File::ensureDirectoryExists($dir);

        $svgs = [
            'demo-1.svg' => ['#2563eb', '#0ea5e9', 'Landing'],
            'demo-2.svg' => ['#059669', '#10b981', 'Dashboard'],
            'demo-3.svg' => ['#7c3aed', '#a855f7', 'API'],
        ];

        foreach ($svgs as $file => [$c1, $c2, $label]) {
            $path = $dir . DIRECTORY_SEPARATOR . $file;
            if (File::exists($path)) {
                continue;
            }
            $svg = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="1200" height="675" viewBox="0 0 1200 675">
  <defs>
    <linearGradient id="g" x1="0" y1="0" x2="1" y2="1">
      <stop offset="0%" stop-color="{$c1}"/>
      <stop offset="100%" stop-color="{$c2}"/>
    </linearGradient>
  </defs>
  <rect width="1200" height="675" fill="url(#g)"/>
  <text x="60" y="120" fill="white" font-size="42" font-family="Arial, sans-serif" font-weight="700">Mindlytics Journey</text>
  <text x="60" y="360" fill="white" font-size="64" font-family="Arial, sans-serif" font-weight="700">{$label} Project</text>
  <text x="60" y="430" fill="rgba(255,255,255,0.85)" font-size="28" font-family="Arial, sans-serif">Verified demo work</text>
</svg>
SVG;
            File::put($path, $svg);
        }
    }
}
