<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('instructor_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('headline')->nullable()->comment('عنوان تعريفي');
            $table->text('bio')->nullable()->comment('نبذة');
            $table->string('photo_path')->nullable()->comment('صورة شخصية');
            $table->text('experience')->nullable()->comment('الخبرات في المجال');
            $table->text('skills')->nullable()->comment('المهارات');
            $table->json('social_links')->nullable()->comment('روابط السوشيال');
            $table->enum('status', ['draft', 'pending_review', 'approved', 'rejected'])->default('draft');
            $table->text('rejection_reason')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();
            $table->unique('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('instructor_profiles');
    }
};
