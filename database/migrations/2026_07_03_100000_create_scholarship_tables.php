<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('scholarship_programs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->foreignId('instructor_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('advanced_course_id')->nullable()->constrained('advanced_courses')->nullOnDelete();
            $table->boolean('is_active')->default(true);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['is_active', 'starts_at', 'ends_at']);
        });

        if (Schema::hasTable('advanced_courses')) {
            Schema::table('advanced_courses', function (Blueprint $table) {
                if (! Schema::hasColumn('advanced_courses', 'is_scholarship_only')) {
                    $table->boolean('is_scholarship_only')->default(false)->after('is_featured');
                }
                if (! Schema::hasColumn('advanced_courses', 'scholarship_program_id')) {
                    $table->foreignId('scholarship_program_id')->nullable()->after('is_scholarship_only')
                        ->constrained('scholarship_programs')->nullOnDelete();
                }
            });
        }

        Schema::create('scholarship_registrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('scholarship_program_id')->constrained('scholarship_programs')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('status', 32)->default('registered');
            $table->timestamp('registered_at');
            $table->timestamp('activated_at')->nullable();
            $table->foreignId('activated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('student_course_enrollment_id')->nullable()
                ->constrained('student_course_enrollments')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['scholarship_program_id', 'user_id']);
            $table->index(['scholarship_program_id', 'status']);
        });

        if (Schema::hasTable('student_course_enrollments')) {
            Schema::table('student_course_enrollments', function (Blueprint $table) {
                if (! Schema::hasColumn('student_course_enrollments', 'scholarship_registration_id')) {
                    $table->foreignId('scholarship_registration_id')->nullable()->after('coupon_id')
                        ->constrained('scholarship_registrations')->nullOnDelete();
                }
            });
        }

        if (Schema::hasTable('student_course_enrollments')
            && Schema::hasColumn('student_course_enrollments', 'enrollment_type')) {
            \Illuminate\Support\Facades\DB::statement(
                "ALTER TABLE `student_course_enrollments` MODIFY `enrollment_type` ENUM('purchase','subscription','gift','trial','promotional','scholarship') NOT NULL DEFAULT 'purchase'"
            );
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('student_course_enrollments')
            && Schema::hasColumn('student_course_enrollments', 'scholarship_registration_id')) {
            Schema::table('student_course_enrollments', function (Blueprint $table) {
                $table->dropForeign(['scholarship_registration_id']);
                $table->dropColumn('scholarship_registration_id');
            });
        }

        Schema::dropIfExists('scholarship_registrations');

        if (Schema::hasTable('advanced_courses')) {
            Schema::table('advanced_courses', function (Blueprint $table) {
                if (Schema::hasColumn('advanced_courses', 'scholarship_program_id')) {
                    $table->dropForeign(['scholarship_program_id']);
                    $table->dropColumn('scholarship_program_id');
                }
                if (Schema::hasColumn('advanced_courses', 'is_scholarship_only')) {
                    $table->dropColumn('is_scholarship_only');
                }
            });
        }

        Schema::dropIfExists('scholarship_programs');
    }
};
