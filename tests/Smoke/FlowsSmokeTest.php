<?php

namespace Tests\Smoke;

use App\Domains\Accounting\Models\Invoice;
use App\Domains\Accounting\Models\Order;
use App\Domains\Accounting\Models\Payment;
use App\Domains\Accounting\Models\Transaction;
use App\Domains\Accounting\Models\Wallet;
use App\Domains\Accounting\Models\WalletTransaction;
use App\Domains\Community\Models\JourneyProfile;
use App\Domains\Learning\Models\AdvancedCourse;
use App\Domains\Learning\Models\CurriculumItem;
use App\Domains\Learning\Models\Lecture;
use App\Domains\Learning\Models\StudentCourseEnrollment;
use App\Domains\Offline\Models\OfflineCourse;
use App\Domains\Offline\Models\OfflineCourseSection;
use App\Domains\Offline\Models\OfflineCurriculumItem;
use App\Domains\Offline\Models\OfflineLecture;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * المسارات الحساسة من أولها لآخرها على نسخة MySQL (كل شيء يُتراجع عنه بعد الاختبار):
 * اعتماد الطلب وما يُنشئه من أرصدة، والقيم المحفوظة بأسماء الكلاسات القديمة في المنهج والمجتمع.
 */
class FlowsSmokeTest extends SmokeTestCase
{
    public function test_order_approval_creates_one_consistent_record_set_and_skips_used_numbers(): void
    {
        $admin = $this->user('super_admin');
        $student = $this->user('student');
        $course = $this->course($this->user('instructor'), 800);
        $wallet = Wallet::create(['name' => 'Smoke Wallet', 'type' => 'vodafone_cash', 'balance' => 0, 'is_active' => true]);
        $order = Order::forceCreate([
            'user_id' => $student->id, 'advanced_course_id' => $course->id,
            'original_amount' => 800, 'discount_amount' => 0, 'amount' => 800,
            'payment_method' => 'bank_transfer', 'wallet_id' => $wallet->id, 'status' => Order::STATUS_PENDING,
        ]);

        // الرقم الذي كانت الصيغة القديمة count()+1 ستولّده عند الاعتماد يجب أن يكون محجوزاً (كما يحدث بعد حذف فاتورة)
        $oldFormula = fn () => 'INV-'.str_pad((string) (Invoice::count() + 1), 8, '0', STR_PAD_LEFT);
        for ($i = 0; $i < 5 && ! Invoice::where('invoice_number', $oldFormula())->exists(); $i++) {
            // رقم حر: إما الرقم الذي سيولّده count()+1 بعد هذا الإدخال، أو رقم غير رقمي إن كان محجوزاً
            $next = 'INV-'.str_pad((string) (Invoice::count() + 2), 8, '0', STR_PAD_LEFT);
            Invoice::forceCreate([
                'invoice_number' => Invoice::where('invoice_number', $next)->exists() ? 'INV-SMOKE-'.Str::random(8) : $next,
                'user_id' => $student->id, 'type' => 'other', 'description' => 'smoke placeholder',
                'subtotal' => 0, 'tax_amount' => 0, 'discount_amount' => 0, 'total_amount' => 0, 'status' => 'cancelled', 'due_date' => now(),
            ]);
        }
        $taken = $oldFormula();
        $this->assertTrue(Invoice::where('invoice_number', $taken)->exists(), 'the old formula must point at a used number for this scenario');

        $this->actingAs($admin)->postJson("/admin/orders/{$order->id}/approve")
            ->assertOk()->assertJson(['success' => true]);

        $order->refresh();
        $this->assertSame(Order::STATUS_APPROVED, $order->status);
        $invoice = Invoice::findOrFail($order->invoice_id);
        $this->assertNotSame($taken, $invoice->invoice_number);
        $this->assertEquals(800, (float) $invoice->total_amount);

        $payments = Payment::where('invoice_id', $invoice->id)->get();
        $this->assertCount(1, $payments);
        $this->assertEquals(800, (float) $payments[0]->amount);
        $transactions = Transaction::where('payment_id', $payments[0]->id)->get();
        $this->assertCount(1, $transactions);
        $this->assertSame('credit', $transactions[0]->type);

        $movements = WalletTransaction::where('wallet_id', $wallet->id)->get();
        $this->assertCount(1, $movements);
        $this->assertEquals(800, (float) $wallet->fresh()->balance);
        $this->assertEquals(0, (float) $movements[0]->balance_before);
        $this->assertEquals(800, (float) $movements[0]->balance_after);

        $this->assertSame('active', StudentCourseEnrollment::where('user_id', $student->id)->where('advanced_course_id', $course->id)->value('status'));

        // اعتماد ثانٍ لنفس الطلب يُرفض بدون أي سجلات إضافية
        $second = $this->actingAs($admin)->postJson("/admin/orders/{$order->id}/approve");
        $this->assertContains($second->getStatusCode(), [400, 409]);
        $this->assertSame(1, Payment::where('invoice_id', $invoice->id)->count());
        $this->assertSame(1, WalletTransaction::where('wallet_id', $wallet->id)->count());
        $this->assertEquals(800, (float) $wallet->fresh()->balance);
    }

