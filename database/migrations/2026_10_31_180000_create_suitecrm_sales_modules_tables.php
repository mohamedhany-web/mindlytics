<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crm_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone', 40)->nullable()->index();
            $table->string('email')->nullable()->index();
            $table->string('website')->nullable();
            $table->string('industry', 120)->nullable();
            $table->string('account_type', 60)->nullable(); // customer / partner / prospect
            $table->text('billing_address')->nullable();
            $table->text('description')->nullable();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index(['assigned_to', 'name']);
        });

        Schema::create('crm_contacts', function (Blueprint $table) {
            $table->id();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('name');
            $table->string('title', 120)->nullable();
            $table->string('phone', 40)->nullable()->index();
            $table->string('email')->nullable()->index();
            $table->foreignId('crm_account_id')->nullable()->constrained('crm_accounts')->nullOnDelete();
            $table->foreignId('student_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('sales_lead_id')->nullable()->constrained('sales_leads')->nullOnDelete();
            $table->text('description')->nullable();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index(['assigned_to', 'name']);
        });

        Schema::create('crm_campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('status', 40)->default('planning'); // planning, active, complete, cancelled
            $table->string('campaign_type', 60)->nullable(); // email, whatsapp, meta, sms, other
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->decimal('budget', 12, 2)->nullable();
            $table->unsignedInteger('leads_count')->default(0);
            $table->text('description')->nullable();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('crm_cases', function (Blueprint $table) {
            $table->id();
            $table->string('subject');
            $table->string('status', 40)->default('new'); // new, assigned, pending, closed
            $table->string('priority', 20)->default('medium'); // low, medium, high, urgent
            $table->string('type', 60)->nullable();
            $table->text('description')->nullable();
            $table->text('resolution')->nullable();
            $table->foreignId('crm_account_id')->nullable()->constrained('crm_accounts')->nullOnDelete();
            $table->foreignId('crm_contact_id')->nullable()->constrained('crm_contacts')->nullOnDelete();
            $table->foreignId('sales_lead_id')->nullable()->constrained('sales_leads')->nullOnDelete();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();
            $table->index(['status', 'assigned_to']);
        });

        Schema::create('crm_documents', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('category', 80)->nullable();
            $table->string('disk', 40)->default('public');
            $table->string('path');
            $table->string('original_name')->nullable();
            $table->string('mime', 120)->nullable();
            $table->unsignedBigInteger('size')->nullable();
            $table->nullableMorphs('parent');
            $table->text('description')->nullable();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        if (Schema::hasTable('sales_activities')) {
            Schema::table('sales_activities', function (Blueprint $table) {
                if (! Schema::hasColumn('sales_activities', 'status')) {
                    $table->string('status', 40)->nullable()->after('type');
                }
                if (! Schema::hasColumn('sales_activities', 'direction')) {
                    $table->string('direction', 20)->nullable()->after('status');
                }
                if (! Schema::hasColumn('sales_activities', 'scheduled_at')) {
                    $table->timestamp('scheduled_at')->nullable()->index()->after('direction');
                }
                if (! Schema::hasColumn('sales_activities', 'due_at')) {
                    $table->timestamp('due_at')->nullable()->index()->after('scheduled_at');
                }
                if (! Schema::hasColumn('sales_activities', 'completed_at')) {
                    $table->timestamp('completed_at')->nullable()->after('due_at');
                }
                if (! Schema::hasColumn('sales_activities', 'parent_type')) {
                    $table->nullableMorphs('parent');
                }
            });
        }

        if (Schema::hasTable('sales_leads') && ! Schema::hasColumn('sales_leads', 'crm_campaign_id')) {
            Schema::table('sales_leads', function (Blueprint $table) {
                $table->foreignId('crm_campaign_id')->nullable()->after('source')->constrained('crm_campaigns')->nullOnDelete();
                $table->foreignId('crm_account_id')->nullable()->after('crm_campaign_id')->constrained('crm_accounts')->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('sales_leads')) {
            Schema::table('sales_leads', function (Blueprint $table) {
                if (Schema::hasColumn('sales_leads', 'crm_campaign_id')) {
                    $table->dropConstrainedForeignId('crm_campaign_id');
                }
                if (Schema::hasColumn('sales_leads', 'crm_account_id')) {
                    $table->dropConstrainedForeignId('crm_account_id');
                }
            });
        }

        if (Schema::hasTable('sales_activities')) {
            Schema::table('sales_activities', function (Blueprint $table) {
                foreach (['status', 'direction', 'scheduled_at', 'due_at', 'completed_at'] as $col) {
                    if (Schema::hasColumn('sales_activities', $col)) {
                        $table->dropColumn($col);
                    }
                }
                if (Schema::hasColumn('sales_activities', 'parent_type')) {
                    $table->dropMorphs('parent');
                }
            });
        }

        Schema::dropIfExists('crm_documents');
        Schema::dropIfExists('crm_cases');
        Schema::dropIfExists('crm_campaigns');
        Schema::dropIfExists('crm_contacts');
        Schema::dropIfExists('crm_accounts');
    }
};
