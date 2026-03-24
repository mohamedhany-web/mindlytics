<?php

namespace App\Console\Commands;

use App\Http\Controllers\Admin\InvoiceController;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;

/**
 * يستدعي InvoiceController::store مباشرةً (نفس منطق الويب دون CSRF) لتشخيص فشل إنشاء الفاتورة.
 * الاستخدام: php artisan invoice:test-store
 */
class TestInvoiceStoreCommand extends Command
{
    protected $signature = 'invoice:test-store {--cleanup : حذف الفاتورة التجريبية بعد النجاح}';

    protected $description = 'تشغيل حفظ فاتورة كما في لوحة التحكم (تجاوز middleware)';

    public function handle(): int
    {
        $admin = User::query()
            ->where(function ($q) {
                $q->whereIn('role', ['super_admin', 'admin'])
                    ->orWhereHas('roles', fn ($r) => $r->whereIn('name', ['super_admin', 'admin']));
            })
            ->orderByRaw("FIELD(role, 'super_admin', 'admin')")
            ->first();

        if (! $admin) {
            $this->error('لا يوجد مستخدم إداري (super_admin/admin).');
            return self::FAILURE;
        }

        $student = User::query()->where('role', 'student')->where('is_active', true)->first();
        if (! $student) {
            $this->error('لا يوجد طالب نشط.');
            return self::FAILURE;
        }

        $this->info('Admin: ' . $admin->id . ' ' . $admin->email . ' role_column=' . ($admin->role ?? '(null)'));
        $this->info('Student: ' . $student->id);

        $col = DB::selectOne("SHOW COLUMNS FROM invoices WHERE Field = 'description'");
        $this->info('DB invoices.description: Null=' . ($col->Null ?? '?') . ' Type=' . ($col->Type ?? '?'));

        Auth::login($admin);
        RateLimiter::clear('create-invoice:' . $admin->id);

        $request = Request::create('/admin/invoices', 'POST', [
            'user_id' => (string) $student->id,
            'type' => 'course',
            'description' => '',
            'subtotal' => '100',
            'tax_amount' => '0',
            'discount_amount' => '0',
            'due_date' => now()->addDays(7)->format('Y-m-d'),
            'notes' => '',
        ]);

        $before = Invoice::query()->count();

        try {
            /** @var \Illuminate\Http\RedirectResponse $response */
            $response = app(InvoiceController::class)->store($request);
        } catch (\Throwable $e) {
            $this->error('استثناء: ' . $e->getMessage());
            $this->line($e->getFile() . ':' . $e->getLine());
            Auth::logout();
            return self::FAILURE;
        }

        Auth::logout();

        $after = Invoice::query()->count();
        $this->info('Invoices count: ' . $before . ' -> ' . $after);
        $this->info('Response status: ' . $response->getStatusCode());
        $this->info('Target: ' . $response->getTargetUrl());

        if ($after <= $before) {
            $this->warn('لم تُضف فاتورة (ربما تحقق أو إعادة توجيه بخطأ). تحقق من الجلسة في المتصفح.');
            if (method_exists($response, 'getSession')) {
                $sess = $response->getSession();
                if ($sess && $sess->has('errors')) {
                    $this->error('أخطاء التحقق: ' . json_encode($sess->get('errors')->toArray(), JSON_UNESCAPED_UNICODE));
                }
                if ($sess && $sess->has('error')) {
                    $this->error('رسالة: ' . $sess->get('error'));
                }
            }
            return self::FAILURE;
        }

        $latest = Invoice::query()->latest('id')->first();
        $this->info('آخر فاتورة: ' . $latest->invoice_number . ' description=' . var_export($latest->description, true));

        if ($this->option('cleanup')) {
            $latest->delete();
            $this->info('تم حذف الفاتورة التجريبية.');
        }

        return self::SUCCESS;
    }
}
