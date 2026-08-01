<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('sales_leads')) {
            Schema::table('sales_leads', function (Blueprint $table) {
                if (! Schema::hasColumn('sales_leads', 'contact_attempts')) {
                    $table->unsignedTinyInteger('contact_attempts')->default(0)->after('last_contacted_at');
                }
                if (! Schema::hasColumn('sales_leads', 'last_attempt_at')) {
                    $table->timestamp('last_attempt_at')->nullable()->after('contact_attempts');
                }
                if (! Schema::hasColumn('sales_leads', 'next_attempt_due_at')) {
                    $table->timestamp('next_attempt_due_at')->nullable()->after('last_attempt_at');
                }
                if (! Schema::hasColumn('sales_leads', 'connected_disposition')) {
                    $table->string('connected_disposition', 32)->nullable()->after('next_attempt_due_at');
                }
                if (! Schema::hasColumn('sales_leads', 'profile_type')) {
                    $table->string('profile_type', 32)->nullable()->after('connected_disposition');
                }
                if (! Schema::hasColumn('sales_leads', 'age')) {
                    $table->unsignedSmallInteger('age')->nullable()->after('profile_type');
                }
                if (! Schema::hasColumn('sales_leads', 'field_domain')) {
                    $table->string('field_domain', 120)->nullable()->after('age');
                }
                if (! Schema::hasColumn('sales_leads', 'experience_level')) {
                    $table->string('experience_level', 80)->nullable()->after('field_domain');
                }
                if (! Schema::hasColumn('sales_leads', 'course_motivation')) {
                    $table->text('course_motivation')->nullable()->after('experience_level');
                }
                if (! Schema::hasColumn('sales_leads', 'start_preference')) {
                    $table->string('start_preference', 120)->nullable()->after('course_motivation');
                }
                if (! Schema::hasColumn('sales_leads', 'can_pay')) {
                    $table->boolean('can_pay')->nullable()->after('start_preference');
                }
                if (! Schema::hasColumn('sales_leads', 'interest_pct')) {
                    $table->unsignedTinyInteger('interest_pct')->nullable()->after('can_pay');
                }
                if (! Schema::hasColumn('sales_leads', 'objection_reason')) {
                    $table->string('objection_reason', 40)->nullable()->after('interest_pct');
                }
                if (! Schema::hasColumn('sales_leads', 'objection_notes')) {
                    $table->text('objection_notes')->nullable()->after('objection_reason');
                }
                if (! Schema::hasColumn('sales_leads', 'follow_up_channel')) {
                    $table->string('follow_up_channel', 20)->nullable()->after('next_follow_up_at');
                }
                if (! Schema::hasColumn('sales_leads', 'offer_sent_at')) {
                    $table->timestamp('offer_sent_at')->nullable()->after('follow_up_channel');
                }
                if (! Schema::hasColumn('sales_leads', 'offer_price')) {
                    $table->decimal('offer_price', 12, 2)->nullable()->after('offer_sent_at');
                }
                if (! Schema::hasColumn('sales_leads', 'offer_discount')) {
                    $table->string('offer_discount', 80)->nullable()->after('offer_price');
                }
                if (! Schema::hasColumn('sales_leads', 'offer_installment_plan')) {
                    $table->string('offer_installment_plan', 160)->nullable()->after('offer_discount');
                }
                if (! Schema::hasColumn('sales_leads', 'offer_notes')) {
                    $table->text('offer_notes')->nullable()->after('offer_installment_plan');
                }
                if (! Schema::hasColumn('sales_leads', 'payment_method')) {
                    $table->string('payment_method', 60)->nullable()->after('offer_notes');
                }
                if (! Schema::hasColumn('sales_leads', 'payment_amount')) {
                    $table->decimal('payment_amount', 12, 2)->nullable()->after('payment_method');
                }
                if (! Schema::hasColumn('sales_leads', 'payment_due_at')) {
                    $table->timestamp('payment_due_at')->nullable()->after('payment_amount');
                }
                if (! Schema::hasColumn('sales_leads', 'payment_txn_ref')) {
                    $table->string('payment_txn_ref', 120)->nullable()->after('payment_due_at');
                }
                if (! Schema::hasColumn('sales_leads', 'paid_at')) {
                    $table->timestamp('paid_at')->nullable()->after('payment_txn_ref');
                }
                if (! Schema::hasColumn('sales_leads', 'stage_entered_at')) {
                    $table->timestamp('stage_entered_at')->nullable()->after('stage');
                }
            });

            // Remap legacy stages → academy pipeline
            $map = [
                'new' => 'new_lead',
                'contacted' => 'connected',
                'qualified' => 'qualification',
                'proposal' => 'offer_sent',
                'won' => 'enrollment_completed',
            ];
            foreach ($map as $from => $to) {
                DB::table('sales_leads')->where('stage', $from)->update(['stage' => $to]);
            }

            DB::table('sales_leads')
                ->whereNull('stage_entered_at')
                ->update(['stage_entered_at' => DB::raw('COALESCE(updated_at, created_at)')]);
        }

        if (Schema::hasTable('sales_activities')) {
            Schema::table('sales_activities', function (Blueprint $table) {
                if (! Schema::hasColumn('sales_activities', 'duration_seconds')) {
                    $table->unsignedInteger('duration_seconds')->nullable()->after('outcome');
                }
                if (! Schema::hasColumn('sales_activities', 'recording_url')) {
                    $table->string('recording_url', 500)->nullable()->after('duration_seconds');
                }
            });

            // Remap stage_change meta JSON keys when possible (MySQL JSON)
            foreach ([
                'new' => 'new_lead',
                'contacted' => 'connected',
                'qualified' => 'qualification',
                'proposal' => 'offer_sent',
                'won' => 'enrollment_completed',
            ] as $from => $to) {
                try {
                    DB::statement(
                        "UPDATE sales_activities SET meta = JSON_SET(meta, '$.to', ?) WHERE type = 'stage_change' AND JSON_UNQUOTE(JSON_EXTRACT(meta, '$.to')) = ?",
                        [$to, $from]
                    );
                    DB::statement(
                        "UPDATE sales_activities SET meta = JSON_SET(meta, '$.from', ?) WHERE type = 'stage_change' AND JSON_UNQUOTE(JSON_EXTRACT(meta, '$.from')) = ?",
                        [$to, $from]
                    );
                } catch (\Throwable) {
                    // Ignore if JSON functions unavailable
                }
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('sales_leads')) {
            $map = [
                'new_lead' => 'new',
                'first_contact' => 'contacted',
                'no_answer' => 'contacted',
                'connected' => 'contacted',
                'qualification' => 'qualified',
                'interested' => 'qualified',
                'objection' => 'qualified',
                'follow_up_scheduled' => 'contacted',
                'offer_sent' => 'proposal',
                'payment_pending' => 'proposal',
                'payment_received' => 'won',
                'enrollment_completed' => 'won',
                'upsell' => 'won',
                'dormant' => 'lost',
            ];
            foreach ($map as $from => $to) {
                DB::table('sales_leads')->where('stage', $from)->update(['stage' => $to]);
            }

            Schema::table('sales_leads', function (Blueprint $table) {
                foreach ([
                    'contact_attempts', 'last_attempt_at', 'next_attempt_due_at', 'connected_disposition',
                    'profile_type', 'age', 'field_domain', 'experience_level', 'course_motivation',
                    'start_preference', 'can_pay', 'interest_pct', 'objection_reason', 'objection_notes',
                    'follow_up_channel', 'offer_sent_at', 'offer_price', 'offer_discount',
                    'offer_installment_plan', 'offer_notes', 'payment_method', 'payment_amount',
                    'payment_due_at', 'payment_txn_ref', 'paid_at', 'stage_entered_at',
                ] as $col) {
                    if (Schema::hasColumn('sales_leads', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }

        if (Schema::hasTable('sales_activities')) {
            Schema::table('sales_activities', function (Blueprint $table) {
                if (Schema::hasColumn('sales_activities', 'recording_url')) {
                    $table->dropColumn('recording_url');
                }
                if (Schema::hasColumn('sales_activities', 'duration_seconds')) {
                    $table->dropColumn('duration_seconds');
                }
            });
        }
    }
};
