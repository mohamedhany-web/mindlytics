<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales_policy_settings', function (Blueprint $table) {
            $table->id();
            $table->string('version', 20)->default('1.0');
            $table->date('effective_date')->nullable();
            $table->string('document_title')->default('دليل قواعد وسياسات فريق المبيعات');
            $table->string('document_title_en')->nullable();
            $table->text('intro_content')->nullable();
            $table->text('acknowledgement_content')->nullable();
            $table->timestamps();
        });

        Schema::create('sales_policy_sections', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('title_en')->nullable();
            $table->string('rules_range', 32)->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('sales_policy_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('section_id')->constrained('sales_policy_sections')->cascadeOnDelete();
            $table->string('rule_number', 32)->nullable();
            $table->string('title');
            $table->string('title_en')->nullable();
            $table->longText('content')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales_policy_rules');
        Schema::dropIfExists('sales_policy_sections');
        Schema::dropIfExists('sales_policy_settings');
    }
};
