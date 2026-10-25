<?php

namespace App\Support\Architecture;

use App\Support\MorphMap;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\View;
use Symfony\Component\Finder\Finder;

/**
 * فحوصات سلامة نقل الكود إلى app/Domains (docs/MODULARIZATION_PLAN.md).
 *
 * تُستخدم من اختبارات tests/Feature/Architecture ومن الأمر `php artisan modularity:verify` على السيرفر،
 * فيفحص السيرفر بعد الرفع نفس ما تفحصه الاختبارات. كل دالة تُرجع قائمة مشاكل (فارغة = سليم) ولا تكتب أي شيء.
 */
final class ModularityChecks
{
    /**
     * Views غير موجودة من قبل النقل (ترجع 500 على main أيضاً) – موثّقة في القسم 15 و 16 من الخطة.
     * لا تضف أسماء جديدة هنا: أي view مفقود جديد يعني أن النقل كسر شيئاً.
     */
    public const KNOWN_MISSING_VIEWS = [
        'tasks.index', 'tasks.create', 'tasks.edit', 'tasks.show',
        'admin.attendance.index', 'admin.attendance.lecture',
        'admin.activity-log', 'admin.statistics', 'admin.notifications.statistics',
        'package-show',
        'accounting::admin.expenses.edit',
        'sales::admin.groups.whatsapp-batch',
        'learning::admin.subjects.create', 'learning::admin.subjects.show', 'learning::admin.subjects.edit',
        'learning::admin.advanced-courses.students', 'learning::admin.advanced-courses.statistics',
        'learning::instructor.questions.index', 'learning::instructor.questions.create', 'learning::instructor.questions.show',
    ];

    /** كلاسات اختيارية يتحقق الكود من وجودها بـ class_exists قبل استخدامها. */
    public const OPTIONAL_CLASSES = [
        'App\\Models\\ReviewHelpful',
    ];

    /** قوالب Blade فيها خطأ PHP من قبل النقل (نفس الخطأ على main). */
    public const KNOWN_BROKEN_TEMPLATES = [
        'cms::admin.blog.index', 'cms::admin.blog.show',
        'cms::admin.faq.index', 'cms::admin.faq.show',
        'cms::admin.media.show',
        'accounting::admin.subscriptions.show',
        'learning::admin.academic-subjects.show',
        'learning::admin.question-bank.show',
        'learning::admin.classrooms.show', 'learning::admin.classrooms.students',
        'learning::admin.question-categories.show',
        'messaging::admin.messages.templates',
    ];

    /** Observers يجب أن تبقى مسجّلة بعد نقلها إلى Providers المجالات: model => observer. */
    public const EXPECTED_OBSERVERS = [
        \App\Models\User::class => \App\Observers\UserObserver::class,
        \App\Domains\Hr\Models\EmployeeSalaryDeduction::class => \App\Domains\Hr\Observers\EmployeeSalaryDeductionObserver::class,
        \App\Domains\Learning\Models\StudentCourseEnrollment::class => \App\Domains\Learning\Observers\EnrollmentObserver::class,
        \App\Domains\Learning\Models\Exam::class => \App\Domains\Learning\Observers\ExamObserver::class,
        \App\Domains\Learning\Models\AdvancedCourse::class => \App\Domains\Learning\Observers\AdvancedCourseObserver::class,
        \App\Domains\Learning\Models\ExamAttempt::class => \App\Domains\Learning\Observers\ExamAttemptObserver::class,
        \App\Domains\Learning\Models\Lecture::class => \App\Domains\Learning\Observers\LectureObserver::class,
        \App\Domains\Learning\Models\Assignment::class => \App\Domains\Learning\Observers\AssignmentObserver::class,
        \App\Domains\Learning\Models\LectureAssignment::class => \App\Domains\Learning\Observers\LectureAssignmentObserver::class,
    ];

