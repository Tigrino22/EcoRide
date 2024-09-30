<?php

namespace Tigrino\Core\Renderer;

class PHPRenderer implements RendererInteface
{
    private const string DEFAULT_NAMESPACE = '__main';
    private array $paths = [];
    private array $globals = [];

    public function __construct(?string $defaultPath = null)
    {
        if (!is_null($defaultPath)) {
            $this->addPath($defaultPath);
        }
    }

    /**
     * Renseigne le chemin des templates
     * possibilité de mettre un namespace pour les templates
     * "@namespace/view"
     *
     * @param string $path
     * @param string|null $namespace
     * @return void
     */
    public function addPath(string $path, ?string $namespace = null): void
    {
        if (is_null($namespace)) {
            $this->paths[self::DEFAULT_NAMESPACE] = $path;
        } else {
            $this->paths[$namespace] = $path;
        }
    }

    /**
     * Rend un vue :
     *  - "view"
     *  - "@namespace/view"
     *
     * @param string $view
     * @param array $params
     * @return string
     */
    public function render(string $view, array $params = []): string
    {
        if ($this->hasNamespace($view)) {
            $path = $this->replaceNamespace($view) . '.php';
        } else {
            $path = $this->paths[self::DEFAULT_NAMESPACE] . DIRECTORY_SEPARATOR . $view . '.php';
        }

        ob_start();
        extract($this->globals);
        extract($params);
        require($path);
        return ob_get_clean();
    }

    /**
     * Ajoute une variable globale accessible à tout le render
     *
     * @param string $key
     * @param $value
     * @return void
     */
    public function addGlobals(string $key, $value): void
    {
        $this->globals[$key] = $value;
    }

    /**
     * Return tous les chemins de template
     *
     * @return array
     */
    public function getPaths(): array
    {
        return $this->paths;
    }

    private function hasNamespace(string $view): bool
    {
        return $view[0] === '@';
    }

    private function getNamespace(string $view): string
    {
        return substr($view, 1, strpos($view, '/') - 1);
    }

    private function replaceNamespace(string $view): string
    {
        $namespace = $this->getNamespace($view);
        return str_replace('@' . $namespace, $this->paths[$namespace], $view);
    }
}
