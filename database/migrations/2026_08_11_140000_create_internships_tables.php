<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('internships', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('department')->nullable();
            $table->text('summary')->nullable();
            $table->longText('description')->nullable();
            $table->longText('requirements')->nullable();
            $table->longText('benefits')->nullable();
            $table->string('location')->nullable();
            $table->string('type', 20)->default('onsite'); // onsite | remote | hybrid
            $table->string('duration')->nullable(); // e.g. 3 أشهر
            $table->unsignedInteger('seats')->nullable();
            $table->string('status', 20)->default('draft'); // draft | open | closed
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->timestamp('application_deadline')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_published')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->integer('sort_order')->default(0);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['status', 'is_published']);
            $table->index(['is_featured', 'sort_order']);
            $table->index('application_deadline');
        });

        Schema::create('internship_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('internship_id')->constrained('internships')->cascadeOnDelete();
            $table->string('name');
            $table->string('email');
            $table->string('phone', 60)->nullable();
            $table->string('university')->nullable();
            $table->string('major')->nullable();
            $table->string('year_of_study', 50)->nullable();
            $table->string('cv_path')->nullable();
            $table->string('portfolio_url', 500)->nullable();
            $table->string('github_url', 500)->nullable();
            $table->string('linkedin_url', 500)->nullable();
            $table->text('cover_letter')->nullable();
            $table->string('status', 20)->default('pending'); // pending | reviewed | accepted | rejected
            $table->text('admin_notes')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('source', 40)->default('website');
            $table->timestamps();

            $table->index(['internship_id', 'status']);
            $table->index(['email', 'internship_id']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('internship_applications');
        Schema::dropIfExists('internships');
    }
};