    /**
     * أعمدة تحفظ أسماء كلاسات في الداتابيز: table.column => وصف.
     * كل قيمة محفوظة يجب أن تُحل إلى كلاس موجود (عبر MorphMap أو LegacyClassAliases).
     */
    public const STORED_CLASS_COLUMNS = [
        'curriculum_items.item_type' => 'منهج الكورسات الأونلاين',
        'offline_curriculum_items.item_type' => 'منهج الكورسات الأوفلاين',
        'course_community_reactions.reactable_type' => 'تفاعلات مجتمع الكورس',
        'journey_share_events.shareable_type' => 'مشاركات الرحلة والبورتفوليو',
        'personal_access_tokens.tokenable_type' => 'توكنات Sanctum',
    ];

    /**
     * كل اسم كلاس App\… مكتوب في الكود (PHP و Blade) يجب أن يكون موجوداً.
     * يكشف imports و namespaces خاطئة في أي ملف حتى لو لم تُفتح صفحته.
     *
     * @return list<string>
     */
    public static function missingClassReferences(): array
    {
        $problems = [];

        foreach (self::phpFiles() as $path) {
            foreach (self::classNamesInPhp(file_get_contents($path)) as [$name, $line]) {
                if (! self::typeExists($name) && ! in_array($name, self::OPTIONAL_CLASSES, true)) {
                    $problems[] = self::rel($path).":{$line} → {$name}";
                }
            }
        }

        foreach (self::bladeFiles() as $path) {
            $text = file_get_contents($path);
            if (! preg_match_all('/(?<![\w\\\\\'"$])\\\\?App\\\\(?:[A-Za-z_]\w*\\\\)*[A-Za-z_]\w*/', $text, $m, PREG_OFFSET_CAPTURE)) {
                continue;
            }
            foreach ($m[0] as [$name, $offset]) {
                $name = ltrim($name, '\\');
                if (! self::typeExists($name) && ! in_array($name, self::OPTIONAL_CLASSES, true)) {
                    $problems[] = self::rel($path).':'.(substr_count(substr($text, 0, $offset), "\n") + 1)." → {$name}";
                }
            }
        }

        return array_values(array_unique($problems));
    }

    /**
     * كل اسم view ثابت في view() / ->view() / view: / @include / @extends / @each / @component موجود.
     *
     * @return list<string>
     */
    public static function missingViewReferences(): array
    {
        $pattern = '/(?:\bview\s*\(|->view\s*\(|View::make\s*\(|@include\s*\(|@includeWhen\s*\([^\'"\)]*?,|@includeUnless\s*\([^\'"\)]*?,|@extends\s*\(|@each\s*\(|@component\s*\(|\bview:|\bmarkdown:)\s*([\'"])([A-Za-z0-9_.:\-]+)\1/';
        $problems = [];

        foreach (array_merge(iterator_to_array(self::phpFiles(), false), iterator_to_array(self::bladeFiles(), false)) as $path) {
            $isBlade = str_ends_with($path, '.blade.php');
            $text = $isBlade ? file_get_contents($path) : self::withoutComments(file_get_contents($path));
            if (! preg_match_all($pattern, $text, $m, PREG_OFFSET_CAPTURE)) {
                continue;
            }
            foreach ($m[2] as $k => [$name, $offset]) {
                // view: في Blade خاصية JavaScript؛ اسم ينتهي بنقطة = بداية اسم يُركّب ديناميكياً
                if (($isBlade && str_starts_with($m[0][$k][0], 'view:')) || str_ends_with($name, '.')) {
                    continue;
                }
                if (in_array($name, self::KNOWN_MISSING_VIEWS, true) || View::exists($name)) {
                    continue;
                }
                $problems[] = self::rel($path).':'.(substr_count(substr($text, 0, $offset), "\n") + 1)." → view [{$name}]";
            }
        }

        return array_values(array_unique($problems));
    }

