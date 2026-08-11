<?php

namespace Database\Seeders;

use App\Services\JourneyAchievementService;
use Illuminate\Database\Seeder;

class JourneyAchievementsSeeder extends Seeder
{
    public function run(): void
    {
        app(JourneyAchievementService::class)->ensureDefinitions();
    }
}
