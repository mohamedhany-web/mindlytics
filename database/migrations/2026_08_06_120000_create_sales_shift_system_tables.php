<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('sales_shift_plans')) {
            Schema::create('sales_shift_plans', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->text('description')->nullable();
                $table->unsignedTinyInteger('work_start_hour')->default(10);
                $table->unsignedTinyInteger('work_end_hour')->default(26);
                $table->unsignedSmallInteger('takeover_grace_minutes')->default(10);
                $table->boolean('is_active')->default(false);
                $table->date('effective_from')->nullable();
                $table->json('rules')->nullable();
                $table->timestamps();

                $table->index(['is_active', 'effective_from']);
            });
        }

        if (! Schema::hasTable('sales_shift_segments')) {
            Schema::create('sales_shift_segments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('sales_shift_plan_id')->constrained('sales_shift_plans')->cascadeOnDelete();
                $table->unsignedTinyInteger('day_of_week'); // 0=Sat … 6=Fri
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->unsignedTinyInteger('start_hour'); // 10–26
                $table->unsignedTinyInteger('end_hour');
                $table->string('mode', 20)->default('normal'); // normal|home
                $table->json('channels'); // ['whatsapp','calls'] or ['all']
                $table->string('location_badge', 40)->nullable(); // from_office
                $table->unsignedSmallInteger('sort_order')->default(0);
                $table->timestamps();

                $table->index(['sales_shift_plan_id', 'day_of_week', 'user_id'], 'shift_seg_plan_day_user');
                $table->index(['day_of_week', 'start_hour', 'end_hour']);
            });
        }

        if (! Schema::hasTable('sales_shift_employee_profiles')) {
            Schema::create('sales_shift_employee_profiles', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->unique()->constrained('users')->cascadeOnDelete();
                $table->string('color_hex', 7)->default('#0EA5E9');
                $table->string('base_channels_label')->nullable();
                $table->unsignedTinyInteger('display_order')->default(0);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('sales_shift_swap_requests')) {
            Schema::create('sales_shift_swap_requests', function (Blueprint $table) {
                $table->id();
                $table->foreignId('requester_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('partner_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('segment_id')->nullable()->constrained('sales_shift_segments')->nullOnDelete();
                $table->date('work_date');
                $table->unsignedTinyInteger('day_of_week')->nullable();
                $table->text('reason')->nullable();
                $table->string('status', 20)->default('pending');
                $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamp('reviewed_at')->nullable();
                $table->text('manager_notes')->nullable();
                $table->timestamps();

                $table->index(['status', 'work_date']);
            });
        }

        if (! Schema::hasTable('sales_shift_channel_events')) {
            Schema::create('sales_shift_channel_events', function (Blueprint $table) {
                $table->id();
                $table->string('channel_code', 30);
                $table->foreignId('owner_user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('actor_user_id')->constrained('users')->cascadeOnDelete();
                $table->string('event_type', 30); // outbound_reply|takeover
                $table->string('source_type', 40)->nullable(); // whatsapp|meta_social|crm
                $table->unsignedBigInteger('source_id')->nullable();
                $table->timestamp('occurred_at');
                $table->timestamps();

                $table->index(['channel_code', 'occurred_at']);
                $table->index(['owner_user_id', 'channel_code', 'occurred_at'], 'shift_chan_owner_at');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('sales_shift_channel_events');
        Schema::dropIfExists('sales_shift_swap_requests');
        Schema::dropIfExists('sales_shift_employee_profiles');
        Schema::dropIfExists('sales_shift_segments');
        Schema::dropIfExists('sales_shift_plans');
    }
};
