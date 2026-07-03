<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('whatsapp_contacts', function (Blueprint $table) {
            $table->id();
            $table->string('phone_number')->unique();
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('company')->nullable();
            $table->string('country_code', 8)->nullable();
            $table->string('language', 16)->nullable();
            $table->string('source', 50)->nullable();
            $table->foreignId('sales_lead_id')->nullable()->constrained('sales_leads')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->decimal('lifetime_value', 12, 2)->nullable();
            $table->timestamp('last_contacted_at')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index('assigned_to');
            $table->index('sales_lead_id');
        });

        Schema::table('whatsapp_conversations', function (Blueprint $table) {
            $table->foreignId('contact_id')->nullable()->after('user_id')->constrained('whatsapp_contacts')->nullOnDelete();
            $table->foreignId('assigned_to')->nullable()->after('contact_id')->constrained('users')->nullOnDelete();
            $table->foreignId('sales_lead_id')->nullable()->after('assigned_to')->constrained('sales_leads')->nullOnDelete();
            $table->string('status', 30)->default('open')->after('sales_lead_id');
            $table->string('department', 30)->nullable()->after('status');
            $table->string('priority', 20)->default('normal')->after('department');
            $table->timestamp('closed_at')->nullable()->after('priority');

            $table->index('assigned_to');
            $table->index('status');
            $table->index('department');
        });

        Schema::create('whatsapp_tags', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('color', 20)->default('slate');
            $table->timestamps();
        });

        Schema::create('whatsapp_conversation_tag', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained('whatsapp_conversations')->cascadeOnDelete();
            $table->foreignId('tag_id')->constrained('whatsapp_tags')->cascadeOnDelete();
            $table->foreignId('tagged_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('created_at')->useCurrent();

            $table->unique(['conversation_id', 'tag_id']);
        });

        Schema::create('whatsapp_conversation_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained('whatsapp_conversations')->cascadeOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('body');
            $table->timestamps();

            $table->index(['conversation_id', 'created_at']);
        });

        Schema::create('whatsapp_conversation_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained('whatsapp_conversations')->cascadeOnDelete();
            $table->foreignId('contact_id')->nullable()->constrained('whatsapp_contacts')->nullOnDelete();
            $table->string('type', 50);
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->json('meta')->nullable();
            $table->foreignId('performed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['conversation_id', 'created_at']);
            $table->index('type');
        });

        $now = now();
        $tags = [
            ['name' => 'VIP', 'slug' => 'vip', 'color' => 'amber'],
            ['name' => 'Lead', 'slug' => 'lead', 'color' => 'sky'],
            ['name' => 'مهتم', 'slug' => 'interested', 'color' => 'emerald'],
            ['name' => 'بارد', 'slug' => 'cold', 'color' => 'slate'],
            ['name' => 'مدفوع', 'slug' => 'paid', 'color' => 'violet'],
            ['name' => 'شكوى', 'slug' => 'complaint', 'color' => 'rose'],
        ];

        foreach ($tags as $tag) {
            DB::table('whatsapp_tags')->insert(array_merge($tag, [
                'created_at' => $now,
                'updated_at' => $now,
            ]));
        }

        if (Schema::hasTable('whatsapp_conversations')) {
            $conversations = DB::table('whatsapp_conversations')->get();
            foreach ($conversations as $conv) {
                $contactId = DB::table('whatsapp_contacts')->where('phone_number', $conv->phone_number)->value('id');
                if (! $contactId) {
                    $contactId = DB::table('whatsapp_contacts')->insertGetId([
                        'phone_number' => $conv->phone_number,
                        'name' => $conv->contact_name,
                        'user_id' => $conv->user_id,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                }

                DB::table('whatsapp_conversations')->where('id', $conv->id)->update([
                    'contact_id' => $contactId,
                    'status' => $conv->status ?? 'open',
                    'department' => $conv->department ?? 'sales',
                    'priority' => $conv->priority ?? 'normal',
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('whatsapp_conversation_events');
        Schema::dropIfExists('whatsapp_conversation_notes');
        Schema::dropIfExists('whatsapp_conversation_tag');
        Schema::dropIfExists('whatsapp_tags');

        Schema::table('whatsapp_conversations', function (Blueprint $table) {
            $table->dropConstrainedForeignId('contact_id');
            $table->dropConstrainedForeignId('assigned_to');
            $table->dropConstrainedForeignId('sales_lead_id');
            $table->dropColumn(['status', 'department', 'priority', 'closed_at']);
        });

        Schema::dropIfExists('whatsapp_contacts');
    }
};
