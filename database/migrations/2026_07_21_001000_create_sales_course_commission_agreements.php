<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales_course_commission_agreements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('course_type', 16); // advanced|offline|legacy
            $table->string('course_key', 48); // e.g. advanced:12
            $table->foreignId('advanced_course_id')->nullable()->constrained('advanced_courses')->nullOnDelete();
            $table->foreignId('offline_course_id')->nullable()->constrained('offline_courses')->nullOnDelete();
            $table->foreignId('course_id')->nullable()->constrained('courses')->nullOnDelete();
            $table->string('calc_mode', 32); // tier_course|tier_global|tier_course_global_count|fixed|percent
            $table->decimal('commission_value', 12, 2)->nullable();
            $table->json('tiers')->nullable();
            $table->string('tier_period', 16)->default('month'); // month|all
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['user_id', 'is_active']);
            $table->unique(['user_id', 'course_key'], 'scc_agr_user_course_unique');
        });

        Schema::table('sales_leads', function (Blueprint $table) {
            if (! Schema::hasColumn('sales_leads', 'course_type')) {
                $table->string('course_type', 16)->nullable()->after('interest');
            }
            if (! Schema::hasColumn('sales_leads', 'advanced_course_id')) {
                $table->foreignId('advanced_course_id')->nullable()->after('course_type')
                    ->constrained('advanced_courses')->nullOnDelete();
            }
            if (! Schema::hasColumn('sales_leads', 'offline_course_id')) {
                $table->foreignId('offline_course_id')->nullable()->after('advanced_course_id')
                    ->constrained('offline_courses')->nullOnDelete();
            }
            if (! Schema::hasColumn('sales_leads', 'course_id')) {
                $table->foreignId('course_id')->nullable()->after('offline_course_id')
                    ->constrained('courses')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('sales_leads', function (Blueprint $table) {
            if (Schema::hasColumn('sales_leads', 'course_id')) {
                $table->dropConstrainedForeignId('course_id');
            }
            if (Schema::hasColumn('sales_leads', 'offline_course_id')) {
                $table->dropConstrainedForeignId('offline_course_id');
            }
            if (Schema::hasColumn('sales_leads', 'advanced_course_id')) {
                $table->dropConstrainedForeignId('advanced_course_id');
            }
            if (Schema::hasColumn('sales_leads', 'course_type')) {
                $table->dropColumn('course_type');
            }
        });

        Schema::dropIfExists('sales_course_commission_agreements');
    }
};
