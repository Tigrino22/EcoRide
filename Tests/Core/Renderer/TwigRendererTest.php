<?php

namespace Core\Renderer;

use DI\ContainerBuilder;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Tigrino\Core\Renderer\TwigRenderer;
use Tigrino\Core\Session\SessionManager;
use Tigrino\Core\Session\SessionManagerInterface;
use Tigrino\Services\CSRFService;

use function DI\autowire;

class TwigRendererTest extends TestCase
{
    private TwigRenderer $twigRenderer;
    private ?string $templatePath = null;
    private ?string $assetPath = null;
    private string $env = 'development';
    private ContainerInterface $container;

    protected function setUp(): void
    {
        $this->templatePath = dirname(__DIR__, 2) . '/Templates';
        $this->assetPath = dirname(__DIR__, 2) . '/Templates/assets';

        $builder = new ContainerBuilder();
        $builder->addDefinitions([
            SessionManagerInterface::class => autowire(SessionManager::class),
        ]);
        $this->container = $builder->build();

        $this->twigRenderer = new TwigRenderer($this->templatePath, $this->assetPath, $this->env, $this->container);
    }

    public function testAddPath(): void
    {
        $this->twigRenderer->addPath('/Home', 'Home');
        $this->assertContains('/Home', $this->twigRenderer->getPath('Home'));
    }

    public function testRender(): void
    {
        $view = 'demo';
        $params = ['name' => 'Tigrino'];

        $this->assertSame('Hello Tigrino', $this->twigRenderer->render($view, $params));
    }

    public function testAddGlobals(): void
    {
        $this->twigRenderer->addGlobals('globalKey', 'globalValue');
        $view = 'global';

        $this->assertSame('global : globalValue', $this->twigRenderer->render($view));
    }
}
