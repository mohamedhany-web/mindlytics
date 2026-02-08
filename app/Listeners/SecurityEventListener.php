<?php

namespace App\Listeners;

use App\Services\SecurityService;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Log;

class SecurityEventListener
{
    protected $securityService;

    public function __construct(SecurityService $securityService)
    {
        $this->securityService = $securityService;
    }

    /**
     * Handle failed login events.
     */
    public function handleFailedLogin(Failed $event): void
    {
        if (config('security.logging.log_failed_logins', true)) {
            $this->securityService->logSuspiciousActivity(
                'Failed Login Attempt',
                request(),
                "Email/Phone: " . ($event->credentials['email'] ?? $event->credentials['phone'] ?? 'unknown')
            );
        }
    }

    /**
     * Handle successful login events.
     */
    public function handleSuccessfulLogin(Login $event): void
    {
        $user = $event->user;
        
        Log::info('Successful Login', [
            'user_id' => $user->id,
            'user_name' => $user->name,
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp' => now()->toDateTimeString(),
        ]);
    }

    /**
     * Handle logout events.
     */
    public function handleLogout(Logout $event): void
    {
        if ($event->user) {
            Log::info('User Logout', [
                'user_id' => $event->user->id,
                'user_name' => $event->user->name,
                'ip' => request()->ip(),
                'timestamp' => now()->toDateTimeString(),
            ]);
        }
    }
}
