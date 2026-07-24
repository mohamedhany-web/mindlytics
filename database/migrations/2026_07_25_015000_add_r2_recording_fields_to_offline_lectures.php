<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('offline_lectures')) {
            return;
        }

        Schema::table('offline_lectures', function (Blueprint $table) {
            if (! Schema::hasColumn('offline_lectures', 'recording_path')) {
                $table->string('recording_path')->nullable()->after('recording_url');
            }
            if (! Schema::hasColumn('offline_lectures', 'recording_disk')) {
                $table->string('recording_disk', 32)->nullable()->after('recording_path');
            }
            if (! Schema::hasColumn('offline_lectures', 'recording_original_name')) {
                $table->string('recording_original_name')->nullable()->after('recording_disk');
            }
            if (! Schema::hasColumn('offline_lectures', 'recording_mime')) {
                $table->string('recording_mime', 100)->nullable()->after('recording_original_name');
            }
            if (! Schema::hasColumn('offline_lectures', 'recording_size')) {
                $table->unsignedBigInteger('recording_size')->nullable()->after('recording_mime');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('offline_lectures')) {
            return;
        }

        Schema::table('offline_lectures', function (Blueprint $table) {
            foreach (['recording_size', 'recording_mime', 'recording_original_name', 'recording_disk', 'recording_path'] as $col) {
                if (Schema::hasColumn('offline_lectures', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
