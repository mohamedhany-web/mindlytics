<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('place_usage_logs')) {
            Schema::table('place_usage_logs', function (Blueprint $table) {
                if (! Schema::hasColumn('place_usage_logs', 'usage_type')) {
                    $table->string('usage_type', 32)->default('course')->after('usage_date');
                }
                if (! Schema::hasColumn('place_usage_logs', 'offline_course_id')) {
                    $table->unsignedBigInteger('offline_course_id')->nullable()->after('usage_type');
                }
            });

            if (Schema::hasTable('offline_courses') && Schema::hasColumn('place_usage_logs', 'offline_course_id')) {
                Schema::table('place_usage_logs', function (Blueprint $table) {
                    $table->foreign('offline_course_id', 'fk_place_usage_offline_course')
                        ->references('id')->on('offline_courses')->nullOnDelete();
                });
            }
        }

        if (! Schema::hasTable('place_daily_expenses')) {
            Schema::create('place_daily_expenses', function (Blueprint $table) {
                $table->id();
                $table->foreignId('offline_location_id')->constrained('offline_locations')->cascadeOnDelete();
                $table->foreignId('logged_by')->constrained('users')->cascadeOnDelete();
                $table->date('expense_date');
                $table->string('title');
                $table->string('category', 32)->default('other');
                $table->decimal('amount', 12, 2);
                $table->unsignedSmallInteger('quantity')->default(1);
                $table->string('status', 32)->default('pending');
                $table->foreignId('place_monthly_settlement_id')->nullable();
                $table->foreignId('place_usage_log_id')->nullable();
                $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamp('reviewed_at')->nullable();
                $table->text('rejection_reason')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->index(['offline_location_id', 'expense_date'], 'idx_place_exp_loc_date');
                $table->index(['status', 'expense_date'], 'idx_place_exp_status');
            });
        }

        if (Schema::hasTable('place_daily_expenses') && Schema::hasTable('place_monthly_settlements')) {
            Schema::table('place_daily_expenses', function (Blueprint $table) {
                if (! $this->fkExists('place_daily_expenses', 'fk_place_exp_settlement')) {
                    $table->foreign('place_monthly_settlement_id', 'fk_place_exp_settlement')
                        ->references('id')->on('place_monthly_settlements')->nullOnDelete();
                }
            });
        }

        if (Schema::hasTable('place_daily_expenses') && Schema::hasTable('place_usage_logs')) {
            Schema::table('place_daily_expenses', function (Blueprint $table) {
                if (! $this->fkExists('place_daily_expenses', 'fk_place_exp_usage_log')) {
                    $table->foreign('place_usage_log_id', 'fk_place_exp_usage_log')
                        ->references('id')->on('place_usage_logs')->nullOnDelete();
                }
            });
        }

        if (Schema::hasTable('place_monthly_settlements') && ! Schema::hasColumn('place_monthly_settlements', 'total_expenses')) {
            Schema::table('place_monthly_settlements', function (Blueprint $table) {
                $table->decimal('total_expenses', 14, 2)->default(0)->after('total_amount');
            });
        }
    }

    private function fkExists(string $table, string $name): bool
    {
        $conn = Schema::getConnection();
        $db = $conn->getDatabaseName();
        $rows = $conn->select(
            'SELECT CONSTRAINT_NAME FROM information_schema.TABLE_CONSTRAINTS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND CONSTRAINT_NAME = ?',
            [$db, $table, $name]
        );

        return count($rows) > 0;
    }

    public function down(): void
    {
        Schema::dropIfExists('place_daily_expenses');

        if (Schema::hasColumn('place_monthly_settlements', 'total_expenses')) {
            Schema::table('place_monthly_settlements', function (Blueprint $table) {
                $table->dropColumn('total_expenses');
            });
        }

        if (Schema::hasTable('place_usage_logs')) {
            Schema::table('place_usage_logs', function (Blueprint $table) {
                foreach (['offline_course_id', 'usage_type'] as $col) {
                    if (Schema::hasColumn('place_usage_logs', $col)) {
                        if ($col === 'offline_course_id') {
                            try {
                                $table->dropForeign('fk_place_usage_offline_course');
                            } catch (\Throwable $e) {
                            }
                        }
                        $table->dropColumn($col);
                    }
                }
            });
        }
    }
};
