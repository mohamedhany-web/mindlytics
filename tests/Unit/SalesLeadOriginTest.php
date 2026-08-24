<?php

namespace Tests\Unit;

use App\Models\SalesLead;
use Tests\TestCase;

class SalesLeadOriginTest extends TestCase
{
    public function test_workshop_origin_from_notes_and_batch(): void
    {
        $lead = new SalesLead([
            'source' => 'event',
            'import_batch' => 'WS-9-20260824120000',
            'notes' => "[workshop:9] [workshop_registration:3]\nاسم الورشة: ورشة الذكاء الاصطناعي",
        ]);

        $this->assertSame('workshop', $lead->originKind());
        $this->assertTrue($lead->isWorkshopImportBatch());
        $this->assertSame(9, $lead->workshopIdFromNotes());
        $this->assertSame('ورشة الذكاء الاصطناعي', $lead->workshopTitleFromNotes());

        $summary = $lead->originSummary();
        $this->assertSame('workshop', $summary['kind']);
        $this->assertSame('ورشة', $summary['label']);
        $this->assertSame('ورشة الذكاء الاصطناعي', $summary['detail']);
    }

    public function test_import_origin_from_batch(): void
    {
        $lead = new SalesLead([
            'source' => 'other',
            'import_batch' => 'IMP-2026-01',
        ]);

        $this->assertSame('import', $lead->originKind());
        $summary = $lead->originSummary();
        $this->assertSame('استيراد', $summary['label']);
        $this->assertSame('IMP-2026-01', $summary['detail']);
    }

    public function test_manual_origin_uses_source_label(): void
    {
        $lead = new SalesLead([
            'source' => 'whatsapp',
        ]);

        $this->assertSame('manual', $lead->originKind());
        $summary = $lead->originSummary();
        $this->assertSame('واتساب', $summary['label']);
    }
}
