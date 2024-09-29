<?php

namespace Tigrino\Core\Middleware;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Run;

class WhoopsMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $whoops = new Run();
        $whoops->pushHandler(new PrettyPageHandler());
        $whoops->register();

        return $handler->handle($request);
    }
}
