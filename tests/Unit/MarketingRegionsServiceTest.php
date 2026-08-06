<?php

namespace Tests\Unit;

use App\Services\GeoIpLookupService;
use App\Services\MarketingRegionsService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Tests\TestCase;

class MarketingRegionsServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::dropIfExists('geo_ip_lookups');
        Schema::create('geo_ip_lookups', function (Blueprint $table) {
            $table->id();
            $table->string('ip', 45)->unique();
            $table->string('country_code', 8)->nullable();
            $table->string('country_name', 120)->nullable();
            $table->string('region_name', 120)->nullable();
            $table->string('city', 120)->nullable();
            $table->json('raw')->nullable();
            $table->timestamp('looked_up_at')->nullable();
            $table->timestamps();
        });
    }

    public function test_geo_ip_skips_private_and_caches_public(): void
    {
        $geo = new GeoIpLookupService;
        $this->assertTrue($geo->isPrivateOrLocal('127.0.0.1'));
        $this->assertTrue($geo->isPrivateOrLocal('192.168.1.1'));

        Http::fake([
            'ip-api.com/*' => Http::response([
                'status' => 'success',
                'country' => 'Egypt',
                'countryCode' => 'EG',
                'regionName' => 'Cairo',
                'city' => 'Cairo',
            ], 200),
        ]);

        $result = $geo->lookup('8.8.8.8');
        $this->assertSame('EG', $result['country_code']);
        $this->assertSame('Cairo', $result['city']);

        Http::fake([
            'ip-api.com/*' => Http::response(['status' => 'fail'], 200),
        ]);
        $cached = $geo->lookup('8.8.8.8');
        $this->assertSame('EG', $cached['country_code']);
    }

    public function test_country_from_phone_via_dashboard_merge_shape(): void
    {
        Schema::dropIfExists('users');
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('role')->nullable();
            $table->timestamps();
        });

        \DB::table('users')->insert([
            ['name' => 'A', 'email' => 'a@t.com', 'phone' => '+201012345678', 'role' => 'student', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'B', 'email' => 'b@t.com', 'phone' => '+966501234567', 'role' => 'student', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'C', 'email' => 'c@t.com', 'phone' => '+201112223334', 'role' => 'student', 'created_at' => now(), 'updated_at' => now()],
        ]);

        Schema::dropIfExists('activity_logs');
        Schema::dropIfExists('marketing_region_daily_stats');
        Schema::dropIfExists('marketing_customer_surveys');

        $service = app(MarketingRegionsService::class);
        $data = $service->dashboard(now()->subDay()->toDateString(), now()->toDateString(), 'registrations');

        $this->assertSame(3, $data['summary']['registrations']);
        $codes = collect($data['phone_countries'])->pluck('country_code')->all();
        $this->assertContains('EG', $codes);
        $this->assertContains('SA', $codes);
        $this->assertSame(2, collect($data['phone_countries'])->firstWhere('country_code', 'EG')['count'] ?? 0);
    }
}
