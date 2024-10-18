<?php

namespace Core\Session;

use PHPUnit\Framework\TestCase;
use Tigrino\Core\Session\SessionManager;

class SessionManagerTest extends TestCase
{
    private SessionManager $sessionManager;

    protected function setUp(): void
    {
        $this->sessionManager = new SessionManager();

        if (session_status() == PHP_SESSION_ACTIVE) {
            session_unset();
            session_destroy();
        }
    }

    public function testSetAndGetKey()
    {
        $this->sessionManager->set('key', 'value');
        $this->assertEquals('value', $this->sessionManager->get('key'));
    }

    public function testGetWithDefaultValue()
    {
        $default_value = 'value';
        $this->assertEquals($default_value, $this->sessionManager->get('key', $default_value));
    }

    public function testHasValue()
    {
        $this->sessionManager->set('key', 'value');
        $this->assertTrue($this->sessionManager->has('key'));
    }

    public function testRemoveValue()
    {
        $this->sessionManager->set('key', 'value');
        $this->assertTrue($this->sessionManager->has('key'));
        $this->sessionManager->remove('key');
        $this->assertFalse($this->sessionManager->has('key'));
    }
}