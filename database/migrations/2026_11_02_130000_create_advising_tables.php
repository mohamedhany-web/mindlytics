<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * الإرشاد المهني (docs/ADVISING.md): مواعيد التوافر الأسبوعية، والأيام المقفولة، وحجوزات جلسات الإرشاد.
 * جداول جديدة بس، مفيش أي تعديل على جداول قديمة.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('advising_availability')) {
            Schema::create('advising_availability', function (Blueprint $table) {
                $table->id();
                $table->unsignedTinyInteger('weekday'); // 0 = الأحد … 6 = السبت (Carbon dayOfWeek)
                $table->time('start_time');
                $table->time('end_time');
                $table->unsignedSmallInteger('slot_minutes')->default(30);
                $table->unsignedTinyInteger('capacity')->default(1); // كام حجز في نفس الميعاد
                $table->string('mode', 20)->default('online'); // online | campus | phone
                $table->foreignId('advisor_id')->nullable()->constrained('users')->nullOnDelete();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
                $table->index(['weekday', 'is_active']);
            });
        }

        if (! Schema::hasTable('advising_blocked_dates')) {
            Schema::create('advising_blocked_dates', function (Blueprint $table) {
                $table->id();
                $table->date('date')->unique();
                $table->string('reason')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('advising_bookings')) {
            Schema::create('advising_bookings', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('phone', 40);
                $table->string('email')->nullable();
                $table->string('goal', 30); // first_job | career_change | upskill | team | unsure
                $table->string('level', 30)->nullable(); // beginner | basics | working
                $table->string('interest')->nullable(); // المجال أو البرنامج اللي مهتم بيه
                $table->text('notes')->nullable(); // كلام الطالب عن وضعه
                $table->dateTime('starts_at');
                $table->unsignedSmallInteger('duration_minutes')->default(30);
                $table->string('mode', 20)->default('online');
                $table->foreignId('availability_id')->nullable()->constrained('advising_availability')->nullOnDelete();
                $table->foreignId('advisor_id')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete(); // لو كان مسجّل دخول
                $table->string('status', 20)->default('pending'); // pending | confirmed | done | no_show | cancelled
                $table->text('outcome')->nullable(); // ملخص الجلسة: احتياجاته وخطة دخوله لسوق العمل
                $table->string('recommended_program')->nullable();
                $table->string('source_url', 500)->nullable();
                $table->string('ip', 45)->nullable();
                $table->timestamps();
                $table->index(['starts_at', 'status']);
                $table->index('status');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('advising_bookings');
        Schema::dropIfExists('advising_blocked_dates');
        Schema::dropIfExists('advising_availability');
    }
};
