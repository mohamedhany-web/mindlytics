<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('hr_job_postings')) {
            Schema::create('hr_job_postings', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->string('department')->nullable();
                $table->string('location')->nullable();
                $table->string('employment_type')->nullable(); // full_time, part_time, contract...
                $table->longText('description')->nullable();
                $table->longText('requirements')->nullable();
                $table->boolean('is_published')->default(false)->index();
                $table->timestamp('published_at')->nullable()->index();
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();

                $table->index(['is_published', 'published_at'], 'hr_jobs_publish_idx');
            });
        }

        if (! Schema::hasTable('hr_job_applications')) {
            Schema::create('hr_job_applications', function (Blueprint $table) {
                $table->id();
                $table->foreignId('job_posting_id')->constrained('hr_job_postings')->cascadeOnDelete();
                $table->string('full_name');
                $table->string('email')->nullable()->index();
                $table->string('phone')->nullable()->index();
                $table->string('linkedin_url')->nullable();
                $table->string('portfolio_url')->nullable();
                $table->longText('cover_letter')->nullable();
                $table->string('status', 30)->default('new')->index(); // new, screening, interview, offer, hired, rejected
                $table->string('source', 60)->nullable(); // website, referral, other...
                $table->timestamp('submitted_at')->nullable()->index();
                $table->timestamps();

                $table->index(['job_posting_id', 'status'], 'hr_app_job_status_idx');
            });
        }

        if (! Schema::hasTable('hr_application_files')) {
            Schema::create('hr_application_files', function (Blueprint $table) {
                $table->id();
                $table->foreignId('job_application_id')->constrained('hr_job_applications')->cascadeOnDelete();
                $table->string('kind', 20)->default('cv')->index(); // cv, attachment
                $table->string('disk', 50)->default('r2');
                $table->string('path', 1024);
                $table->string('original_name')->nullable();
                $table->string('mime', 120)->nullable();
                $table->unsignedBigInteger('size')->nullable();
                $table->timestamps();

                $table->index(['job_application_id', 'kind'], 'hr_app_files_kind_idx');
            });
        }

        if (! Schema::hasTable('hr_rubrics')) {
            Schema::create('hr_rubrics', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->json('criteria_json'); // [{key,label,weight,max,help?}, ...]
                $table->boolean('is_default')->default(false)->index();
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('hr_application_scores')) {
            Schema::create('hr_application_scores', function (Blueprint $table) {
                $table->id();
                $table->foreignId('job_application_id')->constrained('hr_job_applications')->cascadeOnDelete();
                $table->foreignId('rubric_id')->constrained('hr_rubrics')->cascadeOnDelete();
                $table->json('scores_json'); // {criterionKey: score}
                $table->decimal('total_score', 8, 2)->default(0)->index();
                $table->text('notes')->nullable();
                $table->foreignId('scored_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamp('scored_at')->nullable()->index();
                $table->timestamps();

                $table->unique(['job_application_id'], 'hr_app_score_unique');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('hr_application_scores');
        Schema::dropIfExists('hr_rubrics');
        Schema::dropIfExists('hr_application_files');
        Schema::dropIfExists('hr_job_applications');
        Schema::dropIfExists('hr_job_postings');
    }
};

