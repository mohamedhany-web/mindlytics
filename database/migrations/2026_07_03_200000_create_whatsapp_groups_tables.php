<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('whatsapp_groups')) {
            Schema::create('whatsapp_groups', function (Blueprint $table) {
                $table->id();
                $table->foreignId('sales_lead_group_id')->nullable()->constrained('sales_lead_groups')->nullOnDelete();
                $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
                $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
                $table->string('subject');
                $table->text('description')->nullable();
                $table->string('wa_group_jid')->nullable()->unique();
                $table->string('invite_link')->nullable();
                $table->boolean('announce_only')->default(false);
                $table->boolean('restrict_info')->default(false);
                $table->string('status', 30)->default('draft');
                $table->text('bridge_error')->nullable();
                $table->json('settings')->nullable();
                $table->timestamp('last_synced_at')->nullable();
                $table->timestamps();

                $table->index(['assigned_to', 'status']);
                $table->index('created_by');
            });
        }

        if (! Schema::hasTable('whatsapp_group_participants')) {
            Schema::create('whatsapp_group_participants', function (Blueprint $table) {
                $table->id();
                $table->foreignId('whatsapp_group_id')->constrained('whatsapp_groups')->cascadeOnDelete();
                $table->foreignId('sales_lead_id')->nullable()->constrained('sales_leads')->nullOnDelete();
                $table->string('phone', 30);
                $table->string('display_name')->nullable();
                $table->string('wa_participant_jid')->nullable();
                $table->string('status', 30)->default('pending');
                $table->text('error_message')->nullable();
                $table->timestamps();

                $table->unique(['whatsapp_group_id', 'phone']);
                $table->index('status');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('whatsapp_group_participants');
        Schema::dropIfExists('whatsapp_groups');
    }
};
