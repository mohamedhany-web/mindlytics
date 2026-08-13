<?php

namespace App\Services;

use App\Models\SalesShiftEmployeeProfile;
use App\Models\SalesShiftPlan;
use App\Models\SalesShiftSegment;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * يستورد جدول شيفتات فريق المبيعات الحالي (شهد · حنين · مريم · إسراء — كل القنوات).
 */
class SalesShiftPlanImporter
{
    private const PERSON_META = [
        'shahd' => ['match' => ['شهد', 'shahd'], 'color' => '#F5A623', 'base' => 'كل القنوات'],
        'haneen' => ['match' => ['حنين', 'haneen', 'hanin'], 'color' => '#8BD450', 'base' => 'كل القنوات'],
        'mariam' => ['match' => ['مريم', 'mariam', 'maryam'], 'color' => '#4DD0E1', 'base' => 'كل القنوات'],
        'esraa' => ['match' => ['إسراء', 'اسراء', 'esraa', 'israa'], 'color' => '#EC6A9C', 'base' => 'كل القنوات'],
    ];

    public function importDefaultPlan(bool $activate = true): SalesShiftPlan
    {
        return DB::transaction(function () use ($activate) {
            if ($activate) {
                SalesShiftPlan::query()->update(['is_active' => false]);
            }

            $plan = SalesShiftPlan::create([
                'name' => 'جدول شيفتات فريق المبيعات',
                'description' => 'شهد · حنين · مريم · إسراء — 10 ص إلى 2 ص، كل القنوات',
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
                            'mode' => ($seg[2] ?? '') === 'home'
                                ? SalesShiftSegment::MODE_HOME
                                : SalesShiftSegment::MODE_NORMAL,
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
     * 0=سبت … 6=جمعة — مطابق لجدول Mindlytics الحالي.
     *
     * @param  array<string, User>  $users
     * @return list<array{day_of_week: int, location_badge: ?string, lanes: list<array{user_id: int, segments: list<array>}>}>
     */
    private function dayDefinitions(array $users): array
    {
        $u = fn (string $k) => $users[$k]->id ?? null;
        $allLabel = 'كل القنوات';

        /** @param list{0: string, 1: string, 2: string, 3: string} $rotation person keys for 10-14, 14-18, 18-22, 22-26 */
        $fourShifts = function (string $p10, string $p14, string $p18, string $p22) use ($u, $allLabel): array {
            $lanes = [];
            foreach ([[$p10, 10, 14], [$p14, 14, 18], [$p18, 18, 22], [$p22, 22, 26]] as [$person, $start, $end]) {
                if ($uid = $u($person)) {
                    $lanes[] = [
                        'user_id' => $uid,
                        'segments' => [[$start, $end, 'normal', $allLabel]],
                    ];
                }
            }

            return $lanes;
        };

        return [
            // سبت · أحد · اثنين — نفس الدوران
            ['day_of_week' => 0, 'location_badge' => null, 'lanes' => $fourShifts('shahd', 'haneen', 'mariam', 'esraa')],
            ['day_of_week' => 1, 'location_badge' => null, 'lanes' => $fourShifts('shahd', 'haneen', 'mariam', 'esraa')],
            ['day_of_week' => 2, 'location_badge' => null, 'lanes' => $fourShifts('shahd', 'haneen', 'mariam', 'esraa')],
            // ثلاثاء · خميس — إسراء مساءً ومريم ليلاً
            ['day_of_week' => 3, 'location_badge' => null, 'lanes' => $fourShifts('shahd', 'haneen', 'esraa', 'mariam')],
            // أربعاء — شهد 10-6 · مريم 6-2 · إسراء+حنين أجازة
            [
                'day_of_week' => 4,
                'location_badge' => null,
                'lanes' => array_values(array_filter([
                    $u('shahd') ? ['user_id' => $u('shahd'), 'segments' => [[10, 18, 'normal', $allLabel]]] : null,
                    $u('mariam') ? ['user_id' => $u('mariam'), 'segments' => [[18, 26, 'normal', $allLabel]]] : null,
                ])),
            ],
            ['day_of_week' => 5, 'location_badge' => null, 'lanes' => $fourShifts('shahd', 'haneen', 'esraa', 'mariam')],
            // جمعة — حنين 10-6 · إسراء 6-2 · شهد+مريم أجازة
            [
                'day_of_week' => 6,
                'location_badge' => null,
                'lanes' => array_values(array_filter([
                    $u('haneen') ? ['user_id' => $u('haneen'), 'segments' => [[10, 18, 'normal', $allLabel]]] : null,
                    $u('esraa') ? ['user_id' => $u('esraa'), 'segments' => [[18, 26, 'normal', $allLabel]]] : null,
                ])),
            ],
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
