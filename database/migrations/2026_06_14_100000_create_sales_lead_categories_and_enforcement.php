<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('sales_lead_categories')) {
            Schema::create('sales_lead_categories', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug', 80)->unique();
                $table->string('color', 16)->default('#059669');
                $table->text('description')->nullable();
                $table->boolean('is_active')->default(true);
                $table->unsignedSmallInteger('sort_order')->default(0);
                $table->timestamps();
            });

            $now = now();
            $defaults = [
                ['name' => 'دورات أونلاين', 'slug' => 'online-courses', 'color' => '#059669', 'sort_order' => 1],
                ['name' => 'كورسات أوفلاين', 'slug' => 'offline-courses', 'color' => '#0284c7', 'sort_order' => 2],
                ['name' => 'شركات / B2B', 'slug' => 'b2b-corporate', 'color' => '#7c3aed', 'sort_order' => 3],
                ['name' => 'فعاليات وورش', 'slug' => 'events-workshops', 'color' => '#d97706', 'sort_order' => 4],
                ['name' => 'إحالات', 'slug' => 'referrals', 'color' => '#db2777', 'sort_order' => 5],
                ['name' => 'بيانات عامة', 'slug' => 'general', 'color' => '#64748b', 'sort_order' => 6],
            ];

            foreach ($defaults as $row) {
                DB::table('sales_lead_categories')->insert(array_merge($row, [
                    'description' => null,
                    'is_active' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]));
            }
        }

        if (Schema::hasTable('sales_leads')) {
            Schema::table('sales_leads', function (Blueprint $table) {
                if (! Schema::hasColumn('sales_leads', 'category_id')) {
                    $table->foreignId('category_id')->nullable()->after('created_by')
                        ->constrained('sales_lead_categories')->nullOnDelete();
                }
                if (! Schema::hasColumn('sales_leads', 'import_batch')) {
                    $table->string('import_batch', 64)->nullable()->after('category_id');
                    $table->index('import_batch');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('sales_leads')) {
            Schema::table('sales_leads', function (Blueprint $table) {
                if (Schema::hasColumn('sales_leads', 'category_id')) {
                    $table->dropConstrainedForeignId('category_id');
                }
                if (Schema::hasColumn('sales_leads', 'import_batch')) {
                    $table->dropColumn('import_batch');
                }
            });
        }

        Schema::dropIfExists('sales_lead_categories');
    }
};
