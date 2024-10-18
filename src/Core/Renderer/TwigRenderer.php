<?php

namespace Tigrino\Core\Renderer;

use Tigrino\Core\Renderer\RendererInterface;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class TwigRenderer implements RendererInterface
{
    /**
     * @var Environment
     */
    private Environment $twig;
    /**
     * @var FilesystemLoader
     */
    private FilesystemLoader $loader;

    public function __construct(string $path, string $assetPath, string $env, $container)
    {
        $this->loader = new FilesystemLoader($path);
        $this->twig = new Environment($this->loader);
        if (session_status() == PHP_SESSION_ACTIVE) {
            $this->addGlobals('session', $_SESSION);
        }
        $this->twig->addExtension(new TwigAssetsExtension($assetPath, $env));
        $this->twig->addExtension(new TwigPathExtension($container));
        $this->twig->addExtension(new TwigSessionExtension());
    }

    /**
     * @inheritDoc
     */
    public function addPath(string $path, ?string $namespace = null): void
    {
        $this->loader->addPath($path, $namespace);
    }

    public function getPath(string $name): array
    {
        return $this->loader->getPaths($name);
    }

    /**
     * @inheritDoc
     */
    public function render(string $view, array $params = []): string
    {
        return $this->twig->render($view . '.html.twig', $params);
    }

    /**
     * @inheritDoc
     */
    public function addGlobals(string $key, $value): void
    {
        $this->twig->addGlobal($key, $value);
    }
}
