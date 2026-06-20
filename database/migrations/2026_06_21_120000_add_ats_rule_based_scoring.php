<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('hr_job_postings')) {
            Schema::table('hr_job_postings', function (Blueprint $table) {
                if (! Schema::hasColumn('hr_job_postings', 'required_skills')) {
                    $table->json('required_skills')->nullable()->after('requirements');
                }
                if (! Schema::hasColumn('hr_job_postings', 'required_experience')) {
                    $table->unsignedTinyInteger('required_experience')->nullable()->after('required_skills');
                }
                if (! Schema::hasColumn('hr_job_postings', 'required_education')) {
                    $table->string('required_education', 40)->nullable()->after('required_experience');
                }
                if (! Schema::hasColumn('hr_job_postings', 'status')) {
                    $table->string('status', 20)->default('open')->index()->after('required_education');
                }
            });
        }

        if (Schema::hasTable('hr_job_applications')) {
            Schema::table('hr_job_applications', function (Blueprint $table) {
                if (! Schema::hasColumn('hr_job_applications', 'parsed_skills')) {
                    $table->json('parsed_skills')->nullable()->after('cover_letter');
                }
                if (! Schema::hasColumn('hr_job_applications', 'parsed_education')) {
                    $table->string('parsed_education', 40)->nullable()->after('parsed_skills');
                }
                if (! Schema::hasColumn('hr_job_applications', 'parsed_experience_years')) {
                    $table->decimal('parsed_experience_years', 5, 1)->nullable()->after('parsed_education');
                }
                if (! Schema::hasColumn('hr_job_applications', 'skills_score')) {
                    $table->decimal('skills_score', 5, 2)->nullable()->after('parsed_experience_years');
                }
                if (! Schema::hasColumn('hr_job_applications', 'experience_score')) {
                    $table->decimal('experience_score', 5, 2)->nullable()->after('skills_score');
                }
                if (! Schema::hasColumn('hr_job_applications', 'education_score')) {
                    $table->decimal('education_score', 5, 2)->nullable()->after('experience_score');
                }
                if (! Schema::hasColumn('hr_job_applications', 'auto_score')) {
                    $table->decimal('auto_score', 5, 2)->nullable()->index()->after('education_score');
                }
                if (! Schema::hasColumn('hr_job_applications', 'scoring_notes')) {
                    $table->json('scoring_notes')->nullable()->after('auto_score');
                }
                if (! Schema::hasColumn('hr_job_applications', 'scored_at')) {
                    $table->timestamp('scored_at')->nullable()->after('scoring_notes');
                }
            });

            $this->migrateApplicationStatuses();
        }

        if (! Schema::hasTable('hr_application_skills')) {
            Schema::create('hr_application_skills', function (Blueprint $table) {
                $table->id();
                $table->foreignId('job_application_id')->constrained('hr_job_applications')->cascadeOnDelete();
                $table->string('skill_name', 120);
                $table->timestamps();

                $table->index(['job_application_id', 'skill_name'], 'hr_app_skill_idx');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('hr_application_skills');

        if (Schema::hasTable('hr_job_applications')) {
            Schema::table('hr_job_applications', function (Blueprint $table) {
                foreach ([
                    'parsed_skills', 'parsed_education', 'parsed_experience_years',
                    'skills_score', 'experience_score', 'education_score',
                    'auto_score', 'scoring_notes', 'scored_at',
                ] as $col) {
                    if (Schema::hasColumn('hr_job_applications', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }

        if (Schema::hasTable('hr_job_postings')) {
            Schema::table('hr_job_postings', function (Blueprint $table) {
                foreach (['required_skills', 'required_experience', 'required_education', 'status'] as $col) {
                    if (Schema::hasColumn('hr_job_postings', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }
    }

    private function migrateApplicationStatuses(): void
    {
        $map = [
            'new' => 'applied',
            'screening' => 'under_review',
            'offer' => 'accepted',
            'hired' => 'accepted',
        ];

        foreach ($map as $from => $to) {
            DB::table('hr_job_applications')->where('status', $from)->update(['status' => $to]);
        }
    }
};
