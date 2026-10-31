<?php

namespace Tests\Smoke;

use App\Http\Middleware\PreventConcurrentSessions;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

/**
 * اختبارات دخان على قاعدة MySQL حقيقية (نسخة)، كل اختبار داخل transaction يُتراجع عنه.
 *
 * لا تعمل إلا إذا حُدّدت قاعدة اسمها ينتهي بـ _smoke، فلا يمكن تشغيلها على قاعدة التطوير أو الإنتاج بالخطأ:
 *   SMOKE_DATABASE=mindlytics_smoke php artisan test tests/Smoke
 * إنشاء النسخة محلياً (أو على سيرفر staging) موضّح في docs/DEPLOY_TESTING.md.
 */
abstract class SmokeTestCase extends TestCase
{
    use DatabaseTransactions;

    public function createApplication()
    {
        $app = parent::createApplication();

        $database = (string) getenv('SMOKE_DATABASE');
        if ($database !== '' && str_ends_with($database, '_smoke')) {
            $config = $app['config'];
            $config->set('database.default', 'mysql');
            $config->set('database.connections.mysql.database', $database);
            $app['db']->purge('mysql');
        }

        return $app;
    }

    protected function setUp(): void
    {
        $database = (string) getenv('SMOKE_DATABASE');
        if ($database === '') {
            $this->markTestSkipped('Smoke tests need SMOKE_DATABASE=<name>_smoke (a copy of the database).');
        }
        if (! str_ends_with($database, '_smoke')) {
            $this->fail("Refusing to run smoke tests on [{$database}]: the database name must end with _smoke.");
        }

        parent::setUp();

        // لا اتصال بخدمات خارجية (Meta، فواتيرك، كاشير، Bunny…) ولا بريد حقيقي
        Http::fake(['*' => Http::response(['data' => []], 200)]);
        Mail::fake();

        // كل طلب في الاختبار جلسة جديدة: منع الجلسات المتزامنة كان سيسجّل الخروج، و الـ throttle يرجع 429 لكثرة الطلبات
        $this->withoutMiddleware([PreventConcurrentSessions::class, ThrottleRequests::class]);
    }
}
