<?php

namespace Core\Router\Exception;

use PHPUnit\Framework\TestCase;
use Tigrino\Core\Router\Exception\ControllerException;

class ControllerExceptionTest extends TestCase
{

    public function testExceptionMessage(): void
    {
        $this->expectException(ControllerException::class);
        $this->expectExceptionMessage('Controller not found');

        throw new ControllerException('Controller not found');
    }

    public function testExceptionCode(): void
    {
        $this->expectException(ControllerException::class);
        $this->expectExceptionCode(404);

        throw new ControllerException('Controller not found', 404);
    }

    public function testPreviousException()
    {
        $previous = new \Exception('Previous exception');
        $exception = new ControllerException('Controller error', 500, $previous);

        $this->assertSame($previous, $exception->getPrevious());
        $this->assertSame('Controller error', $exception->getMessage());
        $this->assertSame(500, $exception->getCode());
    }


}