<?php

namespace Tests\Security;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Session\Store;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

/**
 * Laravel skips CSRF during PHPUnit (VerifyCsrfToken::runningUnitTests).
 * We assert the middleware itself rejects missing tokens when that bypass is off.
 */
class CsrfProtectionTest extends TestCase
{
    public function test_verify_csrf_token_rejects_post_without_token(): void
    {
        $middleware = new class ($this->app, $this->app->make('encrypter')) extends VerifyCsrfToken
        {
            protected function runningUnitTests(): bool
            {
                return false;
            }
        };

        $session = new Store('test-session', new \Illuminate\Session\ArraySessionHandler(
            $this->app->make('cache')->driver()
        ));
        $session->start();
        $session->regenerateToken();

        $request = Request::create('/login', 'POST', [
            'email' => 'x@example.test',
            'password' => 'password12',
        ]);
        $request->setLaravelSession($session);

        $this->expectException(TokenMismatchException::class);

        $middleware->handle($request, function (): Response {
            return new Response('should not reach');
        });
    }
}
