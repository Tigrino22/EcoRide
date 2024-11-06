<?php

namespace Core\Middleware;

use DI\ContainerBuilder;
use GuzzleHttp\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;
use Tigrino\Core\App;
use Tigrino\Core\Middleware\TrailingSlashMiddleware;

class TrailingslashMiddlewareTest extends TestCase
{
    public function testTrailingslashMiddleware()
    {
        $builder = new ContainerBuilder();
        $builder->addDefinitions(dirname(__DIR__, 3) . '/Config/Container.php');
        $container = $builder->build();
        $app = new App($container);

        $app->addMiddleware(TrailingSlashMiddleware::class);

        $request = new ServerRequest('GET', '/test/');
        $response = $app->run($request);

        $this->assertEquals(301, $response->getStatusCode());
        $this->assertEquals("/test", $response->getHeaderLine('Location'));
    }
}
