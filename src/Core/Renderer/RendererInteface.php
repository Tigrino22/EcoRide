<?php

namespace Tigrino\Core\Renderer;

interface RendererInteface
{
    /**
     * Renseigne le chemin des templates
     * possibilité de mettre un namespace pour les templates
     * "@namespace/view"
     *
     * @param string $path
     * @param string|null $namespace
     * @return void
     */
    public function addPath(string $path, ?string $namespace = null): void;

    /**
     * Rend un vue :
     *  - "view"
     *  - "@namespace/view"
     *
     * @param string $view
     * @param array $params
     * @return string
     */
    public function render(string $view, array $params = []): string;

    /**
     * Ajoute une variable globale accessible à tout le render
     *
     * @param string $key
     * @param $value
     * @return void
     */
    public function addGlobals(string $key, $value): void;
}
