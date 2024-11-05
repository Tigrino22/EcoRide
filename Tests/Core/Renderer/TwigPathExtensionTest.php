<?php

namespace Core\Renderer;

use DI\NotFoundException;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Tigrino\Core\Renderer\Extensions\TwigPathExtension;
use Tigrino\Core\Router\Router;

class TwigPathExtensionTest extends TestCase
{
    private TwigPathExtension $extension;
    private ContainerInterface $container;
    private Router $router;

    protected function setUp(): void
    {
        $this->container = $this->createMock(ContainerInterface::class);
        $this->router = $this->createMock(Router::class);

        $this->extension = new TwigPathExtension($this->container);
    }

    public function testPathGeneratesCorrectUrl(): void
    {
        $this->container->expects($this->once())
            ->method('get')
            ->with(Router::class)
            ->willReturn($this->router);

        $this->router->expects($this->once())
            ->method('generate')
            ->with('home', [])
            ->willReturn('/home');

        $result = $this->extension->path('home');
        $this->assertEquals('/home', $result);
    }

    public function testPathThrowsNotFoundException(): void
    {
        $this->container
            ->expects($this->once())
            ->method('get')
            ->with(Router::class)
            ->willThrowException(new NotFoundException());

        $this->expectException(NotFoundException::class);

        $this->extension->path('home');
    }

    public function testPathThrowsContainerException(): void
    {
        $this->container
            ->expects($this->once())
            ->method('get')
            ->with(Router::class)
            ->willThrowException($this->createMock(NotFoundExceptionInterface::class));

        $this->expectException(NotFoundException::class);

        $this->extension->path('home');
    }
}
