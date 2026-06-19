<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('moderator_mkt_calendar_events', function (Blueprint $table) {
            if (! Schema::hasColumn('moderator_mkt_calendar_events', 'content_type')) {
                $table->string('content_type', 40)->default('post')->after('body')->index();
            }
            if (! Schema::hasColumn('moderator_mkt_calendar_events', 'assigned_employee_id')) {
                $table->foreignId('assigned_employee_id')->nullable()->after('content_type')
                    ->constrained('users')->nullOnDelete();
            }
            if (! Schema::hasColumn('moderator_mkt_calendar_events', 'employee_task_id')) {
                $table->unsignedBigInteger('employee_task_id')->nullable()->after('assigned_employee_id');
                $table->index('employee_task_id', 'idx_mkt_ce_emp_task');
            }
            if (! Schema::hasColumn('moderator_mkt_calendar_events', 'requires_confirmation')) {
                $table->boolean('requires_confirmation')->default(true)->after('employee_task_id');
            }
            if (! Schema::hasColumn('moderator_mkt_calendar_events', 'execution_confirmed_at')) {
                $table->timestamp('execution_confirmed_at')->nullable()->after('requires_confirmation');
            }
            if (! Schema::hasColumn('moderator_mkt_calendar_events', 'execution_confirmed_by')) {
                $table->foreignId('execution_confirmed_by')->nullable()->after('execution_confirmed_at')
                    ->constrained('users')->nullOnDelete();
            }
            if (! Schema::hasColumn('moderator_mkt_calendar_events', 'reminder_sent_at')) {
                $table->timestamp('reminder_sent_at')->nullable()->after('execution_confirmed_by');
            }
            if (! Schema::hasColumn('moderator_mkt_calendar_events', 'execution_penalty_deduction_id')) {
                $table->unsignedBigInteger('execution_penalty_deduction_id')->nullable()->after('reminder_sent_at');
                $table->index('execution_penalty_deduction_id', 'idx_mkt_ce_penalty');
            }
        });

        if (Schema::hasTable('employee_tasks') && ! Schema::hasColumn('employee_tasks', 'marketing_event_id')) {
            Schema::table('employee_tasks', function (Blueprint $table) {
                $table->unsignedBigInteger('marketing_event_id')->nullable()->after('design_cycle_id');
                $table->index('marketing_event_id', 'idx_emp_task_mkt_evt');
            });
        }

        if (Schema::hasTable('employee_tasks') && Schema::hasTable('moderator_mkt_calendar_events')
            && Schema::hasColumn('employee_tasks', 'marketing_event_id')) {
            Schema::table('employee_tasks', function (Blueprint $table) {
                if (! $this->foreignKeyExists('employee_tasks', 'fk_emp_task_mkt_event')) {
                    $table->foreign('marketing_event_id', 'fk_emp_task_mkt_event')
                        ->references('id')
                        ->on('moderator_mkt_calendar_events')
                        ->nullOnDelete();
                }
            });
        }

        if (Schema::hasTable('employee_tasks') && Schema::hasTable('moderator_mkt_calendar_events')
            && Schema::hasColumn('moderator_mkt_calendar_events', 'employee_task_id')) {
            Schema::table('moderator_mkt_calendar_events', function (Blueprint $table) {
                if (! $this->foreignKeyExists('moderator_mkt_calendar_events', 'fk_mkt_ce_emp_task')) {
                    $table->foreign('employee_task_id', 'fk_mkt_ce_emp_task')
                        ->references('id')
                        ->on('employee_tasks')
                        ->nullOnDelete();
                }
            });
        }

        if (Schema::hasTable('employee_salary_deductions') && Schema::hasColumn('moderator_mkt_calendar_events', 'execution_penalty_deduction_id')) {
            Schema::table('moderator_mkt_calendar_events', function (Blueprint $table) {
                if (! $this->foreignKeyExists('moderator_mkt_calendar_events', 'fk_mkt_ce_penalty')) {
                    $table->foreign('execution_penalty_deduction_id', 'fk_mkt_ce_penalty')
                        ->references('id')
                        ->on('employee_salary_deductions')
                        ->nullOnDelete();
                }
            });
        }

        if (Schema::hasTable('moderator_mkt_calendar_events') && Schema::hasColumn('moderator_mkt_calendar_events', 'assigned_employee_id')) {
            Schema::table('moderator_mkt_calendar_events', function (Blueprint $table) {
                if (! $this->foreignKeyExists('moderator_mkt_calendar_events', 'moderator_mkt_calendar_events_assigned_employee_id_foreign')) {
                    try {
                        $table->foreign('assigned_employee_id', 'fk_mkt_ce_assignee')
                            ->references('id')->on('users')->nullOnDelete();
                    } catch (\Throwable $e) {
                    }
                }
            });
        }
    }

    private function foreignKeyExists(string $table, string $name): bool
    {
        $conn = Schema::getConnection();
        $db = $conn->getDatabaseName();
        $result = $conn->select(
            'SELECT CONSTRAINT_NAME FROM information_schema.TABLE_CONSTRAINTS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND CONSTRAINT_NAME = ?',
            [$db, $table, $name]
        );

        return count($result) > 0;
    }

    public function down(): void
    {
        Schema::table('moderator_mkt_calendar_events', function (Blueprint $table) {
            foreach (['execution_penalty_deduction_id', 'employee_task_id'] as $fk) {
                try {
                    $table->dropForeign([$fk === 'execution_penalty_deduction_id' ? 'execution_penalty_deduction_id' : 'employee_task_id']);
                } catch (\Throwable $e) {
                }
            }
            foreach (['content_type', 'assigned_employee_id', 'employee_task_id', 'requires_confirmation', 'execution_confirmed_at', 'execution_confirmed_by', 'reminder_sent_at', 'execution_penalty_deduction_id'] as $col) {
                if (Schema::hasColumn('moderator_mkt_calendar_events', $col)) {
                    $table->dropColumn($col);
                }
            }
        });

        if (Schema::hasColumn('employee_tasks', 'marketing_event_id')) {
            Schema::table('employee_tasks', function (Blueprint $table) {
                $table->dropForeign(['marketing_event_id']);
                $table->dropColumn('marketing_event_id');
            });
        }
    }
};
