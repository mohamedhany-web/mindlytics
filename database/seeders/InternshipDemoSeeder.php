<?php

namespace Database\Seeders;

use App\Models\Internship;
use App\Models\InternshipApplication;
use Illuminate\Database\Seeder;

class InternshipDemoSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            [
                'title' => 'Frontend Development Internship',
                'slug' => 'frontend-development-internship',
                'department' => 'Frontend',
                'summary' => 'تدريب عملي على بناء واجهات حديثة باستخدام React وTailwind تحت إشراف مدربين.',
                'description' => "خلال التدريب ستعمل على مشاريع حقيقية:\n- بناء صفحات متجاوبة\n- مكونات قابلة لإعادة الاستخدام\n- مراجعة كود أسبوعية مع المدرب",
                'requirements' => "- معرفة أساسية بـ HTML/CSS/JS\n- الرغبة في التعلم والعمل الجماعي\n- الالتزام بمدة التدريب",
                'benefits' => "- شهادة تدريب من Mindlytics\n- مشاريع موثّقة في Journey\n- فرصة توصية وظيفية للمتميزين",
                'location' => 'القاهرة / Hybrid',
                'type' => 'hybrid',
                'duration' => '3 أشهر',
                'seats' => 15,
                'featured' => true,
            ],
            [
                'title' => 'Data Analysis Internship',
                'slug' => 'data-analysis-internship',
                'department' => 'Data',
                'summary' => 'تدريب على تحليل البيانات باستخدام Python وبناء تقارير قابلة للتنفيذ.',
                'description' => "مسار عملي يشمل تنظيف البيانات، التصور، وتقديم Insights لفريق المنتج.",
                'requirements' => "- أساسيات Python\n- معرفة مبدئية بـ Excel أو SQL مفيدة",
                'benefits' => "- مشاريع تحليل حقيقية\n- توجيه مهني\n- شهادة إتمام",
                'location' => 'عن بُعد',
                'type' => 'remote',
                'duration' => '2 أشهر',
                'seats' => 10,
                'featured' => true,
            ],
            [
                'title' => 'Laravel Backend Internship',
                'slug' => 'laravel-backend-internship',
                'department' => 'Backend',
                'summary' => 'بناء APIs وخدمات خلفية باستخدام Laravel مع أفضل الممارسات.',
                'description' => "ستتعلم تصميم قواعد البيانات، المصادقة، والاختبارات ضمن فريق تطوير.",
                'requirements' => "- أساسيات PHP\n- فهم قواعد البيانات العلائقية",
                'benefits' => "- خبرة فريق\n- Code review\n- شهادة تدريب",
                'location' => 'الإسكندرية',
                'type' => 'onsite',
                'duration' => '3 أشهر',
                'seats' => 8,
                'featured' => false,
            ],
        ];

        foreach ($items as $i => $item) {
            $internship = Internship::query()->updateOrCreate(
                ['slug' => $item['slug']],
                [
                    'title' => $item['title'],
                    'department' => $item['department'],
                    'summary' => $item['summary'],
                    'description' => $item['description'],
                    'requirements' => $item['requirements'],
                    'benefits' => $item['benefits'],
                    'location' => $item['location'],
                    'type' => $item['type'],
                    'duration' => $item['duration'],
                    'seats' => $item['seats'],
                    'status' => Internship::STATUS_OPEN,
                    'starts_at' => now()->addDays(14),
                    'ends_at' => now()->addMonths(3),
                    'application_deadline' => now()->addDays(21),
                    'is_featured' => $item['featured'],
                    'is_published' => true,
                    'published_at' => now()->subDays(2 - $i),
                    'sort_order' => $i,
                ]
            );

            InternshipApplication::query()->firstOrCreate(
                [
                    'internship_id' => $internship->id,
                    'email' => 'applicant' . ($i + 1) . '@example.com',
                ],
                [
                    'name' => ['أحمد متقدم', 'سارة متقدمة', 'محمود متقدم'][$i] ?? 'متقدم تجريبي',
                    'phone' => '010000000' . ($i + 1),
                    'university' => 'جامعة القاهرة',
                    'major' => $item['department'],
                    'year_of_study' => 'خريج',
                    'cover_letter' => 'أرغب في الانضمام لهذه الفرصة لتطوير مهاراتي عملياً.',
                    'portfolio_url' => 'https://example.com/portfolio',
                    'github_url' => 'https://github.com/example',
                    'status' => 'pending',
                    'source' => 'website',
                ]
            );
        }

        $this->command?->info('Internship demo opportunities + sample applications ready.');
    }
}