    /**
     * كل قالب Blade في المشروع (resources/views وكل app/Domains/*\/resources/views) يُترجم إلى PHP صالح.
     *
     * @return list<string>
     */
    public static function brokenBladeTemplates(): array
    {
        $problems = [];

        foreach (self::viewRoots() as $namespace => $root) {
            foreach ((new Finder)->files()->in($root)->name('*.blade.php') as $file) {
                $name = ($namespace === '' ? '' : $namespace.'::')
                    .str_replace(['/', '.blade.php'], ['.', ''], $file->getRelativePathname());
                if (in_array($name, self::KNOWN_BROKEN_TEMPLATES, true)) {
                    continue;
                }
                try {
                    token_get_all(Blade::compileString($file->getContents()), TOKEN_PARSE);
                } catch (\Throwable $e) {
                    $problems[] = "{$name}: ".$e->getMessage().' (compiled line '.$e->getLine().')';
                }
            }
        }

        return $problems;
    }

    /**
     * أسماء الكلاسات المحفوظة في الداتابيز لا تتغير بالنقل:
     * - لا نص ثابت يحتوي App\Domains\… (يعني أن قيمة محفوظة أو مُرسلة من الـ JS تغيّرت).
     * - كل مفتاح في MorphMap يرجع بنفس القيمة القديمة لكلاسه، والعكس.
     * - لا كتابة أو مقارنة لعمود *_type بـ X::class مباشرة (يجب Relation::getMorphAlias).
     *
     * @return list<string>
     */
    public static function storedClassNameProblems(): array
    {
        $problems = [];

        foreach (MorphMap::map() as $alias => $class) {
            // المفتاح هو النص المحفوظ في الداتابيز: لازم يفضل الاسم القديم، مش ::class الجديد
            if (! str_starts_with($alias, 'App\\Models\\')) {
                $problems[] = "MorphMap: key {$alias} is not the stored legacy name (App\\Models\\…)";
            }
            if (Relation::getMorphAlias($class) !== $alias) {
                $problems[] = "MorphMap: getMorphAlias({$class}) = ".Relation::getMorphAlias($class).", expected {$alias}";
            }
            if (Relation::getMorphedModel($alias) !== $class) {
                $problems[] = "MorphMap: {$alias} is not registered for {$class}";
            }
        }

        foreach (self::phpFiles() as $path) {
            if (str_ends_with($path, 'LegacyClassAliases.php')) {
                continue;
            }
            $code = file_get_contents($path);
            foreach (token_get_all($code) as $token) {
                if (is_array($token) && $token[0] === T_CONSTANT_ENCAPSED_STRING && preg_match('/^[\'"]\\\\{0,2}App\\\\{1,2}Domains\\\\/', $token[1])) {
                    $problems[] = self::rel($path).":{$token[2]} string literal holds a moved class name: {$token[1]}";
                }
                if (is_array($token) && $token[0] === T_CONSTANT_ENCAPSED_STRING && preg_match('/\bin:[^\'"]*App\\\\{1,2}Domains\\\\/', $token[1])) {
                    $problems[] = self::rel($path).":{$token[2]} validation rule lists moved class names: {$token[1]}";
                }
            }
            $code = self::withoutComments($code);
            if (preg_match_all('/[\'"](\w+_type)[\'"]\s*(?:=>|,)\s*\\\\?[\w\\\\]+::class|->(\w+_type)\s*[!=]==?\s*\\\\?[\w\\\\]+::class/', $code, $m, PREG_OFFSET_CAPTURE)) {
                foreach ($m[0] as [$snippet, $offset]) {
                    $problems[] = self::rel($path).':'.(substr_count(substr($code, 0, $offset), "\n") + 1)." *_type compared/written with ::class (use Relation::getMorphAlias): {$snippet}";
                }
            }
        }

        foreach (self::bladeFiles() as $path) {
            $text = file_get_contents($path);
            if (preg_match_all('/[\'"]\\\\{0,2}App\\\\{1,2}Domains\\\\/', $text, $m, PREG_OFFSET_CAPTURE)) {
                foreach ($m[0] as [, $offset]) {
                    $problems[] = self::rel($path).':'.(substr_count(substr($text, 0, $offset), "\n") + 1).' quoted moved class name in a template';
                }
            }
        }

        return $problems;
    }

