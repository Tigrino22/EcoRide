<?php

namespace Tigrino\Core\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Tigrino\Http\Response\JsonResponse;

class CORSMiddleware implements MiddlewareInterface
{
    protected array $settings;

    public function __construct(array $settings = [])
    {
        $this->settings = array_merge([
            'allowed_origins' => ['*'],
            'allowed_methods' => ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'],
            'allowed_headers' => ['Content-Type', 'Authorization'],
            'exposed_headers' => [],
            'max_age' => 3600,
            'allow_credentials' => false,
        ], $settings);
    }

    /**
     * @inheritDoc
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $origin = $request->getHeaderLine('Origin');
        $method = $request->getMethod();

        if ($method === 'OPTIONS') {
            return $this->handlePreflightRequest($request);
        }

        $response = $handler->handle($request);

        return $this->addCorsHeaders($response, $origin);
    }

    protected function handlePreflightRequest(ServerRequestInterface $request): ResponseInterface
    {
        $origin = $request->getHeaderLine('Origin');

        $response = new JsonResponse(status: 200, data: []);

        return $this->addCorsHeaders($response, $origin)
            ->withHeader('Access-Control-Allow-Methods', implode(', ', $this->settings['allowed_methods']))
            ->withHeader('Access-Control-Allow-Headers', implode(', ', $this->settings['allowed_headers']))
            ->withHeader('Access-Control-Max-Age', (string)$this->settings['max_age']);
    }

    protected function addCorsHeaders(ResponseInterface $response, string $origin): ResponseInterface
    {
        if (
            in_array($origin, $this->settings['allowed_origins'])
            || in_array('*', $this->settings['allowed_origins'])
        ) {
            $response = $response->withHeader('Access-Control-Allow-Origin', $origin)
                ->withHeader(
                    'Access-Control-Allow-Credentials',
                    $this->settings['allow_credentials'] ? 'true' : 'false'
                );
        }

        if (!empty($this->settings['exposed_headers'])) {
            $response = $response->withHeader(
                'Access-Control-Expose-Headers',
                implode(', ', $this->settings['exposed_headers'])
            );
        }

        return $response;
    }
}
