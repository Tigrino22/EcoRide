<?php

namespace Core\Renderer;

use PHPUnit\Framework\TestCase;
use Tigrino\Core\Renderer\Extensions\TwigAssetsExtension;

class TwigAssetsExtensionsTest extends TestCase
{
    private TwigAssetsExtension $extension;

    protected function setUp(): void
    {
        $this->extension = new TwigAssetsExtension(
            assetPath: dirname(__DIR__, 3) . '/Templates/assets',
            env: 'DEV'
        );
    }

    public function testLinkInDevMode(): void
    {
        $result = $this->extension->link('main');

        $this->assertEmpty($result); // Doit retourner une chaîne vide car en mode dev le CSS est chargé via JS
    }

    public function testLinkInProdMode(): void
    {
        $this->extension = new TwigAssetsExtension(
            assetPath: dirname(__DIR__, 3) . '/Templates/assets',
            env: 'PROD'
        );

        $result = $this->extension->link('main', ['media' => 'all']);
        $this->assertStringContainsString('<link rel="stylesheet"', $result);
        $this->assertStringContainsString('media="all"', $result);
    }

    public function testScriptInDevMode(): void
    {
        $result = $this->extension->script('main');

        $this->assertStringContainsString('http://localhost:3000/@vite/client', $result);
        $this->assertStringContainsString('src="http://localhost:3000/assets/ts/main.ts"', $result);
    }


    public function testScriptInProdMode(): void
    {
        $this->extension = new TwigAssetsExtension(
            assetPath: dirname(__DIR__, 3) . '/Templates/assets',
            env: 'PROD'
        );

        $result = $this->extension->script('main');

        $this->assertStringContainsString('<script src="assets/', $result);
    }
}
