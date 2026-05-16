<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('group_messages', function (Blueprint $table) {
            if (! Schema::hasColumn('group_messages', 'reply_to_id')) {
                $table->foreignId('reply_to_id')->nullable()->after('body')->constrained('group_messages')->nullOnDelete();
            }
            if (! Schema::hasColumn('group_messages', 'kind')) {
                $table->string('kind', 32)->default('text')->after('reply_to_id');
            }
            if (! Schema::hasColumn('group_messages', 'meta')) {
                $table->json('meta')->nullable()->after('kind');
            }
        });
    }

    public function down(): void
    {
        Schema::table('group_messages', function (Blueprint $table) {
            if (Schema::hasColumn('group_messages', 'meta')) {
                $table->dropColumn('meta');
            }
            if (Schema::hasColumn('group_messages', 'kind')) {
                $table->dropColumn('kind');
            }
            if (Schema::hasColumn('group_messages', 'reply_to_id')) {
                $table->dropConstrainedForeignId('reply_to_id');
            }
        });
    }
};