    public function test_online_curriculum_item_keeps_legacy_type_and_reaches_the_student(): void
    {
        $instructor = $this->user('instructor');
        $student = $this->user('student');
        $course = $this->course($instructor, 0);

        $this->actingAs($instructor)->postJson("/instructor/courses/{$course->id}/sections", ['title' => 'Smoke Section'])
            ->assertOk()->assertJson(['success' => true]);
        $sectionId = DB::table('course_sections')->where('advanced_course_id', $course->id)->value('id');
        $lecture = Lecture::forceCreate([
            'course_id' => $course->id, 'instructor_id' => $instructor->id, 'title' => 'Smoke Lecture '.Str::random(6),
            'scheduled_at' => now()->subDay(), 'duration_minutes' => 30, 'status' => 'completed',
        ]);

        // نفس ما يرسله الـ JavaScript في صفحة المنهج
        $this->actingAs($instructor)->postJson("/instructor/sections/{$sectionId}/items", ['item_type' => 'App\\Models\\Lecture', 'item_id' => $lecture->id])
            ->assertOk()->assertJson(['success' => true]);
        $this->assertSame('App\\Models\\Lecture', CurriculumItem::where('item_id', $lecture->id)->value('item_type'));
        $this->assertInstanceOf(Lecture::class, CurriculumItem::where('item_id', $lecture->id)->first()->item);

        StudentCourseEnrollment::forceCreate(['user_id' => $student->id, 'advanced_course_id' => $course->id, 'status' => 'active', 'enrolled_at' => now(), 'activated_at' => now(), 'progress' => 0]);
        $this->actingAs($student)->get("/my-courses/{$course->id}/learn")->assertOk()->assertSee($lecture->title);
        $this->actingAs($student)->getJson("/my-courses/{$course->id}/lectures/{$lecture->id}")->assertOk()->assertJsonPath('title', $lecture->title);
    }

    public function test_offline_curriculum_attach_and_note_keep_legacy_types(): void
    {
        $instructor = $this->user('instructor');
        $offline = OfflineCourse::forceCreate(['title' => 'Smoke Offline Course', 'instructor_id' => $instructor->id, 'max_students' => 20, 'status' => 'active']);

        $this->actingAs($instructor)->post("/instructor/offline-courses/{$offline->id}/curriculum/sections", ['title' => 'Smoke Offline Section'])->assertRedirect();
        $section = OfflineCourseSection::where('offline_course_id', $offline->id)->firstOrFail();
        $lecture = OfflineLecture::forceCreate(['offline_course_id' => $offline->id, 'instructor_id' => $instructor->id, 'title' => 'Smoke Offline Lecture']);

        $page = $this->actingAs($instructor)->get("/instructor/offline-courses/{$offline->id}/curriculum")->assertOk()->getContent();
        $this->assertStringContainsString('name="item_type" value="App\\Models\\OfflineLecture"', html_entity_decode($page), 'the form must send the stored (legacy) type');

        $this->actingAs($instructor)->post("/instructor/offline-courses/{$offline->id}/curriculum/attach-item", [
            'offline_course_section_id' => $section->id, 'item_type' => 'App\\Models\\OfflineLecture', 'item_id' => $lecture->id,
        ])->assertRedirect()->assertSessionHas('success');
        $this->actingAs($instructor)->post("/instructor/offline-courses/{$offline->id}/curriculum/sections/{$section->id}/notes", ['title' => 'Smoke note', 'body' => 'x'])
            ->assertRedirect();

        $types = OfflineCurriculumItem::where('offline_course_section_id', $section->id)->orderBy('id')->pluck('item_type')->all();
        $this->assertSame(['App\\Models\\OfflineLecture', 'App\\Models\\OfflineCurriculumNote'], $types);
        $this->actingAs($instructor)->get("/instructor/offline-courses/{$offline->id}/curriculum")->assertOk()->assertSee('Smoke Offline Lecture')->assertSee('محاضرة');
    }

