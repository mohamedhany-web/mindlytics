<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('offline_locations', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('اسم المكان');
            $table->text('address')->nullable()->comment('العنوان');
            $table->string('city')->nullable()->comment('المدينة');
            $table->string('phone')->nullable()->comment('رقم الهاتف');
            $table->text('description')->nullable()->comment('الوصف');
            $table->integer('capacity')->default(0)->comment('السعة');
            $table->json('facilities')->nullable()->comment('المرافق');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index('is_active');
            $table->index('city');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('offline_locations');
    }
};
