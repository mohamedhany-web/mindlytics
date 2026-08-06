<?php

namespace App\Services;

use App\Models\SalesShiftEmployeeProfile;
use App\Models\SalesShiftPlan;
use App\Models\SalesShiftSegment;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * يستورد خطة الشيفتات الافتراضية من sales-shifts.html
 */
class SalesShiftPlanImporter
{
    private const PERSON_META = [
        'shahd' => ['match' => ['شهد', 'shahd'], 'color' => '#F5A623', 'base' => 'ماسنجر · انستا', 'weekly_off' => 4],
        'esraa' => ['match' => ['إسراء', 'اسراء', 'esraa'], 'color' => '#EC6A9C', 'base' => 'مكالمات', 'weekly_off' => 3],
        'mariam' => ['match' => ['مريم', 'mariam'], 'color' => '#4DD0E1', 'base' => 'واتساب · فولو أب', 'weekly_off' => 5],
        'haneen' => ['match' => ['حنين', 'haneen'], 'color' => '#8BD450', 'base' => 'كومنتات', 'weekly_off' => 5],
    ];

    private const OFF_CHANNELS = [
        'shahd' => ['messenger', 'instagram'],
        'mariam' => ['whatsapp', 'followup'],
        'haneen' => ['comments', 'calls'],
    ];

    public function importDefaultPlan(bool $activate = true): SalesShiftPlan
    {
        return DB::transaction(function () use ($activate) {
            if ($activate) {
                SalesShiftPlan::query()->update(['is_active' => false]);
            }

            $plan = SalesShiftPlan::create([
                'name' => 'جدول شيفتات فريق المبيعات',
                'description' => 'مستورد من sales-shifts.html — 10 ص إلى 2 ص، توزيع قنوات',
                'work_start_hour' => 10,
                'work_end_hour' => 26,
                'takeover_grace_minutes' => (int) config('sales_shifts.takeover_grace_minutes', 10),
                'is_active' => $activate,
                'effective_from' => today(),
                'rules' => config('sales_shifts.rules'),
            ]);

            $users = $this->resolvePeople();
            $this->syncProfiles($users);

            $sort = 0;
            foreach ($this->dayDefinitions($users) as $day) {
                foreach ($day['lanes'] as $lane) {
                    foreach ($lane['segments'] as $seg) {
                        SalesShiftSegment::create([
                            'sales_shift_plan_id' => $plan->id,
                            'day_of_week' => $day['day_of_week'],
                            'user_id' => $lane['user_id'],
                            'start_hour' => $seg[0],
                            'end_hour' => $seg[1],
                            'mode' => $seg[2] === 'home' ? SalesShiftSegment::MODE_HOME : SalesShiftSegment::MODE_NORMAL,
                            'channels' => $this->parseChannels($seg[3]),
                            'location_badge' => $day['location_badge'] ?? null,
                            'sort_order' => $sort++,
                        ]);
                    }
                }
            }

            return $plan->load('segments');
        });
    }

    /**
     * @return array<string, User>
     */
    private function resolvePeople(): array
    {
        $reps = User::salesEmployees()->where('is_active', true)->orderBy('name')->get();
        $map = [];
        $used = [];

        foreach (self::PERSON_META as $key => $meta) {
            $found = $reps->first(function (User $u) use ($meta, $used) {
                if (in_array($u->id, $used, true)) {
                    return false;
                }
                $name = mb_strtolower($u->name);
                foreach ($meta['match'] as $needle) {
                    if (str_contains($name, mb_strtolower($needle))) {
                        return true;
                    }
                }

                return false;
            });

            if (! $found) {
                $found = $reps->first(fn (User $u) => ! in_array($u->id, $used, true));
            }

            if ($found) {
                $used[] = $found->id;
                if ($meta['weekly_off'] !== null) {
                    $found->forceFill(['weekly_off_day' => $meta['weekly_off']])->save();
                }
                $map[$key] = $found;
            }
        }

        return $map;
    }

    /**
     * @param  array<string, User>  $users
     */
    private function syncProfiles(array $users): void
    {
        $order = 0;
        foreach (self::PERSON_META as $key => $meta) {
            if (! isset($users[$key])) {
                continue;
            }
            SalesShiftEmployeeProfile::updateOrCreate(
                ['user_id' => $users[$key]->id],
                [
                    'color_hex' => $meta['color'],
                    'base_channels_label' => $meta['base'],
                    'display_order' => $order++,
                ]
            );
        }
    }

