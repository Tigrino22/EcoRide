<?php

namespace Errors\Controller;

use PHPUnit\Framework\TestCase;
use Tigrino\Errors\Controller\ErrorController;
use Tigrino\Http\Errors\ForbiddenResponse;
use Tigrino\Http\Errors\NotFoundResponse;

class ErrorControllerTest extends TestCase
{
    private ErrorController $errorController;

    protected function setUp(): void
    {
        // Création d'une instance d'ErrorController
        $this->errorController = $this->getMockBuilder(ErrorController::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['render'])
            ->getMock();
    }

    public function testError403ReturnsForbiddenResponse(): void
    {
        $this->errorController
            ->expects($this->once())
            ->method('render')
            ->willReturn('Forbidden Content');

        $response = $this->errorController->error403();

        $this->assertInstanceOf(ForbiddenResponse::class, $response);
        $this->assertSame('Forbidden Content', (string) $response->getBody());
        $this->assertSame(403, $response->getStatusCode());
    }

    public function testError404ReturnsNotFoundResponse(): void
    {
        $this->errorController
            ->expects($this->once())
            ->method('render')
            ->willReturn('404 Not Found');

        $response = $this->errorController->error404();

        $this->assertInstanceOf(NotFoundResponse::class, $response);
        $this->assertSame('404 Not Found', (string) $response->getBody());
        $this->assertSame(404, $response->getStatusCode());
    }
}
