<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('sales_interest_types')) {
            Schema::create('sales_interest_types', function (Blueprint $table) {
                $table->id();
                $table->string('slug', 64)->unique();
                $table->string('name_ar');
                $table->string('name_en')->nullable();
                $table->string('color', 16)->default('#059669');
                $table->unsignedInteger('sort_order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        $defaults = [
            ['slug' => 'courses', 'name_ar' => 'كورسات', 'name_en' => 'Courses', 'color' => '#0284c7', 'sort_order' => 10],
            ['slug' => 'diplomas', 'name_ar' => 'دبلومات', 'name_en' => 'Diplomas', 'color' => '#7c3aed', 'sort_order' => 20],
            ['slug' => 'workshops', 'name_ar' => 'ورش', 'name_en' => 'Workshops', 'color' => '#d97706', 'sort_order' => 30],
            ['slug' => 'b2b', 'name_ar' => 'B2B / شركات', 'name_en' => 'B2B', 'color' => '#0f766e', 'sort_order' => 40],
            ['slug' => 'other', 'name_ar' => 'أخرى', 'name_en' => 'Other', 'color' => '#64748b', 'sort_order' => 90],
        ];

        foreach ($defaults as $row) {
            $exists = DB::table('sales_interest_types')->where('slug', $row['slug'])->exists();
            if (! $exists) {
                DB::table('sales_interest_types')->insert(array_merge($row, [
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]));
            }
        }

        if (! Schema::hasTable('sales_user_specialties')) {
            Schema::create('sales_user_specialties', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('interest_type_id')->constrained('sales_interest_types')->cascadeOnDelete();
                $table->timestamps();
                $table->unique(['user_id', 'interest_type_id']);
            });
        }

        Schema::table('sales_leads', function (Blueprint $table) {
            if (! Schema::hasColumn('sales_leads', 'interest_type_id')) {
                $table->foreignId('interest_type_id')->nullable()->after('interest')
                    ->constrained('sales_interest_types')->nullOnDelete();
            }
        });

        Schema::table('sales_lead_transfers', function (Blueprint $table) {
            if (! Schema::hasColumn('sales_lead_transfers', 'source')) {
                $table->string('source', 32)->nullable()->after('reason')->index();
            }
            if (! Schema::hasColumn('sales_lead_transfers', 'interest_type_id')) {
                $table->foreignId('interest_type_id')->nullable()->after('source')
                    ->constrained('sales_interest_types')->nullOnDelete();
            }
        });

        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'sales_reports_to_id')) {
                $table->foreignId('sales_reports_to_id')->nullable()->after('employee_job_id')
                    ->constrained('users')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'sales_reports_to_id')) {
                try {
                    $table->dropConstrainedForeignId('sales_reports_to_id');
                } catch (\Throwable) {
                    $table->dropColumn('sales_reports_to_id');
                }
            }
        });

        Schema::table('sales_lead_transfers', function (Blueprint $table) {
            if (Schema::hasColumn('sales_lead_transfers', 'interest_type_id')) {
                try {
                    $table->dropConstrainedForeignId('interest_type_id');
                } catch (\Throwable) {
                    $table->dropColumn('interest_type_id');
                }
            }
            if (Schema::hasColumn('sales_lead_transfers', 'source')) {
                $table->dropColumn('source');
            }
        });

        Schema::table('sales_leads', function (Blueprint $table) {
            if (Schema::hasColumn('sales_leads', 'interest_type_id')) {
                try {
                    $table->dropConstrainedForeignId('interest_type_id');
                } catch (\Throwable) {
                    $table->dropColumn('interest_type_id');
                }
            }
        });

        Schema::dropIfExists('sales_user_specialties');
        Schema::dropIfExists('sales_interest_types');
    }
};
