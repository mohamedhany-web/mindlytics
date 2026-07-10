<?php

namespace Tests\Unit;

use App\Models\SalesTeam;
use App\Models\SalesTeamConversation;
use App\Models\SalesTeamMember;
use App\Models\SalesTeamMessage;
use App\Models\User;
use App\Services\SalesTeamChatService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class SalesTeamChatServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        foreach ([
            'sales_team_message_reactions',
            'sales_team_messages',
            'sales_team_conversation_participants',
            'sales_team_conversations',
            'sales_team_members',
            'sales_teams',
            'employee_jobs',
            'users',
        ] as $table) {
            Schema::dropIfExists($table);
        }

        Schema::create('employee_jobs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->string('password');
            $table->string('role')->default('student');
            $table->boolean('is_employee')->default(true);
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('employee_job_id')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('sales_teams', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->unsignedBigInteger('manager_id');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('sales_team_members', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sales_team_id');
            $table->unsignedBigInteger('user_id');
            $table->string('role')->default('member');
            $table->timestamps();
        });

        Schema::create('sales_team_conversations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sales_team_id');
            $table->string('type', 20);
            $table->string('title')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamp('last_message_at')->nullable();
            $table->timestamps();
        });

        Schema::create('sales_team_conversation_participants', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('conversation_id');
            $table->unsignedBigInteger('user_id');
            $table->timestamp('last_read_at')->nullable();
            $table->timestamps();
            $table->unique(['conversation_id', 'user_id']);
        });

        Schema::create('sales_team_messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('conversation_id');
            $table->unsignedBigInteger('user_id');
            $table->text('body');
            $table->unsignedBigInteger('reply_to_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('sales_team_message_reactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('message_id');
            $table->unsignedBigInteger('user_id');
            $table->string('emoji', 16);
            $table->timestamps();
            $table->unique(['message_id', 'user_id', 'emoji']);
        });
    }

    private function seedTeam(): array
    {
        $mgrJob = \DB::table('employee_jobs')->insertGetId([
            'name' => 'Sales Manager', 'code' => 'sales_manager', 'is_active' => 1,
            'created_at' => now(), 'updated_at' => now(),
        ]);
        $salesJob = \DB::table('employee_jobs')->insertGetId([
            'name' => 'Sales', 'code' => 'sales', 'is_active' => 1,
            'created_at' => now(), 'updated_at' => now(),
        ]);

        $manager = User::withoutEvents(fn () => User::query()->create([
            'name' => 'Manager', 'email' => 'mgr'.uniqid().'@t.test', 'phone' => '011'.random_int(10000000, 99999999),
            'password' => bcrypt('x'), 'role' => 'student', 'is_employee' => true, 'employee_job_id' => $mgrJob,
        ]));
        $rep = User::withoutEvents(fn () => User::query()->create([
            'name' => 'Rep', 'email' => 'rep'.uniqid().'@t.test', 'phone' => '012'.random_int(10000000, 99999999),
            'password' => bcrypt('x'), 'role' => 'student', 'is_employee' => true, 'employee_job_id' => $salesJob,
        ]));
        $rep2 = User::withoutEvents(fn () => User::query()->create([
            'name' => 'Rep2', 'email' => 'rep2'.uniqid().'@t.test', 'phone' => '013'.random_int(10000000, 99999999),
            'password' => bcrypt('x'), 'role' => 'student', 'is_employee' => true, 'employee_job_id' => $salesJob,
        ]));

        $team = SalesTeam::query()->create([
            'name' => 'Team A',
            'manager_id' => $manager->id,
            'created_by' => $manager->id,
            'is_active' => true,
        ]);

        SalesTeamMember::query()->create([
            'sales_team_id' => $team->id,
            'user_id' => $rep->id,
            'role' => 'member',
        ]);
        SalesTeamMember::query()->create([
            'sales_team_id' => $team->id,
            'user_id' => $rep2->id,
            'role' => 'member',
        ]);

        return [$manager->fresh(), $rep->fresh(), $rep2->fresh(), $team->fresh()];
    }

    public function test_team_channel_and_direct_message_flow(): void
    {
        [$manager, $rep, $rep2, $team] = $this->seedTeam();
        $chat = app(SalesTeamChatService::class);

        $channel = $chat->ensureTeamChannel($team, $manager);
        $this->assertSame(SalesTeamConversation::TYPE_TEAM, $channel->type);

        $msg = $chat->sendMessage($manager, $channel, 'مرحبا بالفريق');
        $this->assertSame('مرحبا بالفريق', $msg->body);

        $forRep = $chat->messages($rep, $channel);
        $this->assertCount(1, $forRep);
        $this->assertSame('مرحبا بالفريق', $forRep[0]['body']);

        $dm = $chat->findOrCreateDirect($team, $rep, (int) $manager->id);
        $this->assertTrue($dm->isDirect());
        $reply = $chat->sendMessage($rep, $dm, 'سؤال خاص', null);
        $react = $chat->toggleReaction($manager, $reply, '👍');
        $this->assertTrue($react['added']);

        $unread = $chat->unreadCount($manager, $team);
        $this->assertGreaterThan(0, $unread);

        $chat->markRead($manager, $dm);
        $this->assertSame(0, $chat->unreadCount($manager, $team));
    }

    public function test_cannot_dm_outside_team(): void
    {
        [$manager, $rep, $rep2, $team] = $this->seedTeam();
        $outsider = User::withoutEvents(fn () => User::query()->create([
            'name' => 'Out', 'email' => 'out'.uniqid().'@t.test', 'phone' => '015'.random_int(10000000, 99999999),
            'password' => bcrypt('x'), 'role' => 'student', 'is_employee' => true,
        ]));

        $this->expectException(\Illuminate\Validation\ValidationException::class);
        app(SalesTeamChatService::class)->findOrCreateDirect($team, $manager, (int) $outsider->id);
    }

    public function test_peer_dm_reply_poll_and_manager_delete(): void
    {
        [$manager, $rep, $rep2, $team] = $this->seedTeam();
        $chat = app(SalesTeamChatService::class);

        $channel = $chat->ensureTeamChannel($team, $manager);
        $m1 = $chat->sendMessage($manager, $channel, 'أول');
        $m2 = $chat->sendMessage($rep, $channel, 'ثاني مع رد', $m1->id);
        $this->assertSame($m1->id, $m2->reply_to_id);

        $polled = $chat->messages($rep2, $channel, $m1->id);
        $this->assertCount(1, $polled);
        $this->assertSame($m2->id, $polled[0]['id']);
        $this->assertNotNull($polled[0]['reply_to']);

        $dm = $chat->findOrCreateDirect($team, $rep, (int) $rep2->id);
        $peerMsg = $chat->sendMessage($rep, $dm, 'بين الزملاء');
        $again = $chat->findOrCreateDirect($team, $rep2, (int) $rep->id);
        $this->assertSame($dm->id, $again->id);

        $members = $chat->listMembers($manager, $team);
        $this->assertCount(3, $members);

        $list = $chat->listConversations($rep, $team);
        $this->assertTrue($list->contains(fn ($c) => $c['id'] === $channel->id));
        $this->assertTrue($list->contains(fn ($c) => $c['id'] === $dm->id));

        $chat->deleteMessage($manager, $peerMsg, $team);
        $this->assertSoftDeleted('sales_team_messages', ['id' => $peerMsg->id]);

        $toggleOff = $chat->toggleReaction($manager, $m2, '👍');
        $this->assertTrue($toggleOff['added']);
        $toggleOff2 = $chat->toggleReaction($manager, $m2, '👍');
        $this->assertFalse($toggleOff2['added']);
    }

    public function test_empty_message_rejected(): void
    {
        [$manager, $rep, $rep2, $team] = $this->seedTeam();
        $chat = app(SalesTeamChatService::class);
        $channel = $chat->ensureTeamChannel($team, $manager);

        $this->expectException(\Illuminate\Validation\ValidationException::class);
        $chat->sendMessage($manager, $channel, '   ');
    }
}
