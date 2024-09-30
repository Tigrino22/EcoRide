<?php

namespace Tests\Core\Renderer;

use PHPUnit\Framework\TestCase;
use Tigrino\Core\Renderer\PHPRenderer;

class PHPRendererTest extends TestCase
{
    private PHPRenderer $renderer;
    public function setUp(): void
    {
        $this->renderer = new PHPRenderer();
    }

    public function testRenderTheRightPath()
    {
        $this->renderer->addPath(dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'Templates', 'blog');
        $content = $this->renderer->render('@blog/demo');

        $this->assertEquals("Hello guys", $content);
    }

    public function testRenderTheDefaultPath()
    {
        $this->renderer->addPath(dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'Templates');
        $content = $this->renderer->render('demo');

        $this->assertEquals("Hello guys", $content);
    }

    public function testRenderWithParams()
    {
        $this->renderer->addPath(dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'Templates');
        $content = $this->renderer->render('demoParams', ['name' => 'Tigrino']);

        $this->assertEquals("Hello Tigrino", $content);
    }

    public function testRenderWithGlobal()
    {
        $this->renderer->addPath(dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'Templates');
        $this->renderer->addGlobals('name', 'Tigrino');
        $content = $this->renderer->render('demoParams');

        $this->assertEquals("Hello Tigrino", $content);
    }
}
