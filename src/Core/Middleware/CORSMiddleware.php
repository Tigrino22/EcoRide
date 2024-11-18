<?php

namespace Tigrino\Core\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Tigrino\Core\Errors\ErrorHandler;
use Tigrino\Http\Response\JsonResponse;

class CORSMiddleware implements MiddlewareInterface
{
    protected array $settings;

    public function __construct(array $settings = [])
    {
    }

    /**
     * @inheritDoc
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $method = $request->getMethod();

        // Handle preflight (OPTIONS) requests
        if ($method === 'OPTIONS') {
            ErrorHandler::logMessage('process option');
        }

        return $handler->handle($request);
    }
}
