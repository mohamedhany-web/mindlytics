<?php

namespace Database\Seeders;

use App\Services\SalesShiftPlanImporter;
use Illuminate\Database\Seeder;

class SalesShiftPlanSeeder extends Seeder
{
    public function run(): void
    {
        app(SalesShiftPlanImporter::class)->importDefaultPlan(true);

        $this->command?->info('Sales shift plan seeded (شهد · حنين · مريم · إسراء).');
    }
}
