<?php

namespace App\Domains\Advising;

use Illuminate\Support\ServiceProvider;

/**
 * الإرشاد المهني (docs/ADVISING.md): جلسات إرشاد مجانية تساعد الطالب يعرف احتياجاته ويدخل سوق العمل.
 *
 * الـ routes بتتحمّل بـ require من routes/web/auth.php (العام) و routes/web/admin.php (الإدارة)،
 * زي باقي المجالات، عشان ترتيب الـ routes ومجموعات الإدارة يفضل في مكان واحد.
 */
class AdvisingServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // view('advising::admin.bookings.index') و view('advising::public.book')
        $this->loadViewsFrom(__DIR__.'/resources/views', 'advising');
    }
}
