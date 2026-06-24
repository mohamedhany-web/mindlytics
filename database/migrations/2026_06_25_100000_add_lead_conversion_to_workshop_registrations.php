<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('workshop_registrations')) {
            return;
        }

        Schema::table('workshop_registrations', function (Blueprint $table) {
            if (! Schema::hasColumn('workshop_registrations', 'converted_to_lead_at')) {
                $table->timestamp('converted_to_lead_at')->nullable()->after('whatsapp_link_sent_at');
            }
            if (! Schema::hasColumn('workshop_registrations', 'sales_lead_id')) {
                $table->foreignId('sales_lead_id')
                    ->nullable()
                    ->after('converted_to_lead_at')
                    ->constrained('sales_leads')
                    ->nullOnDelete();
            }
        });

        if (! Schema::hasColumn('workshop_registrations', 'converted_to_lead_at')) {
            return;
        }

        $leads = DB::table('sales_leads')
            ->where('notes', 'like', '%[workshop_registration:%')
            ->get(['id', 'notes', 'assigned_to']);

        foreach ($leads as $lead) {
            if (! preg_match('/\[workshop_registration:(\d+)\]/', (string) $lead->notes, $matches)) {
                continue;
            }

            $registrationId = (int) $matches[1];

            DB::table('workshop_registrations')
                ->where('id', $registrationId)
                ->whereNull('converted_to_lead_at')
                ->update([
                    'converted_to_lead_at' => now(),
                    'sales_lead_id' => $lead->id,
                    'updated_at' => now(),
                ]);
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('workshop_registrations')) {
            return;
        }

        Schema::table('workshop_registrations', function (Blueprint $table) {
            if (Schema::hasColumn('workshop_registrations', 'sales_lead_id')) {
                $table->dropConstrainedForeignId('sales_lead_id');
            }
            if (Schema::hasColumn('workshop_registrations', 'converted_to_lead_at')) {
                $table->dropColumn('converted_to_lead_at');
            }
        });
    }
};
