<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('geo_ip_lookups', function (Blueprint $table) {
            $table->id();
            $table->string('ip', 45)->unique();
            $table->string('country_code', 8)->nullable()->index();
            $table->string('country_name', 120)->nullable();
            $table->string('region_name', 120)->nullable();
            $table->string('city', 120)->nullable();
            $table->json('raw')->nullable();
            $table->timestamp('looked_up_at')->nullable();
            $table->timestamps();
        });

        Schema::create('marketing_region_daily_stats', function (Blueprint $table) {
            $table->id();
            $table->date('stat_date')->index();
            $table->string('country_code', 8)->index();
            $table->unsignedInteger('visits')->default(0);
            $table->timestamps();
            $table->unique(['stat_date', 'country_code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('marketing_region_daily_stats');
        Schema::dropIfExists('geo_ip_lookups');
    }
};
