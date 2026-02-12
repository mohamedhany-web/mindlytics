<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PageController extends Controller
{
    // Home page is handled by welcome.blade.php route

    public function about()
    {
        $stats = [
            'courses' => \App\Models\AdvancedCourse::where('is_active', true)->count(),
            'students' => \App\Models\User::where('role', 'student')->where('is_active', true)->count(),
            'instructors' => \App\Models\User::where('role', 'instructor')->where('is_active', true)->count(),
        ];
        
        return view('public.about', compact('stats'));
    }

    public function faq()
    {
        $faqs = \App\Models\FAQ::active()
            ->orderBy('order')
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('category');
        
        $categories = \App\Models\FAQ::active()
            ->distinct()
            ->pluck('category')
            ->filter()
            ->values();

        $defaultFaqs = $this->getDefaultFaqs();
        
        return view('public.faq', compact('faqs', 'categories', 'defaultFaqs'));
    }

    /**
     * أسئلة شائعة افتراضية مرتبطة بمنصة Mindlytics (تعليم، كورسات، تسجيل، دفع، شهادات)
     */
    private function getDefaultFaqs(): array
    {
        return [
            [
                'question' => 'ما هي منصة Mindlytics؟',
                'answer' => 'Mindlytics منصة تعليمية عربية متخصصة في البرمجة وتطوير المهارات التقنية. نقدم كورسات ومسارات تعليمية منظمة مع شهادات معتمدة، ومتابعة للتحصيل، ودعم فني للطلاب.',
                'category' => 'المنصة',
            ],
            [
                'question' => 'كيف أبدأ التعلم على المنصة؟',
                'answer' => 'أنشئ حساباً مجانياً من صفحة "إنشاء حساب"، ثم تصفح الكورسات أو المسارات التعليمية. يمكنك التسجيل في الكورسات المجانية مباشرة، أو إتمام عملية الدفع للكورسات المدفوعة من صفحة كل كورس.',
                'category' => 'التسجيل والتعلم',
            ],
            [
                'question' => 'ما طرق الدفع المتاحة؟',
                'answer' => 'نقبل التحويل البنكي، المحفظة الإلكترونية (فودافون كاش، إنستا باي)، والدفع الإلكتروني. عند اختيار طريقة الدفع ستظهر لك التفاصيل ورقم الحساب، وتُرفق صورة إيصال الدفع ليتم تفعيل الكورس بعد المراجعة.',
                'category' => 'الدفع والأسعار',
            ],
            [
                'question' => 'متى يُفعّل الكورس بعد الدفع؟',
                'answer' => 'بعد رفع إيصال الدفع يتم مراجعة الطلب من فريقنا. عند الموافقة يُفعّل الكورس تلقائياً على حسابك وستجدونه في "كورساتي" أو "المسار التعليمي" حسب نوع التسجيل. عادةً خلال 24 ساعة عمل.',
                'category' => 'الدفع والأسعار',
            ],
            [
                'question' => 'هل تُصدر شهادات إتمام؟',
                'answer' => 'نعم. عند إتمام متطلبات الكورس أو المسار يمكنك الحصول على شهادة إتمام معتمدة من المنصة. الشهادات قابلة للمشاركة والتحقق عبر صفحة التحقق من الشهادات.',
                'category' => 'الشهادات والإنجازات',
            ],
            [
                'question' => 'ما الفرق بين الكورس والمسار التعليمي؟',
                'answer' => 'الكورس مادة واحدة متخصصة (مثلاً لغة برمجة أو مهارة محددة). المسار التعليمي مجموعة كورسات مرتبة معاً لتحقيق هدف أكبر (مثل مسار "مطور الويب"). التسجيل في المسار يمنحك الوصول لجميع الكورسات ضمنه.',
                'category' => 'التسجيل والتعلم',
            ],
            [
                'question' => 'هل يمكنني الحصول على استرداد أو إلغاء؟',
                'answer' => 'سياسة الاسترداد والإلغاء مُوضحة في صفحة "سياسة الاسترداد". بشكل عام يمكن مراجعة الطلبات في حالات محددة خلال فترة معينة من الشراء. للتفاصيل تواصل معنا عبر صفحة اتصل بنا.',
                'category' => 'الدفع والأسعار',
            ],
            [
                'question' => 'كيف أتواصل مع الدعم أو الإدارة؟',
                'answer' => 'من خلال صفحة "اتصل بنا" على الموقع. يمكنك أيضاً مراجعة قسم "المساعدة" للأسئلة الإضافية. نحرص على الرد خلال أقرب وقت ممكن.',
                'category' => 'الدعم والمساعدة',
            ],
        ];
    }

    public function terms()
    {
        return view('public.terms');
    }

    public function privacy()
    {
        return view('public.privacy');
    }

    public function pricing()
    {
        // جلب الباقات النشطة من قاعدة البيانات
        $packages = \App\Models\Package::active()
            ->with(['courses' => function($query) {
                $query->where('is_active', true);
            }])
            ->withCount('courses')
            ->orderBy('is_popular', 'desc') // الباقات الشائعة أولاً
            ->orderBy('is_featured', 'desc') // ثم المميزة
            ->orderBy('order')
            ->orderBy('price', 'asc') // ثم حسب السعر
            ->get();
        
        return view('public.pricing', compact('packages'));
    }

    public function team()
    {
        return view('public.team');
    }

    public function certificates()
    {
        return view('public.certificates');
    }

    public function help()
    {
        return view('public.help');
    }

    public function refund()
    {
        return view('public.refund');
    }

    public function testimonials()
    {
        return view('public.testimonials');
    }

    public function events()
    {
        return view('public.events');
    }

    public function partners()
    {
        return view('public.partners');
    }
}
