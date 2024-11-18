<?php

namespace Services;

use PHPUnit\Framework\TestCase;
use Tigrino\Core\Session\SessionManager;
use Tigrino\Services\FlashService;

class FlashServiceTest extends TestCase
{
    public function testAddFlash()
    {
        $flashService = new FlashService(new SessionManager());

        $flashService->add('test', 'message_test');

        $result = $flashService->getMessages();
        $this->assertCount(1, $result);
        $this->assertEquals('message_test', $result['test'][0]);
        $this->assertCount(0, $flashService->getMessages());
    }
}