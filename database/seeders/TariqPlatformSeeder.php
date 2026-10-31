<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Domains\Learning\Models\School;
use App\Domains\Learning\Models\Subject;
use App\Domains\Learning\Models\Course;
use App\Domains\Learning\Models\Lesson;
use App\Domains\Learning\Models\QuestionBank;
use App\Domains\Learning\Models\Question;
use App\Domains\Learning\Models\Exam;

class TariqPlatformSeeder extends Seeder
{
    public function run(): void
    {
        echo "\n🔥 إنشاء منصة الطارق في الرياضيات - مستر طارق الداجن 🔥\n";
        echo "=" . str_repeat("=", 60) . "\n";

        // إنشاء المدرسة الرئيسية
        $school = School::firstOrCreate(
            ['name' => 'مدرسة الطارق في الرياضيات'],
            [
                'description' => 'مدرسة متخصصة في تعليم الرياضيات بأحدث الطرق التعليمية مع مستر طارق الداجن',
                'address' => 'المملكة العربية السعودية',
                'phone' => '+966501234567',
                'email' => 'info@tariq-math.com',
                'is_active' => true,
            ]
        );

        // إنشاء المستخدمين
        echo "📱 إنشاء المستخدمين...\n";

        // الأدمن الرئيسي - مستر طارق
        $admin = User::firstOrCreate(
            ['phone' => '0501111111'],
            [
                'name' => 'مستر طارق الداجن',
                'email' => 'tariq@tariq-math.com',
                'password' => Hash::make('admin2024'),
                'role' => 'admin',
                'is_active' => true,
                'bio' => 'خبير في تدريس الرياضيات لأكثر من 15 عاماً، متخصص في المناهج السعودية وطرق التدريس الحديثة.',
            ]
        );

        // مدرسين مساعدين
        $teachers = [
            [
                'name' => 'أستاذ أحمد محمد',
                'phone' => '0502222222',
                'email' => 'ahmed@tariq-math.com',
                'bio' => 'مدرس رياضيات ثانوية، متخصص في الجبر والهندسة.',
            ],
            [
                'name' => 'أستاذة فاطمة علي',
                'phone' => '0503333333',
                'email' => 'fatima@tariq-math.com',
                'bio' => 'مدرسة رياضيات متوسط وثانوي، خبيرة في الإحصاء والاحتمالات.',
            ],
        ];

        foreach ($teachers as $teacherData) {
            User::firstOrCreate(
                ['phone' => $teacherData['phone']],
                [
                    'name' => $teacherData['name'],
                    'email' => $teacherData['email'],
                    'password' => Hash::make('teacher2024'),
                    'role' => 'teacher',
                    'is_active' => true,
                    'bio' => $teacherData['bio'],
                ]
            );
        }

        // طلاب نموذجيين
        $students = [
            ['name' => 'محمد سعد العتيبي', 'phone' => '0504444444', 'grade' => 'ثالث ثانوي'],
            ['name' => 'عبدالله أحمد المطيري', 'phone' => '0505555555', 'grade' => 'ثاني ثانوي'],
            ['name' => 'سارة محمد القحطاني', 'phone' => '0506666666', 'grade' => 'أول ثانوي'],
            ['name' => 'نورا عبدالعزيز السعد', 'phone' => '0507777777', 'grade' => 'ثالث متوسط'],
            ['name' => 'عمر خالد الزهراني', 'phone' => '0508888888', 'grade' => 'ثاني متوسط'],
        ];

        foreach ($students as $studentData) {
            User::firstOrCreate(
                ['phone' => $studentData['phone']],
                [
                    'name' => $studentData['name'],
                    'email' => null,
                    'password' => Hash::make('student2024'),
                    'role' => 'student',
                    'is_active' => true,
                    'bio' => 'طالب في ' . $studentData['grade'],
                ]
            );
        }

        // أولياء أمور
        $parents = [
            ['name' => 'سعد بن محمد العتيبي', 'phone' => '0509999999'],
            ['name' => 'أحمد بن عبدالله المطيري', 'phone' => '0500000001'],
            ['name' => 'محمد بن فهد القحطاني', 'phone' => '0500000002'],
        ];

        foreach ($parents as $parentData) {
            User::firstOrCreate(
                ['phone' => $parentData['phone']],
                [
                    'name' => $parentData['name'],
                    'email' => null,
                    'password' => Hash::make('parent2024'),
                    'role' => 'parent',
                    'is_active' => true,
                    'bio' => 'ولي أمر',
                ]
            );
        }

        // إنشاء المواد الدراسية
        echo "📚 إنشاء المواد الدراسية...\n";

        $subjects = [
            [
                'name' => 'الرياضيات - المرحلة المتوسطة',
                'description' => 'مادة الرياضيات للصفوف المتوسطة (أول - ثاني - ثالث متوسط)',
                'color' => '#3B82F6',
                'icon' => 'fa-calculator',
            ],
            [
                'name' => 'الرياضيات - المرحلة الثانوية',
                'description' => 'مادة الرياضيات للصفوف الثانوية (أول - ثاني - ثالث ثانوي)',
                'color' => '#8B5CF6',
                'icon' => 'fa-square-root-alt',
            ],
            [
                'name' => 'الجبر والمعادلات',
                'description' => 'دراسة الجبر والمعادلات الخطية والتربيعية',
                'color' => '#EF4444',
                'icon' => 'fa-function',
            ],
            [
                'name' => 'الهندسة والقياس',
                'description' => 'الهندسة المستوية والفراغية وحساب المساحات والحجوم',
                'color' => '#10B981',
                'icon' => 'fa-shapes',
            ],
            [
                'name' => 'الإحصاء والاحتمالات',
                'description' => 'الإحصاء الوصفي والاستنتاجي ونظرية الاحتمالات',
                'color' => '#F59E0B',
                'icon' => 'fa-chart-bar',
            ],
        ];

        foreach ($subjects as $subjectData) {
            Subject::firstOrCreate(
                ['name' => $subjectData['name']],
                $subjectData
            );
        }

        // إنشاء الكورسات
        echo "🎓 إنشاء الكورسات التعليمية...\n";

        $mathSubject = Subject::where('name', 'الرياضيات - المرحلة الثانوية')->first();
        $algebraSubject = Subject::where('name', 'الجبر والمعادلات')->first();
        $geometrySubject = Subject::where('name', 'الهندسة والقياس')->first();

        $courses = [
            [
                'title' => 'أساسيات الجبر - الصف الأول الثانوي',
                'description' => 'كورس شامل لأساسيات الجبر للصف الأول الثانوي يشمل المعادلات الخطية والتربيعية والمتباينات',
                'subject_id' => $algebraSubject->id,
                'teacher_id' => $admin->id,
                'status' => 'published',
                'duration_minutes' => 1200, // 20 ساعة
                'is_free' => false,
                'price' => 299.00,
                'content' => 'هذا الكورس مصمم خصيصاً لطلاب الصف الأول الثانوي ويغطي جميع موضوعات الجبر الأساسية بطريقة مبسطة ومفهومة.',
            ],
            [
                'title' => 'الهندسة التحليلية - الصف الثاني الثانوي',
                'description' => 'دراسة شاملة للهندسة التحليلية تشمل المستقيمات والدوائر والقطوع المخروطية',
                'subject_id' => $geometrySubject->id,
                'teacher_id' => $admin->id,
                'status' => 'published',
                'duration_minutes' => 900, // 15 ساعة
                'is_free' => false,
                'price' => 399.00,
                'content' => 'كورس متقدم في الهندسة التحليلية يساعد الطلاب على فهم العلاقات الهندسية باستخدام الرياضيات.',
            ],
            [
                'title' => 'التفاضل والتكامل - الصف الثالث الثانوي',
                'description' => 'مقدمة شاملة لحساب التفاضل والتكامل للطلاب المتقدمين',
                'subject_id' => $mathSubject->id,
                'teacher_id' => $admin->id,
                'status' => 'published',
                'duration_minutes' => 1500, // 25 ساعة
                'is_free' => false,
                'price' => 499.00,
                'content' => 'الكورس الأكثر تقدماً في المنصة، يغطي أساسيات التفاضل والتكامل بطريقة احترافية.',
            ],
            [
                'title' => 'مراجعة شاملة للثانوية العامة',
                'description' => 'مراجعة شاملة لجميع موضوعات الرياضيات للثانوية العامة مع حل نماذج امتحانية',
                'subject_id' => $mathSubject->id,
                'teacher_id' => $admin->id,
                'status' => 'published',
                'duration_minutes' => 2000, // 33+ ساعة
                'is_free' => true,
                'price' => 0.00,
                'content' => 'كورس مجاني لمساعدة طلاب الثانوية العامة على التفوق في مادة الرياضيات.',
            ],
        ];

        foreach ($courses as $courseData) {
            Course::firstOrCreate(
                ['title' => $courseData['title']],
                $courseData
            );
        }

        // إنشاء الدروس
        echo "📖 إنشاء الدروس والمحتوى...\n";

        $algebraCourse = Course::where('title', 'أساسيات الجبر - الصف الأول الثانوي')->first();
        $geometryCourse = Course::where('title', 'الهندسة التحليلية - الصف الثاني الثانوي')->first();
        $calculusCourse = Course::where('title', 'التفاضل والتكامل - الصف الثالث الثانوي')->first();

        $lessons = [
            // دروس الجبر
            [
                'title' => 'مقدمة في الجبر والمتغيرات',
                'description' => 'شرح مفهوم المتغيرات والثوابت في الجبر',
                'course_id' => $algebraCourse->id,
                'order' => 1,
                'duration_minutes' => 45,
                'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                'content' => 'في هذا الدرس سنتعلم أساسيات الجبر والتعامل مع المتغيرات والثوابت.',
                'is_free' => true,
                'status' => 'published',
            ],
            [
                'title' => 'حل المعادلات الخطية',
                'description' => 'طرق حل المعادلات الخطية بمتغير واحد',
                'course_id' => $algebraCourse->id,
                'order' => 2,
                'duration_minutes' => 60,
                'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                'content' => 'سنتعلم جميع طرق حل المعادلات الخطية مع أمثلة تطبيقية.',
                'is_free' => false,
                'status' => 'published',
            ],
            [
                'title' => 'المعادلات التربيعية',
                'description' => 'حل المعادلات التربيعية بالطرق المختلفة',
                'course_id' => $algebraCourse->id,
                'order' => 3,
                'duration_minutes' => 75,
                'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                'content' => 'دراسة شاملة للمعادلات التربيعية وطرق حلها.',
                'is_free' => false,
                'status' => 'published',
            ],
            // دروس الهندسة
            [
                'title' => 'نظام الإحداثيات الديكارتية',
                'description' => 'مقدمة في نظام الإحداثيات وتمثيل النقاط',
                'course_id' => $geometryCourse->id,
                'order' => 1,
                'duration_minutes' => 50,
                'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                'content' => 'سنتعلم أساسيات نظام الإحداثيات وكيفية تمثيل النقاط والأشكال.',
                'is_free' => true,
                'status' => 'published',
            ],
            [
                'title' => 'معادلة المستقيم',
                'description' => 'اشتقاق وفهم معادلة المستقيم بصيغها المختلفة',
                'course_id' => $geometryCourse->id,
                'order' => 2,
                'duration_minutes' => 65,
                'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                'content' => 'دراسة تفصيلية لمعادلة المستقيم وتطبيقاتها.',
                'is_free' => false,
                'status' => 'published',
            ],
        ];

        foreach ($lessons as $lessonData) {
            Lesson::firstOrCreate(
                ['title' => $lessonData['title'], 'course_id' => $lessonData['course_id']],
                $lessonData
            );
        }

        // إنشاء بنوك الأسئلة
        echo "❓ إنشاء بنوك الأسئلة...\n";

        $questionBanks = [
            [
                'title' => 'بنك أسئلة الجبر - المستوى الأساسي',
                'description' => 'مجموعة من الأسئلة الأساسية في الجبر',
                'subject_id' => $algebraSubject->id,
                'created_by' => $admin->id,
                'difficulty' => 'easy',
            ],
            [
                'title' => 'بنك أسئلة الهندسة - المستوى المتوسط',
                'description' => 'أسئلة متوسطة الصعوبة في الهندسة التحليلية',
                'subject_id' => $geometrySubject->id,
                'created_by' => $admin->id,
                'difficulty' => 'medium',
            ],
            [
                'title' => 'بنك أسئلة التفاضل - المستوى المتقدم',
                'description' => 'أسئلة متقدمة في التفاضل والتكامل',
                'subject_id' => $mathSubject->id,
                'created_by' => $admin->id,
                'difficulty' => 'hard',
            ],
        ];

        foreach ($questionBanks as $bankData) {
            QuestionBank::firstOrCreate(
                ['title' => $bankData['title']],
                $bankData
            );
        }

        // إنشاء أسئلة نموذجية
        $algebraBank = QuestionBank::where('title', 'بنك أسئلة الجبر - المستوى الأساسي')->first();
        $geometryBank = QuestionBank::where('title', 'بنك أسئلة الهندسة - المستوى المتوسط')->first();

        $questions = [
            // أسئلة الجبر
            [
                'question_bank_id' => $algebraBank->id,
                'question' => 'حل المعادلة التالية: 2x + 5 = 13',
                'type' => 'multiple_choice',
                'options' => ['x = 4', 'x = 9', 'x = 6', 'x = 8'],
                'correct_answer' => 'x = 4',
                'explanation' => '2x + 5 = 13 => 2x = 8 => x = 4',
                'points' => 2,
            ],
            [
                'question_bank_id' => $algebraBank->id,
                'question' => 'هل المعادلة 3x - 6 = 0 لها حل واحد؟',
                'type' => 'true_false',
                'options' => ['صحيح', 'خطأ'],
                'correct_answer' => 'صحيح',
                'explanation' => 'المعادلة الخطية لها حل واحد فقط وهو x = 2',
                'points' => 1,
            ],
            // أسئلة الهندسة
            [
                'question_bank_id' => $geometryBank->id,
                'question' => 'ما هو ميل المستقيم المار بالنقطتين (2,3) و (4,7)؟',
                'type' => 'multiple_choice',
                'options' => ['2', '3', '1', '4'],
                'correct_answer' => '2',
                'explanation' => 'الميل = (7-3)/(4-2) = 4/2 = 2',
                'points' => 3,
            ],
        ];

        foreach ($questions as $questionData) {
            Question::firstOrCreate(
                ['question' => $questionData['question']],
                $questionData
            );
        }

        // طباعة بيانات الدخول
        echo "\n" . str_repeat("=", 60) . "\n";
        echo "🎉 تم إنشاء منصة الطارق في الرياضيات بنجاح! 🎉\n";
        echo str_repeat("=", 60) . "\n\n";

        echo "🔑 بيانات الدخول للمنصة:\n";
        echo "-" . str_repeat("-", 50) . "\n";
        echo "👑 الإدارة العامة (مستر طارق الداجن):\n";
        echo "   📱 الهاتف: 0501111111\n";
        echo "   🔒 كلمة المرور: admin2024\n\n";

        echo "👨‍🏫 المدرسين:\n";
        echo "   📱 أستاذ أحمد: 0502222222 / teacher2024\n";
        echo "   📱 أستاذة فاطمة: 0503333333 / teacher2024\n\n";

        echo "🎓 الطلاب (نماذج للاختبار):\n";
        echo "   📱 محمد العتيبي: 0504444444 / student2024\n";
        echo "   📱 عبدالله المطيري: 0505555555 / student2024\n";
        echo "   📱 سارة القحطاني: 0506666666 / student2024\n\n";

        echo "👨‍👩‍👧‍👦 أولياء الأمور:\n";
        echo "   📱 سعد العتيبي: 0509999999 / parent2024\n";
        echo "   📱 أحمد المطيري: 0500000001 / parent2024\n\n";

        echo "🌐 رابط المنصة: http://localhost:8000\n";
        echo "⚡ المنصة جاهزة للاستخدام مع جميع الميزات المتقدمة!\n";
        echo str_repeat("=", 60) . "\n";
    }
}
