<?php

namespace Tests\Unit\Accounting;

use App\Domains\Accounting\Support\DocumentNumber;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

/**
 * ترقيم المستندات المالية (docs/ACCOUNTING_AUDIT.md → C2).
 * الجدول هنا مبسّط لأن بعض الـ migrations خاصة بـ MySQL.
 */
class DocumentNumberTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('doc_number_rows', function (Blueprint $table) {
            $table->id();
            $table->string('number')->unique();
        });
    }

    public function test_matches_previous_count_plus_one_when_nothing_was_deleted(): void
    {
        $this->insert('INV-00000001', 'INV-00000002');

        $this->assertSame('INV-00000003', $this->next('INV-', 8));
    }

    public function test_skips_numbers_still_in_use_after_a_deletion(): void
    {
        $this->insert('INV-00000001', 'INV-00000002', 'INV-00000003');
        DocNumberRow::where('number', 'INV-00000001')->delete();

        // count() + 1 = 3 وهو مستخدم؛ الرقم الصحيح التالي 4
        $this->assertSame('INV-00000004', $this->next('INV-', 8));
        DocNumberRow::create(['number' => $this->next('INV-', 8)]);
        $this->assertSame('INV-00000005', $this->next('INV-', 8));
    }

    public function test_keeps_shared_table_counter_behaviour_for_other_prefixes(): void
    {
        // transactions فيها TXN- و SALE-COM- معاً، والعدّاد القديم كان على كل الصفوف
        $this->insert('TXN-00000001', 'SALE-COM-000002', 'SALE-COM-000003', 'TXN-00000004');

        $this->assertSame('TXN-00000005', $this->next('TXN-', 8));
        $this->assertSame('SALE-COM-000005', $this->next('SALE-COM-', 6));
    }

    public function test_year_prefix_uses_the_same_rule(): void
    {
        $this->insert('WDR-2025-000001', 'WDR-2025-000002');
        DocNumberRow::where('number', 'WDR-2025-000001')->delete();

        $this->assertSame('WDR-2025-000003', $this->next('WDR-2025-', 6));
        $this->assertSame('WDR-2026-000002', $this->next('WDR-2026-', 6));
    }

    public function test_ignores_values_that_are_not_numeric_after_the_prefix(): void
    {
        $this->insert('INV-00000001', 'INV-MANUAL-ABC');

        $this->assertSame('INV-00000003', $this->next('INV-', 8));
    }

    public function test_works_inside_a_transaction_and_when_numbers_outgrow_the_padding(): void
    {
        $this->insert('INV-99999999');
        DocNumberRow::create(['number' => 'INV-100000000']);

        DB::transaction(function () {
            $this->assertSame('INV-100000001', $this->next('INV-', 8));
        });
    }

    public function test_empty_table_starts_at_one(): void
    {
        $this->assertSame('PAY-00000001', $this->next('PAY-', 8));
    }

    private function next(string $prefix, int $pad): string
    {
        return DocumentNumber::next(DocNumberRow::class, 'number', $prefix, $pad);
    }

    private function insert(string ...$numbers): void
    {
        foreach ($numbers as $number) {
            DocNumberRow::create(['number' => $number]);
        }
    }
}

class DocNumberRow extends Model
{
    protected $table = 'doc_number_rows';

    public $timestamps = false;

    protected $guarded = [];
}
