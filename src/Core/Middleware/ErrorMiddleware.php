<?php

namespace Tigrino\Core\Middleware;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Throwable;

class ErrorMiddleware implements MiddlewareInterface
{
    /**
     * @inheritDoc
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            // Pass the request to the next middleware or handler
            return $handler->handle($request);
        } catch (Throwable $exception) {
            // Handle the exception and return a custom error response
            return new Response(
                500,
                [],
                "Internal Server Error with message : {$exception->getMessage()}"
            );
        }
    }
}
