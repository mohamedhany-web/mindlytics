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
        
        return view('public.faq', compact('faqs', 'categories'));
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
