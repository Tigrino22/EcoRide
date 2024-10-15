<?php

namespace Tigrino\Core\Renderer;

use DI\NotFoundException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Tigrino\Core\Router\Router;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class TwigPathExtension extends AbstractExtension
{
    private ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }
    public function getFunctions()
    {
        return [
            new TwigFunction('path', $this->path(...))
        ];
    }

    /**
     * Genere un lien dans la vue Twig
     *
     * @param string $path
     * @param array $params
     * @return string
     * @throws NotFoundException|ContainerExceptionInterface
     */
    private function path(string $path, array $params = []): string
    {
        $params = [];
        try {
            return $this->container->get(Router::class)->generate($path, $params);
        } catch (NotFoundExceptionInterface $e) {
            throw new NotFoundException($e);
        }
    }
}
