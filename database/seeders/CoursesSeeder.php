<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\User;
use App\Models\AdvancedCourse;
use App\Models\CourseSection;
use App\Models\CourseLesson;
use App\Models\AcademicYear;
use App\Models\AcademicSubject;

// زيادة حد الذاكرة
ini_set('memory_limit', '1024M');

class CoursesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (!Schema::hasTable('advanced_courses')) {
            $this->command->warn('⚠️  جدول advanced_courses غير موجود. يرجى تشغيل migrations أولاً.');
            return;
        }

        echo "\n📚 إضافة كورسات تجريبية...\n";
        echo "=" . str_repeat("=", 60) . "\n";

        // الحصول على مدرب
        $instructor = User::where('role', 'instructor')->where('is_active', true)->first() 
                     ?? User::where('role', 'teacher')->where('is_active', true)->first()
                     ?? User::where('role', 'admin')->where('is_active', true)->first()
                     ?? User::first();

        $instructorId = $instructor->id ?? null;

        // الحصول على سنة دراسية ومواد دراسية إذا كانت موجودة
        $academicYear = AcademicYear::where('is_active', true)->first();
        $academicSubject = AcademicSubject::where('is_active', true)->first();

        // كورسات تجريبية متنوعة
        $courses = [
            [
                'title' => 'مقدمة في البرمجة - JavaScript',
                'description' => 'كورس شامل لتعلم أساسيات JavaScript من الصفر. ستعلم كيفية كتابة الأكواد البرمجية، استخدام المتغيرات والدوال، والعمل مع DOM.',
                'objectives' => 'فهم أساسيات JavaScript، كتابة الكود البرمجي، التعامل مع DOM',
                'level' => 'beginner',
                'duration_hours' => 30,
                'price' => 299,
                'is_free' => false,
                'is_featured' => true,
                'programming_language' => 'JavaScript',
                'requirements' => 'لا توجد متطلبات مسبقة',
                'what_you_learn' => 'تعلم JavaScript من الصفر، كتابة الكود البرمجي، بناء مشاريع عملية',
            ],
            [
                'title' => 'Python للمبتدئين',
                'description' => 'ابدأ رحلتك في تعلم Python مع هذا الكورس الشامل. تعلم أساسيات اللغة البرمجية الأكثر شعبية في العالم.',
                'objectives' => 'تعلم Python من الصفر، فهم البرمجة الكائنية، بناء مشاريع عملية',
                'level' => 'beginner',
                'duration_hours' => 40,
                'price' => 349,
                'is_free' => false,
                'is_featured' => true,
                'programming_language' => 'Python',
                'requirements' => 'لا توجد متطلبات مسبقة',
                'what_you_learn' => 'Python basics، Data structures، Functions، OOP',
            ],
            [
                'title' => 'تطوير الويب الكامل - Full Stack',
                'description' => 'كورس شامل لتعلم تطوير الويب من الصفر إلى الاحتراف. HTML, CSS, JavaScript, React, Node.js وغيرها.',
                'objectives' => 'بناء مواقع ويب كاملة، تعلم Frontend و Backend، نشر المشاريع',
                'level' => 'intermediate',
                'duration_hours' => 80,
                'price' => 599,
                'is_free' => false,
                'is_featured' => true,
                'programming_language' => 'JavaScript',
                'category' => 'Web Development',
                'requirements' => 'معرفة أساسية بالبرمجة',
                'what_you_learn' => 'HTML/CSS، JavaScript، React، Node.js، Databases',
            ],
            [
                'title' => 'React المتقدم',
                'description' => 'تعلم React بشكل متقدم مع Hooks، State Management، وبناء تطبيقات معقدة.',
                'objectives' => 'إتقان React، استخدام Hooks، State Management، بناء تطبيقات واقعية',
                'level' => 'advanced',
                'duration_hours' => 50,
                'price' => 449,
                'is_free' => false,
                'is_featured' => false,
                'programming_language' => 'JavaScript',
                'framework' => 'React',
                'requirements' => 'معرفة JavaScript و React أساسيات',
                'what_you_learn' => 'React Hooks، Redux، Context API، Performance Optimization',
            ],
            [
                'title' => 'Node.js و Express.js',
                'description' => 'تعلم بناء واجهات برمجية (APIs) وخدمات خلفية باستخدام Node.js و Express.js.',
                'objectives' => 'بناء REST APIs، فهم Backend Development، التعامل مع Databases',
                'level' => 'intermediate',
                'duration_hours' => 45,
                'price' => 399,
                'is_free' => false,
                'is_featured' => false,
                'programming_language' => 'JavaScript',
                'framework' => 'Express.js',
                'requirements' => 'معرفة JavaScript',
                'what_you_learn' => 'Node.js، Express.js، REST APIs، MongoDB، Authentication',
            ],
            [
                'title' => 'HTML & CSS للمبتدئين',
                'description' => 'كورس شامل لتعلم HTML و CSS من الصفر. بناء صفحات ويب جميلة ومتجاوبة.',
                'objectives' => 'تعلم HTML و CSS، بناء صفحات ويب، Responsive Design',
                'level' => 'beginner',
                'duration_hours' => 25,
                'price' => 199,
                'is_free' => false,
                'is_featured' => false,
                'category' => 'Web Development',
                'requirements' => 'لا توجد متطلبات مسبقة',
                'what_you_learn' => 'HTML Tags، CSS Styling، Flexbox، Grid، Responsive Design',
            ],
            [
                'title' => 'PHP و Laravel',
                'description' => 'تعلم PHP و إطار عمل Laravel لبناء تطبيقات ويب قوية وآمنة.',
                'objectives' => 'تعلم PHP، فهم Laravel Framework، بناء تطبيقات كاملة',
                'level' => 'intermediate',
                'duration_hours' => 60,
                'price' => 499,
                'is_free' => false,
                'is_featured' => true,
                'programming_language' => 'PHP',
                'framework' => 'Laravel',
                'requirements' => 'معرفة أساسية بالبرمجة',
                'what_you_learn' => 'PHP Basics، Laravel Framework، MVC Pattern، Database',
            ],
            [
                'title' => 'البرمجة الكائنية - OOP',
                'description' => 'فهم مفاهيم البرمجة الكائنية والتوجه للكائنات في البرمجة.',
                'objectives' => 'فهم OOP Concepts، Classes و Objects، Inheritance، Polymorphism',
                'level' => 'intermediate',
                'duration_hours' => 35,
                'price' => 299,
                'is_free' => false,
                'is_featured' => false,
                'category' => 'Programming Concepts',
                'requirements' => 'معرفة أساسية بأي لغة برمجية',
                'what_you_learn' => 'Classes، Objects، Inheritance، Encapsulation، Polymorphism',
            ],
            [
                'title' => 'قواعد البيانات - SQL',
                'description' => 'تعلم قواعد البيانات وإدارة البيانات باستخدام SQL.',
                'objectives' => 'فهم قواعد البيانات، تعلم SQL، تصميم Databases',
                'level' => 'beginner',
                'duration_hours' => 30,
                'price' => 249,
                'is_free' => false,
                'is_featured' => false,
                'category' => 'Database',
                'requirements' => 'لا توجد متطلبات مسبقة',
                'what_you_learn' => 'SQL Queries، Database Design، Normalization، Relationships',
            ],
            [
                'title' => 'Algorithms و Data Structures',
                'description' => 'تعلم الخوارزميات وهياكل البيانات لتحسين مهاراتك البرمجية.',
                'objectives' => 'فهم Algorithms، Data Structures، Problem Solving',
                'level' => 'advanced',
                'duration_hours' => 70,
                'price' => 649,
                'is_free' => false,
                'is_featured' => true,
                'category' => 'Computer Science',
                'requirements' => 'معرفة متقدمة بالبرمجة',
                'what_you_learn' => 'Algorithms، Data Structures، Complexity Analysis، Problem Solving',
            ],
            [
                'title' => 'Vue.js من الصفر',
                'description' => 'تعلم Vue.js لإطار عمل JavaScript الحديث لبناء واجهات مستخدم تفاعلية.',
                'objectives' => 'تعلم Vue.js، بناء Single Page Applications، State Management',
                'level' => 'intermediate',
                'duration_hours' => 40,
                'price' => 379,
                'is_free' => false,
                'is_featured' => false,
                'programming_language' => 'JavaScript',
                'framework' => 'Vue.js',
                'requirements' => 'معرفة JavaScript و HTML/CSS',
                'what_you_learn' => 'Vue.js Basics، Components، Vuex، Vue Router',
            ],
            [
                'title' => 'Flutter لتطوير التطبيقات',
                'description' => 'تعلم Flutter لبناء تطبيقات موبايل متعددة المنصات باستخدام Dart.',
                'objectives' => 'بناء تطبيقات موبايل، تعلم Flutter Framework، نشر التطبيقات',
                'level' => 'intermediate',
                'duration_hours' => 55,
                'price' => 549,
                'is_free' => false,
                'is_featured' => true,
                'programming_language' => 'Dart',
                'framework' => 'Flutter',
                'requirements' => 'معرفة أساسية بالبرمجة',
                'what_you_learn' => 'Flutter Basics، Widgets، State Management، App Publishing',
            ],
            [
                'title' => 'Git و GitHub',
                'description' => 'تعلم إدارة المشاريع البرمجية باستخدام Git و GitHub.',
                'objectives' => 'فهم Git، استخدام GitHub، إدارة المشاريع، Collaboration',
                'level' => 'beginner',
                'duration_hours' => 20,
                'price' => 0,
                'is_free' => true,
                'is_featured' => false,
                'category' => 'Tools',
                'requirements' => 'لا توجد متطلبات مسبقة',
                'what_you_learn' => 'Git Commands، GitHub، Branching، Pull Requests، Collaboration',
            ],
            [
                'title' => 'Docker و DevOps',
                'description' => 'تعلم Docker و DevOps لتحسين عملية التطوير والنشر.',
                'objectives' => 'فهم Docker، CI/CD، DevOps Practices، Containerization',
                'level' => 'advanced',
                'duration_hours' => 45,
                'price' => 599,
                'is_free' => false,
                'is_featured' => false,
                'category' => 'DevOps',
                'requirements' => 'معرفة بالبرمجة والنظم',
                'what_you_learn' => 'Docker، Kubernetes، CI/CD، DevOps Tools',
            ],
            // مسارات تعليمية إضافية
            [
                'title' => 'مسار تطوير تطبيقات Android',
                'description' => 'مسار شامل لتعلم تطوير تطبيقات Android من الصفر إلى الاحتراف باستخدام Kotlin.',
                'objectives' => 'بناء تطبيقات Android، تعلم Kotlin، Material Design، نشر التطبيقات',
                'level' => 'intermediate',
                'duration_hours' => 90,
                'price' => 799,
                'is_free' => false,
                'is_featured' => true,
                'programming_language' => 'Kotlin',
                'framework' => 'Android',
                'category' => 'Mobile Development',
                'requirements' => 'معرفة أساسية بالبرمجة',
                'what_you_learn' => 'Kotlin، Android SDK، Material Design، Firebase، App Publishing',
                'skills' => ['Kotlin', 'Android Development', 'Material Design', 'Firebase'],
            ],
            [
                'title' => 'مسار تطوير تطبيقات iOS',
                'description' => 'تعلم تطوير تطبيقات iOS باستخدام Swift و SwiftUI.',
                'objectives' => 'بناء تطبيقات iOS، تعلم Swift، SwiftUI، Core Data',
                'level' => 'intermediate',
                'duration_hours' => 85,
                'price' => 899,
                'is_free' => false,
                'is_featured' => true,
                'programming_language' => 'Swift',
                'framework' => 'SwiftUI',
                'category' => 'Mobile Development',
                'requirements' => 'معرفة أساسية بالبرمجة',
                'what_you_learn' => 'Swift، SwiftUI، Core Data، App Store Publishing',
                'skills' => ['Swift', 'SwiftUI', 'iOS Development', 'Core Data'],
            ],
            [
                'title' => 'مسار الأمن السيبراني',
                'description' => 'مسار شامل لتعلم الأمن السيبراني وحماية الأنظمة والشبكات.',
                'objectives' => 'فهم الأمن السيبراني، Ethical Hacking، Network Security، Cryptography',
                'level' => 'advanced',
                'duration_hours' => 100,
                'price' => 1299,
                'is_free' => false,
                'is_featured' => true,
                'category' => 'Cybersecurity',
                'requirements' => 'معرفة متقدمة بالبرمجة والشبكات',
                'what_you_learn' => 'Ethical Hacking، Network Security، Cryptography، Penetration Testing',
                'skills' => ['Cybersecurity', 'Ethical Hacking', 'Network Security', 'Cryptography'],
            ],
            [
                'title' => 'مسار الذكاء الاصطناعي و Machine Learning',
                'description' => 'تعلم الذكاء الاصطناعي وتعلم الآلة باستخدام Python و TensorFlow.',
                'objectives' => 'فهم AI و ML، تعلم TensorFlow، بناء نماذج ذكية، Deep Learning',
                'level' => 'advanced',
                'duration_hours' => 120,
                'price' => 1499,
                'is_free' => false,
                'is_featured' => true,
                'programming_language' => 'Python',
                'framework' => 'TensorFlow',
                'category' => 'Artificial Intelligence',
                'requirements' => 'معرفة متقدمة بـ Python والرياضيات',
                'what_you_learn' => 'Machine Learning، Deep Learning، TensorFlow، Neural Networks',
                'skills' => ['Machine Learning', 'Deep Learning', 'TensorFlow', 'Neural Networks'],
            ],
            [
                'title' => 'مسار تطوير الألعاب - Unity',
                'description' => 'تعلم تطوير الألعاب باستخدام Unity و C#.',
                'objectives' => 'بناء ألعاب 2D و 3D، تعلم Unity، C# Programming، Game Design',
                'level' => 'intermediate',
                'duration_hours' => 75,
                'price' => 699,
                'is_free' => false,
                'is_featured' => true,
                'programming_language' => 'C#',
                'framework' => 'Unity',
                'category' => 'Game Development',
                'requirements' => 'معرفة أساسية بالبرمجة',
                'what_you_learn' => 'Unity Basics، C# for Games، 2D/3D Game Development، Game Physics',
                'skills' => ['Unity', 'C#', 'Game Development', 'Game Design'],
            ],
            [
                'title' => 'مسار Blockchain و Web3',
                'description' => 'تعلم تقنية Blockchain وتطوير تطبيقات Web3.',
                'objectives' => 'فهم Blockchain، Smart Contracts، Solidity، Web3 Development',
                'level' => 'advanced',
                'duration_hours' => 80,
                'price' => 1199,
                'is_free' => false,
                'is_featured' => false,
                'programming_language' => 'Solidity',
                'category' => 'Blockchain',
                'requirements' => 'معرفة متقدمة بالبرمجة',
                'what_you_learn' => 'Blockchain، Smart Contracts، Solidity، Web3، DeFi',
                'skills' => ['Blockchain', 'Smart Contracts', 'Solidity', 'Web3'],
            ],
            [
                'title' => 'مسار Data Science',
                'description' => 'تعلم علم البيانات وتحليل البيانات باستخدام Python.',
                'objectives' => 'تحليل البيانات، Data Visualization، Statistical Analysis، Machine Learning',
                'level' => 'intermediate',
                'duration_hours' => 95,
                'price' => 999,
                'is_free' => false,
                'is_featured' => true,
                'programming_language' => 'Python',
                'category' => 'Data Science',
                'requirements' => 'معرفة Python والرياضيات',
                'what_you_learn' => 'Data Analysis، Pandas، NumPy، Data Visualization، Statistics',
                'skills' => ['Data Science', 'Pandas', 'NumPy', 'Data Visualization'],
            ],
            [
                'title' => 'مسار UI/UX Design',
                'description' => 'تعلم تصميم واجهات المستخدم وتجربة المستخدم.',
                'objectives' => 'تصميم واجهات جميلة، UX Research، Prototyping، Design Tools',
                'level' => 'beginner',
                'duration_hours' => 60,
                'price' => 599,
                'is_free' => false,
                'is_featured' => false,
                'category' => 'Design',
                'requirements' => 'لا توجد متطلبات مسبقة',
                'what_you_learn' => 'UI Design، UX Research، Figma، Prototyping، Design Principles',
                'skills' => ['UI Design', 'UX Design', 'Figma', 'Prototyping'],
            ],
            [
                'title' => 'مسار Testing و QA',
                'description' => 'تعلم اختبار البرمجيات وضمان الجودة.',
                'objectives' => 'Unit Testing، Integration Testing، Automation Testing، QA Practices',
                'level' => 'intermediate',
                'duration_hours' => 50,
                'price' => 549,
                'is_free' => false,
                'is_featured' => false,
                'category' => 'Quality Assurance',
                'requirements' => 'معرفة أساسية بالبرمجة',
                'what_you_learn' => 'Testing Strategies، Selenium، Jest، QA Best Practices',
                'skills' => ['Testing', 'QA', 'Selenium', 'Jest'],
            ],
        ];

        $created = 0;
        $sectionsCreated = 0;
        $lessonsCreated = 0;

        foreach ($courses as $courseData) {
            // التحقق من وجود الكورس أولاً
            $course = AdvancedCourse::where('title', $courseData['title'])->first();
            
            if ($course) {
                echo "ℹ️  الكورس موجود مسبقاً: {$courseData['title']}\n";
                continue;
            }
            
            // إنشاء الكورس باستخدام DB facade لتوفير الذاكرة
            $courseId = DB::table('advanced_courses')->insertGetId([
                'title' => $courseData['title'],
                'description' => $courseData['description'] ?? null,
                'objectives' => $courseData['objectives'] ?? null,
                'level' => $courseData['level'] ?? 'beginner',
                'duration_hours' => $courseData['duration_hours'] ?? 0,
                'duration_minutes' => ($courseData['duration_hours'] ?? 0) * 60,
                'price' => $courseData['price'] ?? 0,
                'is_free' => $courseData['is_free'] ?? false,
                'is_featured' => $courseData['is_featured'] ?? false,
                'is_active' => true,
                'programming_language' => $courseData['programming_language'] ?? null,
                'framework' => $courseData['framework'] ?? null,
                'category' => $courseData['category'] ?? null,
                'requirements' => $courseData['requirements'] ?? null,
                'what_you_learn' => $courseData['what_you_learn'] ?? null,
                'skills' => isset($courseData['skills']) ? json_encode($courseData['skills']) : null,
                'instructor_id' => $instructorId,
                'academic_year_id' => $academicYear->id ?? null,
                'academic_subject_id' => $academicSubject->id ?? null,
                'rating' => rand(40, 50) / 10, // تقييم بين 4.0 و 5.0
                'reviews_count' => rand(10, 100),
                'students_count' => rand(50, 500),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            $created++;
            echo "✅ تم إنشاء كورس: {$courseData['title']} - السعر: " . ($courseData['price'] ?? 0) . " ج.م\n";

            // إضافة Sections و Lessons للكورس
            $sections = $this->getCourseSections($courseData['title']);
            $sectionOrder = 1;
            
            foreach ($sections as $sectionData) {
                $sectionId = DB::table('course_sections')->insertGetId([
                    'advanced_course_id' => $courseId,
                    'title' => $sectionData['title'],
                    'description' => $sectionData['description'] ?? null,
                    'order' => $sectionOrder++,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                
                $sectionsCreated++;
                
                // إضافة Lessons للـ Section
                $lessonOrder = 1;
                $lessonsToInsert = [];
                foreach ($sectionData['lessons'] ?? [] as $lessonData) {
                    $lessonsToInsert[] = [
                        'advanced_course_id' => $courseId,
                        'title' => $lessonData['title'],
                        'description' => $lessonData['description'] ?? null,
                        'type' => $lessonData['type'] ?? 'video',
                        'content' => $lessonData['content'] ?? null,
                        'duration_minutes' => $lessonData['duration_minutes'] ?? rand(15, 60),
                        'order' => $lessonOrder++,
                        'is_free' => $lessonData['is_free'] ?? false,
                        'is_active' => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
                
                if (!empty($lessonsToInsert)) {
                    DB::table('course_lessons')->insert($lessonsToInsert);
                    $lessonsCreated += count($lessonsToInsert);
                }
            }
            
            // تنظيف الذاكرة
            unset($sections);
        }

        echo "\n🎉 تم إنشاء {$created} كورس، {$sectionsCreated} قسم، و {$lessonsCreated} درس بنجاح!\n";
        echo "=" . str_repeat("=", 60) . "\n";
    }

    /**
     * الحصول على Sections و Lessons لكل كورس
     */
    private function getCourseSections($courseTitle): array
    {
        $sectionsMap = [
            'مقدمة في البرمجة - JavaScript' => [
                [
                    'title' => 'المقدمة والأساسيات',
                    'description' => 'تعلم أساسيات JavaScript',
                    'lessons' => [
                        ['title' => 'ما هو JavaScript؟', 'type' => 'video', 'duration_minutes' => 20, 'is_free' => true],
                        ['title' => 'إعداد البيئة التطويرية', 'type' => 'video', 'duration_minutes' => 15],
                        ['title' => 'المتغيرات والأنواع', 'type' => 'video', 'duration_minutes' => 30],
                        ['title' => 'اختبار: أساسيات JavaScript', 'type' => 'quiz', 'duration_minutes' => 10],
                    ],
                ],
                [
                    'title' => 'الدوال والكائنات',
                    'description' => 'تعلم الدوال والكائنات في JavaScript',
                    'lessons' => [
                        ['title' => 'إنشاء واستخدام الدوال', 'type' => 'video', 'duration_minutes' => 35],
                        ['title' => 'الكائنات والخصائص', 'type' => 'video', 'duration_minutes' => 40],
                        ['title' => 'تمرين: بناء آلة حاسبة', 'type' => 'assignment', 'duration_minutes' => 60],
                    ],
                ],
            ],
            'Python للمبتدئين' => [
                [
                    'title' => 'مقدمة Python',
                    'description' => 'تعلم أساسيات Python',
                    'lessons' => [
                        ['title' => 'ما هو Python؟', 'type' => 'video', 'duration_minutes' => 25, 'is_free' => true],
                        ['title' => 'تثبيت Python', 'type' => 'video', 'duration_minutes' => 20],
                        ['title' => 'أول برنامج Python', 'type' => 'video', 'duration_minutes' => 30],
                    ],
                ],
                [
                    'title' => 'البيانات والعمليات',
                    'description' => 'تعلم أنواع البيانات والعمليات',
                    'lessons' => [
                        ['title' => 'أنواع البيانات', 'type' => 'video', 'duration_minutes' => 35],
                        ['title' => 'العمليات الحسابية', 'type' => 'video', 'duration_minutes' => 30],
                        ['title' => 'السلاسل النصية', 'type' => 'video', 'duration_minutes' => 40],
                    ],
                ],
            ],
            'تطوير الويب الكامل - Full Stack' => [
                [
                    'title' => 'HTML & CSS',
                    'description' => 'أساسيات HTML و CSS',
                    'lessons' => [
                        ['title' => 'مقدمة HTML', 'type' => 'video', 'duration_minutes' => 30, 'is_free' => true],
                        ['title' => 'CSS الأساسيات', 'type' => 'video', 'duration_minutes' => 40],
                        ['title' => 'Flexbox و Grid', 'type' => 'video', 'duration_minutes' => 50],
                    ],
                ],
                [
                    'title' => 'JavaScript',
                    'description' => 'تعلم JavaScript',
                    'lessons' => [
                        ['title' => 'JavaScript الأساسيات', 'type' => 'video', 'duration_minutes' => 45],
                        ['title' => 'DOM Manipulation', 'type' => 'video', 'duration_minutes' => 50],
                    ],
                ],
                [
                    'title' => 'React',
                    'description' => 'تعلم React',
                    'lessons' => [
                        ['title' => 'مقدمة React', 'type' => 'video', 'duration_minutes' => 40],
                        ['title' => 'Components و Props', 'type' => 'video', 'duration_minutes' => 45],
                    ],
                ],
            ],
            'مسار تطوير تطبيقات Android' => [
                [
                    'title' => 'مقدمة Android',
                    'description' => 'تعلم أساسيات تطوير Android',
                    'lessons' => [
                        ['title' => 'ما هو Android؟', 'type' => 'video', 'duration_minutes' => 25, 'is_free' => true],
                        ['title' => 'إعداد Android Studio', 'type' => 'video', 'duration_minutes' => 30],
                        ['title' => 'أول تطبيق Android', 'type' => 'video', 'duration_minutes' => 40],
                    ],
                ],
                [
                    'title' => 'Kotlin الأساسيات',
                    'description' => 'تعلم لغة Kotlin',
                    'lessons' => [
                        ['title' => 'مقدمة Kotlin', 'type' => 'video', 'duration_minutes' => 35],
                        ['title' => 'البيانات والدوال', 'type' => 'video', 'duration_minutes' => 40],
                    ],
                ],
            ],
            'مسار الذكاء الاصطناعي و Machine Learning' => [
                [
                    'title' => 'مقدمة الذكاء الاصطناعي',
                    'description' => 'تعلم أساسيات AI',
                    'lessons' => [
                        ['title' => 'ما هو الذكاء الاصطناعي؟', 'type' => 'video', 'duration_minutes' => 30, 'is_free' => true],
                        ['title' => 'أنواع Machine Learning', 'type' => 'video', 'duration_minutes' => 40],
                    ],
                ],
                [
                    'title' => 'TensorFlow',
                    'description' => 'تعلم TensorFlow',
                    'lessons' => [
                        ['title' => 'مقدمة TensorFlow', 'type' => 'video', 'duration_minutes' => 45],
                        ['title' => 'بناء أول نموذج', 'type' => 'video', 'duration_minutes' => 50],
                    ],
                ],
            ],
        ];

        // إذا لم يكن هناك sections محددة للكورس، نضيف sections افتراضية
        if (!isset($sectionsMap[$courseTitle])) {
            return [
                [
                    'title' => 'المقدمة',
                    'description' => 'مقدمة الكورس',
                    'lessons' => [
                        ['title' => 'مقدمة الكورس', 'type' => 'video', 'duration_minutes' => 20, 'is_free' => true],
                        ['title' => 'نظرة عامة', 'type' => 'video', 'duration_minutes' => 15],
                    ],
                ],
                [
                    'title' => 'المحتوى الأساسي',
                    'description' => 'المحتوى الأساسي للكورس',
                    'lessons' => [
                        ['title' => 'الدرس الأول', 'type' => 'video', 'duration_minutes' => 30],
                        ['title' => 'الدرس الثاني', 'type' => 'video', 'duration_minutes' => 35],
                        ['title' => 'تمرين عملي', 'type' => 'assignment', 'duration_minutes' => 60],
                    ],
                ],
            ];
        }

        return $sectionsMap[$courseTitle];
    }
}
