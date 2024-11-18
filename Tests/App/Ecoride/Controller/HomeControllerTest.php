<?php

namespace App\Ecoride\Controller;

use DI\ContainerBuilder;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Tests\Core\Renderer\FakeRenderer;
use Tigrino\App\Ecoride\Controller\HomeController;
use Tigrino\Core\Renderer\RendererInterface;
use function DI\autowire;

class HomeControllerTest extends TestCase
{
    private ContainerInterface $container;
    private HomeController $controller;

    protected function setUp(): void
    {
        $builder = new ContainerBuilder();
        $builder->addDefinitions([
            RendererInterface::class => autowire(FakeRenderer::class),
        ]);
        $this->container = $builder->build();

        $this->controller = new HomeController($this->container);
    }

    public function testIndex()
    {
        /** @var Response $result */
        $result = $this->controller->index();

        $this->assertInstanceOf(Response::class, $result);
        $this->assertEquals(200, $result->getStatusCode());
    }

    public function testContact()
    {
        /** @var Response $result */
        $result = $this->controller->contact();

        $this->assertInstanceOf(Response::class, $result);
        $this->assertEquals(200, $result->getStatusCode());
    }

    public function testAdmin()
    {
        /** @var Response $result */
        $result = $this->controller->admin();

        $this->assertInstanceOf(Response::class, $result);
        $this->assertEquals(200, $result->getStatusCode());
    }


}