    /**
     * @param  array<string, User>  $users
     * @return list<array{day_of_week: int, location_badge: ?string, lanes: list<array{user_id: int, segments: list<array>}>}>
     */
    private function dayDefinitions(array $users): array
    {
        $u = fn (string $k) => $users[$k]->id ?? null;
        $off = fn (string $k) => self::OFF_CHANNELS[$k] ?? [];
        $all = ['all'];

        $office = function (string $opener) use ($u, $off, $all) {
            $lanes = [];
            if ($uid = $u($opener)) {
                $lanes[] = [
                    'user_id' => $uid,
                    'segments' => [
                        [10, 11, 'home', implode(' · ', array_map(fn ($c) => config("sales_shifts.channels.{$c}.label", $c), $off($opener)))],
                        [11, 19, '', implode(' · ', array_map(fn ($c) => config("sales_shifts.channels.{$c}.label", $c), $off($opener)))],
                    ],
                ];
            }
            foreach (['shahd', 'mariam', 'haneen'] as $k) {
                if ($k === $opener || ! ($uid = $u($k))) {
                    continue;
                }
                $lanes[] = ['user_id' => $uid, 'segments' => [[11, 19, '', implode(' · ', array_map(fn ($c) => config("sales_shifts.channels.{$c}.label", $c), $off($k)))]]];
            }
            if ($uid = $u('esraa')) {
                $lanes[] = ['user_id' => $uid, 'segments' => [[19, 26, '', 'كل القنوات']]];
            }

            return $lanes;
        };

        return [
            ['day_of_week' => 0, 'location_badge' => 'from_office', 'lanes' => $office('shahd')],
            ['day_of_week' => 1, 'location_badge' => 'from_office', 'lanes' => $office('mariam')],
            ['day_of_week' => 2, 'location_badge' => 'from_office', 'lanes' => $office('haneen')],
            ['day_of_week' => 3, 'location_badge' => null, 'lanes' => array_values(array_filter([
                $u('shahd') ? ['user_id' => $u('shahd'), 'segments' => [[10, 14, '', 'ماسنجر · انستا · كومنتات'], [14, 18, '', 'ماسنجر · انستا']]] : null,
                $u('esraa') ? ['user_id' => $u('esraa'), 'segments' => [[10, 14, '', 'مكالمات · واتساب · فولو أب'], [14, 18, '', 'مكالمات · كومنتات']]] : null,
                $u('mariam') ? ['user_id' => $u('mariam'), 'segments' => [[14, 18, '', 'واتساب · فولو أب'], [18, 22, '', 'واتساب · مكالمات · فولو أب']]] : null,
                $u('haneen') ? ['user_id' => $u('haneen'), 'segments' => [[18, 22, '', 'ماسنجر · انستا · كومنتات'], [22, 26, '', 'كل القنوات']]] : null,
            ]))],
            ['day_of_week' => 4, 'location_badge' => null, 'lanes' => array_values(array_filter([
                $u('shahd') ? ['user_id' => $u('shahd'), 'segments' => [[10, 14, '', 'كل القنوات'], [14, 18, '', 'ماسنجر · انستا · واتساب']]] : null,
                $u('haneen') ? ['user_id' => $u('haneen'), 'segments' => [[14, 18, '', 'كومنتات · مكالمات · فولو أب'], [18, 22, '', 'ماسنجر · انستا · كومنتات']]] : null,
                $u('mariam') ? ['user_id' => $u('mariam'), 'segments' => [[18, 22, '', 'واتساب · مكالمات · فولو أب'], [22, 26, '', 'كل القنوات']]] : null,
            ]))],
            ['day_of_week' => 5, 'location_badge' => null, 'lanes' => array_values(array_filter([
                $u('esraa') ? ['user_id' => $u('esraa'), 'segments' => [[10, 14, '', 'كل القنوات'], [14, 18, '', 'مكالمات · كومنتات · ماسنجر']]] : null,
                $u('mariam') ? ['user_id' => $u('mariam'), 'segments' => [[14, 18, '', 'واتساب · انستا · فولو أب'], [18, 22, '', 'واتساب · مكالمات · فولو أب']]] : null,
                $u('haneen') ? ['user_id' => $u('haneen'), 'segments' => [[18, 22, '', 'ماسنجر · انستا · كومنتات'], [22, 26, '', 'كل القنوات']]] : null,
            ]))],
            ['day_of_week' => 6, 'location_badge' => null, 'lanes' => array_values(array_filter([
                $u('esraa') ? ['user_id' => $u('esraa'), 'segments' => [[10, 18, '', 'كل القنوات']]] : null,
                $u('shahd') ? ['user_id' => $u('shahd'), 'segments' => [[18, 26, '', 'كل القنوات']]] : null,
            ]))],
        ];
    }

    /**
     * @return list<string>
     */
    private function parseChannels(string $label): array
    {
        if (str_contains($label, 'كل القنوات')) {
            return ['all'];
        }

        $map = [
            'ماسنجر' => 'messenger',
            'انستا' => 'instagram',
            'مكالمات' => 'calls',
            'واتساب' => 'whatsapp',
            'فولو' => 'followup',
            'فولو أب' => 'followup',
            'كومنتات' => 'comments',
        ];

        $codes = [];
        foreach ($map as $ar => $code) {
            if (str_contains($label, $ar) && ! in_array($code, $codes, true)) {
                $codes[] = $code;
            }
        }

        return $codes !== [] ? $codes : ['all'];
    }
}
