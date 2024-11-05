<?php

namespace Tigrino\Core\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Tigrino\Core\Exceptions\CsrfException;
use Tigrino\Services\CSRFService;

class CSRFMiddleware implements MiddlewareInterface
{
    private CSRFService $CSRFService;

    public function __construct(CSRFService $CSRFService)
    {
        $this->CSRFService = $CSRFService;
    }

    /**
     * @inheritDoc
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (in_array($request->getMethod(), ['POST', 'PUT', 'PATCH', 'DELETE'])) {
            $formName = $request->getParsedBody()['csrf_name'] ?? '';
            $token = $request->getParsedBody()['csrf_token'] ?? '';

            if (!$this->CSRFService->validateToken($formName, $token)) {
                throw new CsrfException('CSRF validation failed');
            }
        }

        return $handler->handle($request);
    }
}
