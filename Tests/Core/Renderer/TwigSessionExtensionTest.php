<?php

namespace Core\Renderer;

use PHPUnit\Framework\TestCase;
use Tigrino\Core\Renderer\TwigSessionExtension;

class TwigSessionExtensionTest extends TestCase
{
    private TwigSessionExtension $extension;

    protected function setUp(): void
    {
        $this->extension = new TwigSessionExtension();

        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function testSessionWithoutKey(): void
    {
        $_SESSION['key1'] = 'value1';
        $_SESSION['key2'] = 'value2';

        $result = $this->extension->session();

        $this->assertIsArray($result);
        $this->assertArrayHasKey('key1', $result);
        $this->assertArrayHasKey('key2', $result);
    }

    public function testSessionWithKey(): void
    {
        $_SESSION['key1'] = 'value1';

        $result = $this->extension->session('key1');

        $this->assertEquals('value1', $result);
    }

    public function testSessionStartsIfNontExists(): void
    {

        if (session_status() == PHP_SESSION_ACTIVE) {
            session_destroy();
        }

        $this->extension->session();

        $this->assertEquals(PHP_SESSION_ACTIVE, session_status());
    }
}
