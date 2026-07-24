<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('certificate_brandings')) {
            Schema::create('certificate_brandings', function (Blueprint $table) {
                $table->id();
                $table->string('academy_name')->default('Mindlytics');
                $table->string('academy_tagline')->nullable()->default('أكاديمية البرمجة');
                $table->string('logo_path')->nullable();
                $table->string('signature_path')->nullable();
                $table->string('stamp_path')->nullable();
                $table->string('signature_name')->nullable()->default('المدير العام');
                $table->string('signature_title')->nullable()->default('Mindlytics Academy');
                $table->string('seal_label')->nullable()->default('CERTIFICATION');
                $table->string('seal_since')->nullable()->default('2020');
                $table->string('default_template')->default('achievement');
                $table->timestamps();
            });

            DB::table('certificate_brandings')->insert([
                'academy_name' => 'Mindlytics',
                'academy_tagline' => 'أكاديمية البرمجة',
                'signature_name' => 'المدير العام',
                'signature_title' => 'Mindlytics Academy',
                'seal_label' => 'CERTIFICATION',
                'seal_since' => '2020',
                'default_template' => 'achievement',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        if (! Schema::hasTable('certificates')) {
            return;
        }

        Schema::table('certificates', function (Blueprint $table) {
            if (! Schema::hasColumn('certificates', 'serial_number')) {
                $table->string('serial_number')->nullable()->after('certificate_number');
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
        });

        // Backfill missing serials with unique values (no Eloquent dependency)
        $rows = DB::table('certificates')
            ->where(function ($q) {
                $q->whereNull('serial_number')->orWhere('serial_number', '');
            })
            ->orderBy('id')
            ->get(['id']);

        foreach ($rows as $row) {
            $serial = null;
            for ($i = 0; $i < 50; $i++) {
                $candidate = 'MIND-' . date('Y') . '-' . strtoupper(substr(uniqid(), -8)) . '-' . str_pad((string) random_int(1, 9999), 4, '0', STR_PAD_LEFT);
                $exists = DB::table('certificates')->where('serial_number', $candidate)->exists();
                if (! $exists) {
                    $serial = $candidate;
                    break;
                }
            }
            $serial = $serial ?: ('MIND-' . date('Y') . '-' . $row->id . '-' . time());
            DB::table('certificates')->where('id', $row->id)->update(['serial_number' => $serial]);
        }

        $indexes = collect(DB::select('SHOW INDEX FROM certificates'));
        $hasUnique = $indexes->contains(fn ($idx) => ($idx->Key_name ?? '') === 'certificates_serial_number_unique');
        if (! $hasUnique) {
            Schema::table('certificates', function (Blueprint $table) {
                $table->unique('serial_number');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('certificates')) {
            Schema::table('certificates', function (Blueprint $table) {
                try {
                    $table->dropUnique('certificates_serial_number_unique');
                } catch (\Throwable $e) {
                    // ignore
                }
                foreach (['stamp_path', 'logo_path'] as $col) {
                    if (Schema::hasColumn('certificates', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });
        }

        Schema::dropIfExists('certificate_brandings');
    }
};
