<?php

namespace Tests\Security;

use Symfony\Component\Process\Process;
use Tests\TestCase;

/**
 * Ensures the static security audit script stays runnable (CI / local).
 */
class SecurityAuditScriptTest extends TestCase
{
    public function test_security_audit_script_exits_zero(): void
    {
        $script = base_path('scripts/security_audit.php');
        $this->assertFileExists($script);

        $process = new Process([PHP_BINARY, $script], base_path());
        $process->setTimeout(300);
        $process->run();

        $this->assertTrue(
            $process->isSuccessful(),
            'security_audit.php failed: '.$process->getErrorOutput().$process->getOutput()
        );
    }
}
