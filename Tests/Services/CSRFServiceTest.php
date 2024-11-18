<?php

namespace Services;

use PHPUnit\Framework\TestCase;
use Tigrino\Core\Session\SessionManager;
use Tigrino\Services\CSRFService;

class CSRFServiceTest extends TestCase
{
    public function testGenerateToken()
    {
        $session = new SessionManager();
        $csrfService = new CSRFService($session);

        $token = $csrfService->generateToken('test');

        $this->assertEquals(64, strlen($token));
        $this->assertTrue($session->has('csrf_test'));
    }

    public function testValidateToken()
    {
        $session = new SessionManager();
        $csrfService = new CSRFService($session);

        $token = $csrfService->generateToken('test');

        $this->assertTrue($csrfService->validateToken('test', $token));

        $token = $csrfService->generateToken('test2');

        $this->assertFalse($csrfService->validateToken('test2', 'jgsjhbdbjslnqklq'));
    }
}