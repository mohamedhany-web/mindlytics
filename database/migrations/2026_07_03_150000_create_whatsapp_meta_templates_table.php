<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('whatsapp_meta_templates', function (Blueprint $table) {
            $table->id();
            $table->string('meta_template_id')->nullable()->index();
            $table->string('name', 512);
            $table->string('language', 20);
            $table->enum('category', ['AUTHENTICATION', 'UTILITY', 'MARKETING'])->default('UTILITY');
            $table->enum('status', [
                'draft',
                'pending',
                'approved',
                'rejected',
                'paused',
                'disabled',
            ])->default('draft');
            $table->text('body_text');
            $table->string('header_type')->nullable(); // text, image, video, document
            $table->text('header_content')->nullable();
            $table->string('footer_text', 60)->nullable();
            $table->json('buttons')->nullable();
            $table->unsignedTinyInteger('body_variable_count')->default(0);
            $table->json('components')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->text('quality_score')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('meta_synced_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['name', 'language']);
            $table->index(['status', 'category']);
            $table->index('language');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('whatsapp_meta_templates');
    }
};
