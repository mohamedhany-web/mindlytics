<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('journey_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('slug', 120)->unique();
            $table->string('display_name')->nullable();
            $table->string('headline')->nullable();
            $table->text('bio')->nullable();
            $table->string('career_goal')->nullable();
            $table->string('github_url', 500)->nullable();
            $table->string('linkedin_url', 500)->nullable();
            $table->string('website_url', 500)->nullable();
            $table->string('visibility', 20)->default('private'); // private | unlisted | public
            $table->unsignedTinyInteger('profile_completion')->default(0);
            $table->boolean('is_open_to_work')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->index(['visibility', 'is_active', 'published_at']);
            $table->index('is_open_to_work');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('journey_profiles');
    }
};