    /**
     * ربط المجالات: كل Provider مسجّل ومحمّل، لكل مجال views namespace باسمه،
     * كل ملف routes داخل مجال يُستدعى بـ require مرة واحدة بالضبط، والـ observers مسجّلة.
     *
     * @return list<string>
     */
    public static function wiringProblems(): array
    {
        $problems = [];
        $providersFile = file_get_contents(base_path('bootstrap/providers.php'));
        $hints = View::getFinder()->getHints();

        foreach (glob(app_path('Domains/*'), GLOB_ONLYDIR) as $dir) {
            $domain = basename($dir);
            $provider = "App\\Domains\\{$domain}\\{$domain}ServiceProvider";
            if (! class_exists($provider)) {
                $problems[] = "{$domain}: provider {$provider} missing";
            } else {
                if (! str_contains($providersFile, $provider.'::class')) {
                    $problems[] = "{$domain}: {$provider} not listed in bootstrap/providers.php";
                }
                if (app()->getProvider($provider) === null) {
                    $problems[] = "{$domain}: {$provider} not loaded";
                }
            }
            if (is_dir("{$dir}/resources/views") && ! in_array(realpath("{$dir}/resources/views"), array_map('realpath', $hints[strtolower($domain)] ?? []), true)) {
                $problems[] = "{$domain}: views namespace '".strtolower($domain)."' not registered";
            }
        }

        $requireSources = '';
        foreach ((new Finder)->files()->in(base_path('routes'))->name('*.php') as $file) {
            $requireSources .= self::withoutComments($file->getContents());
        }
        foreach ((new Finder)->files()->in(app_path('Domains'))->path('/routes/')->name('*.php') as $file) {
            $requireSources .= self::withoutComments($file->getContents());
        }
        foreach ((new Finder)->files()->in(app_path('Domains'))->path('/routes/')->name('*.php') as $file) {
            $relative = 'Domains/'.str_replace('\\', '/', substr($file->getPathname(), strlen(app_path('Domains')) + 1));
            $count = substr_count($requireSources, "app_path('{$relative}')");
            if ($count !== 1) {
                $problems[] = "route file app/{$relative} is required {$count} times (expected 1)";
            }
        }

        $events = ['retrieved', 'creating', 'created', 'updating', 'updated', 'saving', 'saved', 'deleting', 'deleted', 'restoring', 'restored', 'forceDeleting', 'forceDeleted'];
        foreach (self::EXPECTED_OBSERVERS as $model => $observer) {
            $observed = array_values(array_filter($events, fn ($e) => method_exists($observer, $e)));
            foreach ($observed as $event) {
                if (! Event::hasListeners("eloquent.{$event}: {$model}")) {
                    $problems[] = "observer {$observer} not registered for {$model} ({$event})";
                    break;
                }
            }
        }

        return $problems;
    }

    /**
     * لقطة نصية لأوامر artisan و الجدولة لمقارنتها قبل وبعد النقل.
     */
    public static function consoleSnapshot(\Illuminate\Contracts\Console\Kernel $kernel, \Illuminate\Console\Scheduling\Schedule $schedule): string
    {
        $commands = array_keys($kernel->all());
        sort($commands);

        $events = array_map(
            fn ($e) => $e->expression.' '.preg_replace('/^.*artisan[\'"]?\s+/', '', (string) $e->command),
            $schedule->events()
        );
        sort($events);

        return "# commands\n".implode("\n", $commands)."\n\n# schedule\n".implode("\n", $events)."\n";
    }

    /** @return iterable<string> */
    private static function phpFiles(): iterable
    {
        $finder = (new Finder)->files()->name('*.php')->notName('*.blade.php')
            ->in([app_path(), base_path('routes'), config_path(), database_path('seeders')]);
        foreach ($finder as $file) {
            yield $file->getPathname();
        }
        yield base_path('bootstrap/app.php');
        yield base_path('bootstrap/providers.php');
    }

