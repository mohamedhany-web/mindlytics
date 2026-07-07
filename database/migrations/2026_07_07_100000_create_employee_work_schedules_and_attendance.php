<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('work_schedules')) {
            Schema::create('work_schedules', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->time('start_time');
                $table->time('end_time');
                $table->decimal('required_hours', 4, 2)->default(8);
                $table->json('work_days')->nullable();
                $table->unsignedSmallInteger('grace_minutes')->default(15);
                $table->unsignedSmallInteger('early_access_minutes')->default(10);
                $table->boolean('is_active')->default(true);
                $table->text('description')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasColumn('users', 'work_schedule_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->foreignId('work_schedule_id')->nullable()->after('weekly_off_day')
                    ->constrained('work_schedules')->nullOnDelete();
            });
        }

        if (! Schema::hasTable('employee_attendance_records')) {
            Schema::create('employee_attendance_records', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('work_schedule_id')->nullable()->constrained('work_schedules')->nullOnDelete();
                $table->date('work_date');
                $table->dateTime('scheduled_start');
                $table->dateTime('scheduled_end');
                $table->unsignedSmallInteger('required_minutes')->default(480);
                $table->dateTime('clock_in_at')->nullable();
                $table->dateTime('clock_out_at')->nullable();
                $table->unsignedInteger('worked_minutes')->nullable();
                $table->enum('status', [
                    'pending',
                    'active',
                    'completed',
                    'absent',
                    'late',
                    'incomplete',
                    'on_leave',
                    'off_day',
                ])->default('pending');
                $table->boolean('is_late')->default(false);
                $table->string('clock_in_ip', 45)->nullable();
                $table->string('clock_out_ip', 45)->nullable();
                $table->json('metadata')->nullable();
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();

                $table->unique(['user_id', 'work_date']);
                $table->index(['work_date', 'status']);
            });
        }

        if (Schema::hasTable('work_schedules') && DB::table('work_schedules')->count() === 0) {
            DB::table('work_schedules')->insert([
                'name' => 'دوام افتراضي',
                'start_time' => '09:00:00',
                'end_time' => '17:00:00',
                'required_hours' => 8,
                'work_days' => json_encode([0, 1, 2, 3, 4]),
                'grace_minutes' => 15,
                'early_access_minutes' => 10,
                'is_active' => true,
                'description' => 'موعد عمل افتراضي — الأحد إلى الخميس',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $defaultId = DB::table('work_schedules')->value('id');
            if ($defaultId && Schema::hasColumn('users', 'work_schedule_id')) {
                DB::table('users')
                    ->where('is_employee', true)
                    ->whereNull('work_schedule_id')
                    ->update(['work_schedule_id' => $defaultId]);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_attendance_records');

        if (Schema::hasColumn('users', 'work_schedule_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropConstrainedForeignId('work_schedule_id');
            });
        }

        Schema::dropIfExists('work_schedules');
    }
};
