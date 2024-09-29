<?php

namespace Tests\Core\Middleware;

use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;
use Psr\Http\Server\RequestHandlerInterface;
use Tigrino\Core\Middleware\ErrorMiddleware;

class ErrorMiddlewareTest extends TestCase
{
    public function testProcessWithoutException()
    {
        $middleware = new ErrorMiddleware();

        // Simule une requête
        $request = new ServerRequest('GET', '/');

        // Mock un handler qui retourne une réponse normale
        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler->method('handle')->willReturn(new Response(200, [], 'OK'));

        // Exécute le middleware
        $response = $middleware->process($request, $handler);

        // Vérifie que le statut de la réponse est 200
        $this->assertEquals(200, $response->getStatusCode());

        // Vérifie le contenu de la réponse
        $this->assertEquals('OK', (string)$response->getBody());
    }

    public function testProcessWithException()
    {
        $middleware = new ErrorMiddleware();

        $request = new ServerRequest('GET', '/');

        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler->method('handle')->will($this->throwException(new \Exception('Something went wrong')));

        // Exécute le middleware
        $response = $middleware->process($request, $handler);

        // Vérifie que le statut de la réponse est 500
        $this->assertEquals(500, $response->getStatusCode());
        $this->assertStringContainsString('Internal Server Error', $response->getBody());
    }
}
