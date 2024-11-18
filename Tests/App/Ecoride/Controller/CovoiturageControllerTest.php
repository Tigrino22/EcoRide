<?php

namespace App\Ecoride\Controller;

use DI\ContainerBuilder;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Tests\Core\Renderer\FakeRenderer;
use Tigrino\App\Ecoride\Controller\CovoiturageController;
use Tigrino\Core\Renderer\RendererInterface;

use function DI\autowire;

class CovoiturageControllerTest extends TestCase
{
    private ContainerInterface $container;
    private CovoiturageController $controller;

    protected function setUp(): void
    {
        $builder = new ContainerBuilder();
        $builder->addDefinitions([
            RendererInterface::class => autowire(FakeRenderer::class),
        ]);
        $this->container = $builder->build();
        $this->controller = new CovoiturageController($this->container);
    }

    public function testIndex()
    {
        /** @var Response $result */
        $result = $this->controller->index();

        $this->assertInstanceOf(Response::class, $result);
        $this->assertEquals(200, $result->getStatusCode());
    }

    public function testSearchCovoiturage()
    {
        /** @var Response $result */
        $result = $this->controller->searchCovoiturage();

        $this->assertInstanceOf(Response::class, $result);
        $this->assertEquals(200, $result->getStatusCode());
    }
}