    /** @return iterable<string> */
    private static function bladeFiles(): iterable
    {
        foreach (self::viewRoots() as $root) {
            foreach ((new Finder)->files()->in($root)->name('*.blade.php') as $file) {
                yield $file->getPathname();
            }
        }
    }

    /** @return array<string, string> namespace ('' = بدون namespace) => المجلد */
    private static function viewRoots(): array
    {
        $roots = ['' => resource_path('views')];
        foreach (glob(app_path('Domains/*/resources/views'), GLOB_ONLYDIR) as $dir) {
            $roots[strtolower(basename(dirname($dir, 2)))] = $dir;
        }

        return $roots;
    }

    /** @return list<array{0: string, 1: int}> أسماء App\… في الكود (بدون namespace declarations و use function/const) */
    private static function classNamesInPhp(string $code): array
    {
        $tokens = token_get_all($code);
        $names = [];
        $skipNext = false;
        $count = count($tokens);

        for ($i = 0; $i < $count; $i++) {
            $t = $tokens[$i];
            if (! is_array($t)) {
                continue;
            }
            if ($t[0] === T_NAMESPACE || ($t[0] === T_USE && self::nextMeaningful($tokens, $i) instanceof \stdClass)) {
                $skipNext = true;

                continue;
            }
            if (! in_array($t[0], [T_NAME_FULLY_QUALIFIED, T_NAME_QUALIFIED], true)) {
                continue;
            }
            if ($skipNext) {
                $skipNext = false;

                continue;
            }
            $name = ltrim($t[1], '\\');
            if (! str_starts_with($name, 'App\\')) {
                continue;
            }
            $next = self::nextMeaningful($tokens, $i);
            if ($next === '{' || $next === '(' && ! self::previousIsNew($tokens, $i)) {
                continue; // group use prefix أو استدعاء دالة
            }
            $names[] = [$name, $t[2]];
        }

        return $names;
    }

    /** @return string|\stdClass|null الـ token التالي غير الفارغ؛ stdClass يعني «use function/const» */
    private static function nextMeaningful(array $tokens, int $i): string|\stdClass|null
    {
        for ($j = $i + 1; $j < count($tokens); $j++) {
            $t = $tokens[$j];
            if (is_array($t) && in_array($t[0], [T_WHITESPACE, T_COMMENT, T_DOC_COMMENT], true)) {
                continue;
            }
            if (is_array($t) && in_array(strtolower($t[1]), ['function', 'const'], true) && $tokens[$i][0] === T_USE) {
                return new \stdClass;
            }

            return is_array($t) ? $t[1] : $t;
        }

        return null;
    }

    private static function previousIsNew(array $tokens, int $i): bool
    {
        for ($j = $i - 1; $j >= 0; $j--) {
            $t = $tokens[$j];
            if (is_array($t) && in_array($t[0], [T_WHITESPACE, T_COMMENT, T_DOC_COMMENT], true)) {
                continue;
            }

            return is_array($t) && $t[0] === T_NEW;
        }

        return false;
    }

    /** الكود بدون تعليقات مع الحفاظ على أرقام الأسطر */
    private static function withoutComments(string $code): string
    {
        $out = '';
        foreach (token_get_all($code) as $t) {
            if (is_array($t) && in_array($t[0], [T_COMMENT, T_DOC_COMMENT], true)) {
                $out .= str_repeat("\n", substr_count($t[1], "\n"));

                continue;
            }
            $out .= is_array($t) ? $t[1] : $t;
        }

        return $out;
    }

    private static function typeExists(string $name): bool
    {
        return class_exists($name) || interface_exists($name) || trait_exists($name) || enum_exists($name);
    }

    private static function rel(string $path): string
    {
        return ltrim(str_replace(base_path(), '', $path), '/');
    }
}
