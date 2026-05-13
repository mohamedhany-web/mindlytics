<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('branches', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique()->comment('لـ subdomain، أحرف لاتينية صغيرة وأرقام وشرطات');
            $table->string('custom_domain')->nullable()->unique()->comment('دومين مخصص للفرع بعد ربط DNS');
            $table->string('country_code', 2)->nullable();
            $table->string('timezone')->nullable();
            $table->string('currency', 3)->default('EGP');
            $table->boolean('is_active')->default(true);
            $table->string('primary_color', 7)->nullable()->comment('مثال #2563eb');
            $table->string('logo_path')->nullable();
            $table->text('internal_notes')->nullable()->comment('للإدارة المركزية فقط');
            $table->json('settings')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('branches');
    }
};
