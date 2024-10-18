<?php

namespace Tigrino\Core\Controller;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Tigrino\Core\Renderer\RendererInterface;

abstract class AbstractController
{
    /**
     * @var RequestInterface|ServerRequestInterface
     */
    protected RequestInterface|ServerRequestInterface $request;
    /**
     * @var ContainerInterface
     */
    protected ContainerInterface $container;
    private RendererInterface $renderer;

    /**
     * Constructeur de base pour injecter le container.
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->renderer = $this->container->get(RendererInterface::class);
    }

    /**
     * Méthode de rendu directement implémenter dans le controller
     *
     * @param string $view
     * @param array $params
     * @return string
     */
    protected function render(string $view, array $params = []): string
    {
        return $this->renderer->render($view, $params);
    }
    /**
     * Exécute la méthode demandée avec les paramètres et la requête.
     *
     * @param string $method
     * @param array $params
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     * @throws \Exception
     */
    public function execute(string $method, array $params, ServerRequestInterface $request): ResponseInterface
    {

        $this->request = $request;

        if (method_exists($this, $method)) {
            return $this->$method(...$params);
        } else {
            throw new \Exception("Méthode {$method} non trouvée dans le contrôleur.");
        }
    }
}
