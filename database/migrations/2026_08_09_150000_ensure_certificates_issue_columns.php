<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('certificates')) {
            return;
        }

        Schema::table('certificates', function (Blueprint $table) {
            if (! Schema::hasColumn('certificates', 'issued_at')) {
                $table->timestamp('issued_at')->nullable()->after('issue_date');
            }
            if (! Schema::hasColumn('certificates', 'certified_at')) {
                $table->timestamp('certified_at')->nullable();
            }
            if (! Schema::hasColumn('certificates', 'certificate_hash')) {
                $table->string('certificate_hash', 64)->nullable();
            }
            if (! Schema::hasColumn('certificates', 'verification_url')) {
                $table->string('verification_url', 500)->nullable();
            }
            if (! Schema::hasColumn('certificates', 'qr_code_path')) {
                $table->string('qr_code_path')->nullable();
            }
            if (! Schema::hasColumn('certificates', 'serial_number')) {
                $table->string('serial_number')->nullable()->unique();
            }
            if (! Schema::hasColumn('certificates', 'logo_path')) {
                $table->string('logo_path')->nullable();
            }
            if (! Schema::hasColumn('certificates', 'stamp_path')) {
                $table->string('stamp_path')->nullable();
            }
            if (! Schema::hasColumn('certificates', 'academy_signature')) {
                $table->string('academy_signature')->nullable();
            }
            if (! Schema::hasColumn('certificates', 'academy_signature_name')) {
                $table->string('academy_signature_name')->nullable();
            }
            if (! Schema::hasColumn('certificates', 'academy_signature_title')) {
                $table->string('academy_signature_title')->nullable();
            }
            if (! Schema::hasColumn('certificates', 'instructor_id')) {
                $table->foreignId('instructor_id')->nullable()->constrained('users')->nullOnDelete();
            }
            if (! Schema::hasColumn('certificates', 'instructor_signature')) {
                $table->string('instructor_signature')->nullable();
            }
            if (! Schema::hasColumn('certificates', 'instructor_signature_name')) {
                $table->string('instructor_signature_name')->nullable();
            }
            if (! Schema::hasColumn('certificates', 'instructor_signature_title')) {
                $table->string('instructor_signature_title')->nullable();
            }
            if (! Schema::hasColumn('certificates', 'title')) {
                $table->string('title')->nullable();
            }
            if (! Schema::hasColumn('certificates', 'description')) {
                $table->text('description')->nullable();
            }
            if (! Schema::hasColumn('certificates', 'status')) {
                $table->string('status', 20)->default('pending');
            }
        });
    }

    public function down(): void
    {
        // non-destructive
    }
};
