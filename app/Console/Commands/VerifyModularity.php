<?php

namespace App\Console\Commands;

use App\Support\Architecture\ModularityChecks;
use App\Support\LegacyClassAliases;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * فحص سلامة النقل إلى app/Domains على السيرفر بعد الرفع (docs/DEPLOY_TESTING.md).
 * لا يكتب أي شيء: قراءة ملفات و SELECT فقط.
 */
class VerifyModularity extends Command
{
    protected $signature = 'modularity:verify
        {--db : فحص أسماء الكلاسات المحفوظة في الداتابيز (SELECT فقط)}';

    protected $description = 'التحقق من أن نقل الكود إلى app/Domains سليم (classes, views, routes, providers, القيم المحفوظة)';

    public function handle(): int
    {
        $checks = [
            'أسماء الكلاسات في الكود موجودة' => fn () => ModularityChecks::missingClassReferences(),
            'الـ views المستخدمة موجودة' => fn () => ModularityChecks::missingViewReferences(),
            'قوالب Blade تُترجم بدون أخطاء' => fn () => ModularityChecks::brokenBladeTemplates(),
            'الـ routes تشير لـ controllers موجودة' => fn () => $this->brokenRoutes(),
            'الأسماء القديمة (aliases) تُحل' => fn () => $this->brokenAliases(),
            'أسماء الكلاسات المحفوظة لم تتغير في الكود' => fn () => ModularityChecks::storedClassNameProblems(),
            'Providers و views namespaces و routes و observers' => fn () => ModularityChecks::wiringProblems(),
        ];
        if ($this->option('db')) {
            $checks['القيم المحفوظة في الداتابيز تُحل لكلاسات موجودة'] = fn () => $this->unresolvableStoredValues();
        }

        $failed = 0;
        foreach ($checks as $label => $run) {
            $problems = $run();
            if ($problems === []) {
                $this->line("  <fg=green>✓</> {$label}");

                continue;
            }
            $failed++;
            $this->line("  <fg=red>✗</> {$label} (".count($problems).')');
            foreach (array_slice($problems, 0, 25) as $problem) {
                $this->line("      - {$problem}");
            }
            if (count($problems) > 25) {
                $this->line('      … و '.(count($problems) - 25).' أخرى');
            }
        }

        if ($this->option('db')) {
            $this->reportInfo();
        }

        $this->newLine();
        if ($failed > 0) {
            $this->error("{$failed} فحص فشل. لا تكمل النشر قبل إصلاحه.");

            return self::FAILURE;
        }
        $this->info('كل الفحوصات سليمة.');

        return self::SUCCESS;
    }

    /** @return list<string> */
    private function brokenRoutes(): array
    {
        $problems = [];
        foreach (app('router')->getRoutes()->getRoutes() as $route) {
            $uses = $route->getAction('uses');
            if (! is_string($uses)) {
                continue;
            }
            [$class, $method] = str_contains($uses, '@') ? explode('@', $uses, 2) : [$uses, '__invoke'];
            if (! class_exists($class) || ! method_exists($class, $method)) {
                $problems[] = "{$route->uri()} → {$class}@{$method}";
            }
        }

        return $problems;
    }

    /** @return list<string> */
    private function brokenAliases(): array
    {
        $problems = [];
        foreach (LegacyClassAliases::MAP as $old => $new) {
            if (! class_exists($new)) {
                $problems[] = "{$old} → {$new} (target missing)";
            } elseif (! class_exists($old) || ! is_a($old, $new, true)) {
                $problems[] = "{$old} does not resolve to {$new}";
            }
        }

        return $problems;
    }

    /** @return list<string> */
    private function unresolvableStoredValues(): array
    {
        $problems = [];
        foreach (ModularityChecks::STORED_CLASS_COLUMNS as $column => $label) {
            [$table, $field] = explode('.', $column);
            if (! Schema::hasTable($table) || ! Schema::hasColumn($table, $field)) {
                continue;
            }
            foreach (DB::table($table)->select($field)->distinct()->pluck($field) as $value) {
                if ($value === null || $value === '') {
                    continue;
                }
                $class = Relation::getMorphedModel($value) ?? LegacyClassAliases::resolve($value);
                if (! class_exists($class)) {
                    $problems[] = "{$column} ({$label}): «{$value}» لا يُحل إلى كلاس";
                }
            }
        }

        if (Schema::hasTable('activity_logs') && Schema::hasColumn('activity_logs', 'model_type')) {
            foreach (DB::table('activity_logs')->whereNotNull('model_type')->select('model_type')->distinct()->pluck('model_type') as $value) {
                if (str_contains($value, '\\') && ! class_exists(LegacyClassAliases::resolve($value))) {
                    $problems[] = "activity_logs.model_type: «{$value}» لا يُحل إلى كلاس";
                }
            }
        }

        return $problems;
    }

    private function reportInfo(): void
    {
        $this->newLine();
        $this->line('  معلومات (لا تُفشل الفحص):');
        if (Schema::hasTable('jobs')) {
            $pending = DB::table('jobs')->count();
            $this->line("    - jobs في الطابور: {$pending}".($pending > 0 ? ' — يُفضّل تفريغه قبل النشر: php artisan queue:work --stop-when-empty' : ''));
        }
        if (Schema::hasTable('failed_jobs')) {
            $this->line('    - failed_jobs: '.DB::table('failed_jobs')->count());
        }
        foreach (ModularityChecks::STORED_CLASS_COLUMNS as $column => $label) {
            [$table, $field] = explode('.', $column);
            if (Schema::hasTable($table) && Schema::hasColumn($table, $field)) {
                $values = DB::table($table)->select($field)->distinct()->pluck($field)->filter()->implode('، ');
                $this->line("    - {$column}: ".($values !== '' ? $values : '—'));
            }
        }
    }
}
