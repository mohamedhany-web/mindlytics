<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('offline_locations')) {
            Schema::table('offline_locations', function (Blueprint $table) {
                if (! Schema::hasColumn('offline_locations', 'hourly_rate')) {
                    $table->decimal('hourly_rate', 12, 2)->nullable()->after('is_active');
                }
                if (! Schema::hasColumn('offline_locations', 'default_wallet_id')) {
                    $table->foreignId('default_wallet_id')->nullable()->after('hourly_rate')
                        ->constrained('wallets')->nullOnDelete();
                }
                if (! Schema::hasColumn('offline_locations', 'manager_user_id')) {
                    $table->foreignId('manager_user_id')->nullable()->after('default_wallet_id')
                        ->constrained('users')->nullOnDelete();
                }
                if (! Schema::hasColumn('offline_locations', 'vendor_contact_name')) {
                    $table->string('vendor_contact_name')->nullable()->after('manager_user_id');
                }
                if (! Schema::hasColumn('offline_locations', 'vendor_tax_id')) {
                    $table->string('vendor_tax_id')->nullable()->after('vendor_contact_name');
                }
                if (! Schema::hasColumn('offline_locations', 'vendor_bank_details')) {
                    $table->text('vendor_bank_details')->nullable()->after('vendor_tax_id');
                }
            });
        }

        if (Schema::hasTable('users') && ! Schema::hasColumn('users', 'offline_location_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->foreignId('offline_location_id')->nullable()->after('branch_id')
                    ->constrained('offline_locations')->nullOnDelete();
            });
        }

        if (! Schema::hasTable('place_usage_logs')) {
            Schema::create('place_usage_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('offline_location_id')->constrained('offline_locations')->cascadeOnDelete();
                $table->foreignId('logged_by')->constrained('users')->cascadeOnDelete();
                $table->date('usage_date');
                $table->decimal('hours', 8, 2);
                $table->string('description')->nullable();
                $table->string('status', 32)->default('pending'); // pending, approved, rejected
                $table->foreignId('place_monthly_settlement_id')->nullable();
                $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamp('reviewed_at')->nullable();
                $table->text('rejection_reason')->nullable();
                $table->timestamps();

                $table->index(['offline_location_id', 'usage_date']);
                $table->index(['status', 'usage_date']);
            });
        }

        if (! Schema::hasTable('place_monthly_settlements')) {
            Schema::create('place_monthly_settlements', function (Blueprint $table) {
                $table->id();
                $table->string('settlement_number', 32)->unique();
                $table->foreignId('offline_location_id')->constrained('offline_locations')->cascadeOnDelete();
                $table->char('period_month', 7); // YYYY-MM
                $table->decimal('total_hours', 10, 2)->default(0);
                $table->decimal('hourly_rate', 12, 2)->default(0);
                $table->decimal('total_amount', 14, 2)->default(0);
                $table->string('currency', 8)->default('EGP');
                $table->string('status', 32)->default('open');
                // open → submitted → approved → closed → paid
                $table->foreignId('submitted_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamp('submitted_at')->nullable();
                $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamp('approved_at')->nullable();
                $table->foreignId('closed_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamp('closed_at')->nullable();
                $table->foreignId('wallet_id')->nullable()->constrained('wallets')->nullOnDelete();
                $table->foreignId('expense_id')->nullable()->constrained('expenses')->nullOnDelete();
                $table->foreignId('place_invoice_id')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->unique(['offline_location_id', 'period_month'], 'pms_location_period_unique');
                $table->index(['status', 'period_month'], 'pms_status_period_idx');
            });
        }

        if (Schema::hasTable('place_usage_logs') && Schema::hasTable('place_monthly_settlements')) {
            Schema::table('place_usage_logs', function (Blueprint $table) {
                if (! Schema::hasColumn('place_usage_logs', 'place_monthly_settlement_id')) {
                    return;
                }
                $table->foreign('place_monthly_settlement_id')
                    ->references('id')->on('place_monthly_settlements')->nullOnDelete();
            });
        }

        if (! Schema::hasTable('place_invoices')) {
            Schema::create('place_invoices', function (Blueprint $table) {
                $table->id();
                $table->string('invoice_number', 32)->unique();
                $table->foreignId('offline_location_id')->constrained('offline_locations')->cascadeOnDelete();
                $table->foreignId('place_monthly_settlement_id')->constrained('place_monthly_settlements')->cascadeOnDelete();
                $table->decimal('amount', 14, 2);
                $table->string('currency', 8)->default('EGP');
                $table->char('period_month', 7);
                $table->string('status', 32)->default('issued'); // issued, paid, cancelled
                $table->timestamp('issued_at')->nullable();
                $table->timestamp('paid_at')->nullable();
                $table->json('line_items')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }

        if (Schema::hasTable('place_monthly_settlements') && Schema::hasTable('place_invoices')) {
            Schema::table('place_monthly_settlements', function (Blueprint $table) {
                $table->foreign('place_invoice_id')
                    ->references('id')->on('place_invoices')->nullOnDelete();
            });
        }

        if (Schema::hasTable('expenses')) {
            Schema::table('expenses', function (Blueprint $table) {
                if (! Schema::hasColumn('expenses', 'offline_location_id')) {
                    $table->foreignId('offline_location_id')->nullable()->after('wallet_id')
                        ->constrained('offline_locations')->nullOnDelete();
                }
                if (! Schema::hasColumn('expenses', 'place_monthly_settlement_id')) {
                    $table->foreignId('place_monthly_settlement_id')->nullable()->after('offline_location_id')
                        ->constrained('place_monthly_settlements')->nullOnDelete();
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('expenses')) {
            Schema::table('expenses', function (Blueprint $table) {
                if (Schema::hasColumn('expenses', 'place_monthly_settlement_id')) {
                    $table->dropConstrainedForeignId('place_monthly_settlement_id');
                }
                if (Schema::hasColumn('expenses', 'offline_location_id')) {
                    $table->dropConstrainedForeignId('offline_location_id');
                }
            });
        }

        Schema::dropIfExists('place_invoices');
        Schema::dropIfExists('place_usage_logs');
        Schema::dropIfExists('place_monthly_settlements');

        if (Schema::hasTable('users') && Schema::hasColumn('users', 'offline_location_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropConstrainedForeignId('offline_location_id');
            });
        }

        if (Schema::hasTable('offline_locations')) {
            Schema::table('offline_locations', function (Blueprint $table) {
                foreach (['vendor_bank_details', 'vendor_tax_id', 'vendor_contact_name', 'manager_user_id', 'default_wallet_id', 'hourly_rate'] as $col) {
                    if (Schema::hasColumn('offline_locations', $col)) {
                        if (in_array($col, ['manager_user_id', 'default_wallet_id'], true)) {
                            $table->dropConstrainedForeignId($col);
                        } else {
                            $table->dropColumn($col);
                        }
                    }
                }
            });
        }
    }
};
