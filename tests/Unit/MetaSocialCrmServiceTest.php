<?php

namespace Tests\Unit;

use App\Models\MetaSocialConversation;
use App\Models\MetaSocialMessage;
use App\Models\MetaSocialPage;
use App\Models\SalesLead;
use App\Models\SalesLeadCategory;
use App\Models\User;
use App\Services\MetaSocial\MetaSocialCrmService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class MetaSocialCrmServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        foreach ([
            'meta_social_messages',
            'meta_social_conversations',
            'meta_social_pages',
            'sales_activities',
            'sales_leads',
            'sales_lead_categories',
            'employee_jobs',
            'users',
        ] as $table) {
            Schema::dropIfExists($table);
        }

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password')->nullable();
            $table->boolean('is_employee')->default(false);
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('employee_job_id')->nullable();
            $table->timestamps();
        });

        Schema::create('employee_jobs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('sales_lead_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->nullable();
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('sales_leads', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('assigned_to')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('category_id')->nullable();
            $table->string('name');
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('source')->nullable();
            $table->string('stage')->default('new_lead');
            $table->string('priority')->default('normal');
            $table->text('notes')->nullable();
            $table->timestamp('last_contacted_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('sales_activities', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sales_lead_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('type')->nullable();
            $table->string('title')->nullable();
            $table->text('body')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();
        });

        Schema::create('meta_social_pages', function (Blueprint $table) {
            $table->id();
            $table->string('page_id')->nullable();
            $table->string('page_name')->nullable();
            $table->text('page_access_token')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('meta_social_conversations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('meta_social_page_id');
            $table->string('platform')->default('messenger');
            $table->string('participant_id');
            $table->string('participant_name')->nullable();
            $table->string('participant_username')->nullable();
            $table->string('participant_profile_pic')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->text('notes')->nullable();
            $table->string('thread_id')->nullable();
            $table->timestamp('last_message_at')->nullable();
            $table->string('last_message_preview')->nullable();
            $table->unsignedInteger('unread_count')->default(0);
            $table->string('status')->default('open');
            $table->unsignedBigInteger('assigned_to')->nullable();
            $table->unsignedBigInteger('sales_lead_id')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();
        });

        Schema::create('meta_social_messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('meta_social_conversation_id');
            $table->string('meta_message_id')->nullable()->unique();
            $table->string('direction');
            $table->string('message_type')->default('text');
            $table->text('body')->nullable();
            $table->string('attachment_url')->nullable();
            $table->unsignedBigInteger('sent_by_user_id')->nullable();
            $table->json('meta')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();
        });
    }

    public function test_create_lead_from_conversation_links_crm(): void
    {
        $category = SalesLeadCategory::query()->create([
            'name' => 'عام',
            'slug' => 'general',
            'is_default' => true,
            'is_active' => true,
        ]);

        $agent = User::withoutEvents(fn () => User::query()->create([
            'name' => 'Sales Agent',
            'email' => 'agent@test.local',
            'password' => bcrypt('password'),
            'is_employee' => true,
            'is_active' => true,
        ]));

        $page = MetaSocialPage::query()->create([
            'page_id' => '123',
            'page_name' => 'Mindlytics',
            'is_active' => true,
        ]);

        $conversation = MetaSocialConversation::query()->create([
            'meta_social_page_id' => $page->id,
            'platform' => 'messenger',
            'participant_id' => 'psid-1',
            'participant_name' => 'Ahmed Ali',
            'phone' => '01012345678',
            'status' => 'open',
        ]);

        $this->actingAs($agent);

        $lead = app(MetaSocialCrmService::class)->createLeadFromConversation(
            $conversation,
            (int) $agent->id,
            (int) $agent->id,
            '01012345678',
            null,
            'Ahmed Ali',
        );

        $this->assertInstanceOf(SalesLead::class, $lead);
        $this->assertSame('social', $lead->source);
        $this->assertSame('new_lead', $lead->stage);
        $this->assertSame((int) $category->id, (int) $lead->category_id);

        $conversation->refresh();
        $this->assertSame((int) $lead->id, (int) $conversation->sales_lead_id);
        $this->assertSame((int) $agent->id, (int) $conversation->assigned_to);
    }

    public function test_messages_ordered_by_sent_at_then_id(): void
    {
        $page = MetaSocialPage::query()->create(['page_id' => '1', 'page_name' => 'P', 'is_active' => true]);
        $conversation = MetaSocialConversation::query()->create([
            'meta_social_page_id' => $page->id,
            'platform' => 'messenger',
            'participant_id' => 'p1',
            'status' => 'open',
        ]);

        MetaSocialMessage::query()->create([
            'meta_social_conversation_id' => $conversation->id,
            'meta_message_id' => 'm2',
            'direction' => 'inbound',
            'body' => 'second',
            'sent_at' => now()->addMinutes(2),
        ]);
        MetaSocialMessage::query()->create([
            'meta_social_conversation_id' => $conversation->id,
            'meta_message_id' => 'm1',
            'direction' => 'inbound',
            'body' => 'first',
            'sent_at' => now()->addMinute(),
        ]);

        $bodies = $conversation->messages()->pluck('body')->all();
        $this->assertSame(['first', 'second'], $bodies);
    }
}
