<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Schema;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * التحقق من أرقام الهواتف في صفحة التسجيل (الأردن + دول قائمة).
 * لا نستخدم RefreshDatabase لأن بعض الترحيلات خاصة بـ MySQL — ننشئ جدول users مبسطاً.
 */
class RegisterPhoneValidationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Bus::fake();

        Schema::create('branches', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->nullable();
            $table->string('custom_domain')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->string('password');
            $table->string('role')->default('student');
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('branch_id')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * @return array<string, array{string, string, string}>
     */
    public static function validPhones(): array
    {
        return [
            'JO local 07 form' => ['+962', '0791234567', '+962791234567'],
            'JO national 7 form' => ['+962', '781234567', '+962781234567'],
            'JO 77 prefix with spaces' => ['+962', '77 123 4567', '+962771234567'],
            'JO +962 international' => ['+962', '+962 79 123 4567', '+962791234567'],
            'JO 00962 international' => ['+962', '00962791234567', '+962791234567'],
            'JO 962 without plus' => ['+962', '962791234567', '+962791234567'],
            'JO +962 then local 0' => ['+962', '+962 0791234567', '+962791234567'],
            'EG local 01 form' => ['+20', '01012345678', '+201012345678'],
            'EG +20 international' => ['+20', '+201012345678', '+201012345678'],
            'SA national form' => ['+966', '501234567', '+966501234567'],
            'SA 00966 international' => ['+966', '00966501234567', '+966501234567'],
        ];
    }

    /**
     * @return array<string, array{string, string}>
     */
    public static function invalidPhones(): array
    {
        return [
            'JO old 8-digit form' => ['+962', '79123456'],
            'JO landline-like 76 prefix' => ['+962', '761234567'],
            'JO landline 06' => ['+962', '065123456'],
            'JO too long' => ['+962', '07912345678'],
            'JO Saudi number' => ['+962', '+966501234567'],
            'EG wrong prefix' => ['+20', '1312345678'],
            'SA too short' => ['+966', '50123456'],
        ];
    }

    #[DataProvider('validPhones')]
    public function test_valid_phone_registers_and_is_stored_normalized(string $dialCode, string $input, string $stored): void
    {
        $response = $this->post('/register', $this->payload($dialCode, $input));

        $response->assertSessionHasNoErrors();
        $this->assertSame($stored, User::where('email', 'student@example.com')->value('phone'));
    }

    #[DataProvider('invalidPhones')]
    public function test_invalid_phone_is_rejected(string $dialCode, string $input): void
    {
        $response = $this->from('/register')->post('/register', $this->payload($dialCode, $input));

        $response->assertRedirect('/register');
        $response->assertSessionHasErrors('phone');
        $this->assertSame(0, User::count());
    }

    /**
     * @return array<string, string>
     */
    private function payload(string $dialCode, string $phone): array
    {
        return [
            'name' => 'Test Student',
            'country_code' => $dialCode,
            'phone' => $phone,
            'email' => 'student@example.com',
            'password' => 'Str0ngPassw0rd99',
            'password_confirmation' => 'Str0ngPassw0rd99',
        ];
    }
}
