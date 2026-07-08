<?php

use App\Models\EmployeeJob;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('sales_teams')) {
            Schema::create('sales_teams', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->text('description')->nullable();
                $table->foreignId('manager_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->index(['is_active', 'manager_id']);
            });
        }

        if (! Schema::hasTable('sales_team_members')) {
            Schema::create('sales_team_members', function (Blueprint $table) {
                $table->id();
                $table->foreignId('sales_team_id')->constrained('sales_teams')->cascadeOnDelete();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->string('role', 24)->default('member');
                $table->timestamp('joined_at')->nullable();
                $table->timestamps();

                $table->unique(['sales_team_id', 'user_id']);
                $table->unique('user_id');
            });
        }

        if (! Schema::hasTable('sales_lead_transfers')) {
            Schema::create('sales_lead_transfers', function (Blueprint $table) {
                $table->id();
                $table->foreignId('sales_lead_id')->constrained('sales_leads')->cascadeOnDelete();
                $table->foreignId('from_user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('to_user_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('transferred_by')->constrained('users')->cascadeOnDelete();
                $table->foreignId('sales_team_id')->nullable()->constrained('sales_teams')->nullOnDelete();
                $table->text('reason')->nullable();
                $table->timestamps();

                $table->index(['sales_team_id', 'created_at']);
            });
        }

        if (! Schema::hasTable('sales_team_daily_reports')) {
            Schema::create('sales_team_daily_reports', function (Blueprint $table) {
                $table->id();
                $table->foreignId('sales_team_id')->constrained('sales_teams')->cascadeOnDelete();
                $table->foreignId('manager_id')->constrained('users')->cascadeOnDelete();
                $table->date('report_date');
                $table->string('status', 24)->default('draft');
                $table->timestamp('submitted_at')->nullable();
                $table->unsignedInteger('team_members_count')->nullable();
                $table->unsignedInteger('reports_received')->nullable();
                $table->unsignedInteger('total_calls')->nullable();
                $table->unsignedInteger('total_leads_qualified')->nullable();
                $table->unsignedInteger('total_bookings')->nullable();
                $table->text('team_summary')->nullable();
                $table->text('performance_notes')->nullable();
                $table->text('challenges')->nullable();
                $table->text('recommendations')->nullable();
                $table->timestamps();

                $table->unique(['sales_team_id', 'report_date']);
                $table->index(['report_date', 'status']);
            });
        }

        if (Schema::hasTable('sales_daily_reports')) {
            Schema::table('sales_daily_reports', function (Blueprint $table) {
                if (! Schema::hasColumn('sales_daily_reports', 'manager_reviewed_at')) {
                    $table->timestamp('manager_reviewed_at')->nullable()->after('submitted_at');
                }
                if (! Schema::hasColumn('sales_daily_reports', 'manager_reviewed_by')) {
                    $table->foreignId('manager_reviewed_by')->nullable()->after('manager_reviewed_at')
                        ->constrained('users')->nullOnDelete();
                }
                if (! Schema::hasColumn('sales_daily_reports', 'sales_team_id')) {
                    $table->foreignId('sales_team_id')->nullable()->after('manager_reviewed_by')
                        ->constrained('sales_teams')->nullOnDelete();
                }
            });
        }

        EmployeeJob::query()->updateOrCreate(
            ['code' => 'sales_manager'],
            [
                'name' => 'مدير مبيعات',
                'description' => 'إدارة فريق المبيعات، متابعة الأداء، تحويل العملاء بين أعضاء الفريق، ورفع التقارير للإدارة.',
                'responsibilities' => 'متابعة فريق السيلز؛ مراقبة المحادثات والحضور؛ تحويل Leads؛ مراجعة التقارير اليومية؛ رفع تقرير الفريق للإدارة.',
                'permissions' => null,
                'is_active' => true,
            ]
        );
    }

    public function down(): void
    {
        if (Schema::hasTable('sales_daily_reports')) {
            Schema::table('sales_daily_reports', function (Blueprint $table) {
                if (Schema::hasColumn('sales_daily_reports', 'sales_team_id')) {
                    $table->dropConstrainedForeignId('sales_team_id');
                }
                if (Schema::hasColumn('sales_daily_reports', 'manager_reviewed_by')) {
                    $table->dropConstrainedForeignId('manager_reviewed_by');
                }
                if (Schema::hasColumn('sales_daily_reports', 'manager_reviewed_at')) {
                    $table->dropColumn('manager_reviewed_at');
                }
            });
        }

        Schema::dropIfExists('sales_team_daily_reports');
        Schema::dropIfExists('sales_lead_transfers');
        Schema::dropIfExists('sales_team_members');
        Schema::dropIfExists('sales_teams');

        EmployeeJob::query()->where('code', 'sales_manager')->delete();
    }
};
