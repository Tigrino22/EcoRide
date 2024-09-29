<?php

namespace Tests\Core\Middleware;

use PHPUnit\Framework\TestCase;
use Tigrino\Core\Middleware\CORSMiddleware;
use GuzzleHttp\Psr7\ServerRequest;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Server\RequestHandlerInterface;

class CORSMiddlewareTest extends TestCase
{
    protected CORSMiddleware $middleware;

    protected function setUp(): void
    {
        $this->middleware = new CORSMiddleware([
            'allowed_origins' => ['http://example.com'],
            'allowed_methods' => ['GET', 'POST'],
            'allowed_headers' => ['Content-Type', 'Authorization'],
        ]);
    }

    public function testHandlePreflightRequest()
    {
        // Simule une requête OPTIONS avec un en-tête Origin
        $request = new ServerRequest('OPTIONS', '/');
        $request = $request->withHeader('Origin', 'http://example.com');

        $handler = $this->createMock(RequestHandlerInterface::class);

        // Exécute le middleware
        $response = $this->middleware->process($request, $handler);

        // Vérifie que le middleware retourne bien les en-têtes CORS corrects
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('http://example.com', $response->getHeaderLine('Access-Control-Allow-Origin'));
        $this->assertEquals('GET, POST', $response->getHeaderLine('Access-Control-Allow-Methods'));
        $this->assertEquals('Content-Type, Authorization', $response->getHeaderLine('Access-Control-Allow-Headers'));
    }

    public function testProcessWithNormalRequest()
    {
        // Simule une requête normale (GET)
        $request = new ServerRequest('GET', '/');
        $request = $request->withHeader('Origin', 'http://example.com');

        // Mock un handler qui retourne une réponse vide
        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler->method('handle')->willReturn(new Response());

        // Exécute le middleware
        $response = $this->middleware->process($request, $handler);

        // Vérifie que les en-têtes CORS sont ajoutés
        $this->assertEquals('http://example.com', $response->getHeaderLine('Access-Control-Allow-Origin'));
        $this->assertEquals('false', $response->getHeaderLine('Access-Control-Allow-Credentials'));
    }

    public function testProcessWithInvalidOrigin()
    {
        // Simule une requête avec une origine non autorisée
        $request = new ServerRequest('GET', '/');
        $request = $request->withHeader('Origin', 'http://invalid.com');

        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler->method('handle')->willReturn(new Response());

        // Exécute le middleware
        $response = $this->middleware->process($request, $handler);

        // Vérifie que les en-têtes CORS ne sont pas ajoutés
        $this->assertEmpty($response->getHeader('Access-Control-Allow-Origin'));
    }

    public function testProcessWithExposedHeaders()
    {
        // Crée un middleware avec des 'exposed_headers' spécifiques
        $middleware = new CORSMiddleware([
            'allowed_origins' => ['http://example.com'],
            'exposed_headers' => ['X-Custom-Header', 'X-Another-Header'],
        ]);

        // Simule une requête normale (GET)
        $request = new ServerRequest('GET', '/');
        $request = $request->withHeader('Origin', 'http://example.com');

        // Mock un handler qui retourne une réponse vide
        $handler = $this->createMock(RequestHandlerInterface::class);
        $handler->method('handle')->willReturn(new Response());

        // Exécute le middleware
        $response = $middleware->process($request, $handler);

        // Vérifie que les en-têtes CORS sont ajoutés
        $this->assertEquals('http://example.com', $response->getHeaderLine('Access-Control-Allow-Origin'));

        // Vérifie que les en-têtes exposés sont correctement ajoutés
        $this->assertEquals(
            'X-Custom-Header, X-Another-Header',
            $response->getHeaderLine('Access-Control-Expose-Headers')
        );
    }
}
