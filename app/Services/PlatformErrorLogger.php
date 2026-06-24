<?php

namespace App\Services;

use App\Models\PlatformErrorLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Throwable;

class PlatformErrorLogger
{
    private static bool $recording = false;

    /** @var list<class-string<Throwable>> */
    private const SKIP_EXCEPTIONS = [
        \Illuminate\Auth\AuthenticationException::class,
        \Illuminate\Validation\ValidationException::class,
        \Symfony\Component\HttpKernel\Exception\NotFoundHttpException::class,
        \Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException::class,
        \Illuminate\Http\Exceptions\ThrottleRequestsException::class,
    ];

    public function recordException(Throwable $e, ?Request $request = null, array $context = []): ?PlatformErrorLog
    {
        if ($this->shouldSkipException($e)) {
            return null;
        }

        $request ??= $this->safeRequest();

        return $this->persist([
            'level' => $this->levelFromException($e),
            'exception_class' => $e::class,
            'message' => mb_substr($e->getMessage() ?: class_basename($e), 0, 1000),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $this->truncateTrace($e->getTraceAsString()),
            'url' => $request?->fullUrl(),
            'method' => $request?->method(),
            'ip_address' => $request?->ip(),
            'user_agent' => $request?->userAgent(),
            'user_id' => Auth::id() ?? ($context['user_id'] ?? null),
            'context' => $this->sanitizeContext(array_merge($context, [
                'previous' => $e->getPrevious()?->getMessage(),
            ])),
            'request_input' => $this->sanitizeRequestInput($request),
        ]);
    }

    public function recordLog(string $level, string $message, array $context = []): ?PlatformErrorLog
    {
        if (! in_array($level, ['error', 'critical', 'alert', 'emergency', 'warning'], true)) {
            return null;
        }

        if (isset($context['exception']) && $context['exception'] instanceof Throwable) {
            return null;
        }

        if (str_contains($message, 'platform_error_log') || str_contains($message, 'PlatformErrorLogger')) {
            return null;
        }

        $request = $this->safeRequest();
        $mappedLevel = $level === 'emergency' ? 'critical' : ($level === 'warning' ? 'warning' : 'error');

        $file = isset($context['file']) ? (string) $context['file'] : null;
        $line = isset($context['line']) ? (int) $context['line'] : null;
        $trace = isset($context['trace']) ? $this->truncateTrace((string) $context['trace']) : null;

        return $this->persist([
            'level' => $mappedLevel,
            'exception_class' => isset($context['exception']) ? (string) $context['exception'] : null,
            'message' => mb_substr($message, 0, 1000),
            'file' => $file,
            'line' => $line,
            'trace' => $trace,
            'url' => $context['url'] ?? $request?->fullUrl(),
            'method' => $context['method'] ?? $request?->method(),
            'ip_address' => $request?->ip(),
            'user_agent' => $request?->userAgent(),
            'user_id' => $context['user_id'] ?? Auth::id(),
            'context' => $this->sanitizeContext($context),
            'request_input' => $this->sanitizeRequestInput($request),
        ]);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function persist(array $data): ?PlatformErrorLog
    {
        if (self::$recording || ! Schema::hasTable('platform_error_logs')) {
            return null;
        }

        self::$recording = true;

        try {
            $fingerprint = $this->fingerprint(
                (string) ($data['exception_class'] ?? 'log'),
                (string) ($data['file'] ?? ''),
                (int) ($data['line'] ?? 0),
                (string) ($data['message'] ?? '')
            );

            return PlatformErrorLog::create([
                'user_id' => $data['user_id'] ?? null,
                'level' => $data['level'] ?? 'error',
                'status' => 'open',
                'fingerprint' => $fingerprint,
                'exception_class' => $data['exception_class'] ?? null,
                'message' => $data['message'] ?? 'Unknown error',
                'file' => $data['file'] ?? null,
                'line' => $data['line'] ?? null,
                'trace' => $data['trace'] ?? null,
                'url' => $data['url'] ? mb_substr((string) $data['url'], 0, 2048) : null,
                'method' => $data['method'] ?? null,
                'ip_address' => $data['ip_address'] ?? null,
                'user_agent' => $data['user_agent'] ? mb_substr((string) $data['user_agent'], 0, 65000) : null,
                'context' => $data['context'] ?? null,
                'request_input' => $data['request_input'] ?? null,
            ]);
        } catch (\Throwable) {
            return null;
        } finally {
            self::$recording = false;
        }
    }

    private function shouldSkipException(Throwable $e): bool
    {
        foreach (self::SKIP_EXCEPTIONS as $class) {
            if ($e instanceof $class) {
                return true;
            }
        }

        return false;
    }

    private function levelFromException(Throwable $e): string
    {
        return match (true) {
            $e instanceof \Error => 'critical',
            default => 'error',
        };
    }

    private function fingerprint(string $class, string $file, int $line, string $message): string
    {
        return hash('sha256', $class.'|'.$file.'|'.$line.'|'.mb_substr($message, 0, 200));
    }

    private function truncateTrace(?string $trace): ?string
    {
        if ($trace === null || $trace === '') {
            return null;
        }

        return mb_strlen($trace) > 32000 ? mb_substr($trace, 0, 32000).'…' : $trace;
    }

    private function safeRequest(): ?Request
    {
        try {
            return app()->runningInConsole() ? null : request();
        } catch (\Throwable) {
            return null;
        }
    }

    /**
     * @return array<string, mixed>|null
     */
    private function sanitizeRequestInput(?Request $request): ?array
    {
        if (! $request) {
            return null;
        }

        $input = $request->except($this->sensitiveKeys());
        if ($input === []) {
            return null;
        }

        return $this->sanitizeContext($input);
    }

    /**
     * @param  array<string, mixed>  $context
     * @return array<string, mixed>|null
     */
    private function sanitizeContext(array $context): ?array
    {
        if ($context === []) {
            return null;
        }

        $out = [];
        foreach ($context as $key => $value) {
            if (in_array((string) $key, $this->sensitiveKeys(), true)) {
                $out[$key] = '[redacted]';
                continue;
            }

            if ($value instanceof Throwable) {
                $out[$key] = $value::class.': '.$value->getMessage();
                continue;
            }

            if (is_array($value)) {
                $out[$key] = $this->sanitizeContext($value);
                continue;
            }

            if (is_object($value)) {
                $out[$key] = method_exists($value, '__toString') ? (string) $value : '[object]';
                continue;
            }

            if (is_string($value) && mb_strlen($value) > 2000) {
                $out[$key] = mb_substr($value, 0, 2000).'…';
                continue;
            }

            $out[$key] = $value;
        }

        return $out;
    }

    /**
     * @return list<string>
     */
    private function sensitiveKeys(): array
    {
        return [
            'password',
            'password_confirmation',
            'current_password',
            'new_password',
            'token',
            '_token',
            'api_token',
            'secret',
            'credit_card',
            'cvv',
        ];
    }
}