    public function test_community_reaction_and_journey_share_keep_legacy_types(): void
    {
        $student = $this->user('student');
        $course = $this->course($this->user('instructor'), 0);
        StudentCourseEnrollment::forceCreate(['user_id' => $student->id, 'advanced_course_id' => $course->id, 'status' => 'active', 'enrolled_at' => now(), 'activated_at' => now(), 'progress' => 0]);

        $postId = $this->actingAs($student, 'sanctum')->postJson("/api/v1/student/community/courses/{$course->id}/posts", ['body' => 'Smoke post'])
            ->assertCreated()->json('post_id');
        $this->actingAs($student, 'sanctum')->postJson("/api/v1/student/community/posts/{$postId}/react", ['type' => 'like', 'reaction' => 'like'])->assertOk();

        $this->assertSame('App\\Models\\CourseCommunityPost', DB::table('course_community_reactions')->where('reactable_id', $postId)->value('reactable_type'));
        $feed = json_encode($this->actingAs($student, 'sanctum')->getJson("/api/v1/student/community/courses/{$course->id}/feed")->assertOk()->json());
        $this->assertMatchesRegularExpression('/"likes":1/', $feed, 'the like must be counted in the feed');

        $profile = JourneyProfile::forceCreate(['user_id' => $student->id, 'slug' => 'smoke-'.Str::lower(Str::random(8))]);
        $this->actingAs($student)->postJson('/journey/share-track', ['shareable_type' => 'profile', 'shareable_id' => $profile->id, 'channel' => 'copy'])->assertOk();
        $this->assertSame('App\\Models\\JourneyProfile', DB::table('journey_share_events')->where('shareable_id', $profile->id)->value('shareable_type'));
    }

    public function test_models_cached_under_old_class_names_still_load(): void
    {
        $course = $this->course($this->user('instructor'), 0);
        $new = AdvancedCourse::class;
        $old = 'App\\Models\\AdvancedCourse';
        $legacy = str_replace('O:'.strlen($new).':"'.$new.'"', 'O:'.strlen($old).':"'.$old.'"', serialize($course));

        $this->assertStringStartsWith('O:'.strlen($old).':"'.$old.'"', $legacy);
        $restored = unserialize($legacy);
        $this->assertInstanceOf(AdvancedCourse::class, $restored);
        $this->assertSame($course->id, $restored->id);
    }

    private function user(string $role): User
    {
        return User::forceCreate([
            'name' => "Smoke {$role}", 'email' => 'smoke-'.$role.'-'.Str::lower(Str::random(8)).'@example.test',
            'phone' => '+2010'.random_int(10000000, 99999999), 'password' => Hash::make('password'), 'role' => $role, 'is_active' => true,
        ]);
    }

    private function course(User $instructor, float $price): AdvancedCourse
    {
        return AdvancedCourse::forceCreate([
            'title' => 'Smoke Course '.Str::random(6), 'instructor_id' => $instructor->id, 'price' => $price,
            'is_active' => true, 'is_free' => $price <= 0, 'is_scholarship_only' => false,
        ]);
    }
}
