<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('sales_leads')) {
            Schema::create('sales_leads', function (Blueprint $table) {
                $table->id();
                $table->foreignId('assigned_to')->constrained('users')->restrictOnDelete();
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->string('name');
                $table->string('phone')->nullable();
                $table->string('email')->nullable();
                $table->string('company')->nullable();
                $table->string('source', 64)->default('other');
                $table->string('stage', 32)->default('new');
                $table->text('interest')->nullable();
                $table->decimal('expected_value', 12, 2)->nullable();
                $table->text('notes')->nullable();
                $table->timestamp('next_follow_up_at')->nullable();
                $table->timestamp('closed_at')->nullable();
                $table->string('lost_reason')->nullable();
                $table->timestamps();
                $table->softDeletes();

                $table->index(['assigned_to', 'stage']);
                $table->index(['next_follow_up_at']);
                $table->index('stage');
            });
        }

        if (! Schema::hasTable('sales_activities')) {
            Schema::create('sales_activities', function (Blueprint $table) {
                $table->id();
                $table->foreignId('sales_lead_id')->constrained('sales_leads')->cascadeOnDelete();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->string('type', 32)->default('note');
                $table->string('title')->nullable();
                $table->text('body')->nullable();
                $table->json('meta')->nullable();
                $table->timestamps();

                $table->index(['sales_lead_id', 'created_at']);
            });
        }

        if (Schema::hasTable('employee_jobs')) {
            $exists = DB::table('employee_jobs')->where('code', 'sales')->exists();
            if (! $exists) {
                DB::table('employee_jobs')->insert([
                    'name' => 'مبيعات',
                    'code' => 'sales',
                    'description' => 'متابعة العملاء المحتملين وإغلاق الصفقات',
                    'responsibilities' => 'إدارة العملاء المحتملين، المتابعة، تسجيل النشاط',
                    'permissions' => json_encode([]),
                    'min_salary' => null,
                    'max_salary' => null,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('sales_activities');
        Schema::dropIfExists('sales_leads');

        if (Schema::hasTable('employee_jobs')) {
            DB::table('employee_jobs')->where('code', 'sales')->delete();
        }
    }
};
