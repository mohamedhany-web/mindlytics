<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mobile_app_settings', function (Blueprint $table) {
            $table->id();
            $table->string('welcome_title_ar')->nullable();
            $table->string('welcome_title_en')->nullable();
            $table->string('welcome_subtitle_ar')->nullable();
            $table->string('welcome_subtitle_en')->nullable();
            $table->string('mission_headline_ar')->nullable();
            $table->string('mission_headline_en')->nullable();
            $table->text('mission_body_ar')->nullable();
            $table->text('mission_body_en')->nullable();
            $table->string('no_subscription_title_ar')->nullable();
            $table->string('no_subscription_title_en')->nullable();
            $table->text('no_subscription_body_ar')->nullable();
            $table->text('no_subscription_body_en')->nullable();
            $table->string('catalog_web_path', 255)->default('/courses');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mobile_app_settings');
    }
};
