<?php

namespace Tigrino\Core\Renderer\Extensions;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class TwigAssetsExtension extends AbstractExtension
{
    private ?array $paths = null;
    private bool $polyfillLoaded = false;
    private readonly bool $isProduction;
    private readonly string $assetPath;

    public function __construct(
        string $assetPath,
        string $env
    ) {
        $this->assetPath = $assetPath;
        $this->isProduction = 'PROD' === $env;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('vite_entry_link_tags', $this->link(...), ['is_safe' => ['html']]),
            new TwigFunction('vite_entry_script_tags', $this->script(...), ['is_safe' => ['html']])
        ];
    }

    /**
     * @throws \JsonException
     */
    public function link(string $name, array $attrs = []): string
    {
        $uri = $this->uri($name . '.css');
        if (strpos($uri, ':3000')) {
            return ''; // Chargement du CSS via la balise script de l'env de dev
        }

        $attributes = implode(' ', array_map(fn($key) => "{$key}=\"{$attrs[$key]}\"", array_keys($attrs)));

        return sprintf(
            '<link rel="stylesheet" href="%s" %s>',
            $this->uri($name . '.css'),
            empty($attributes) ? '' : (' ' . $attributes)
        );
    }

    public function script(string $name): string
    {
        $extension = $this->isProduction ? '.js' : '.ts';

        if (!$this->isProduction) {
            $script = '
            <script type="module" src="http://localhost:3000/@vite/client"></script>
            <script src="' . $this->uri('ts/' . $name . $extension) . '" type="module" defer></script>
            ';
        } else {
            $script = '<script src="' . $this->uri($name . $extension) . '" type="module" defer></script>';
        }

        /**
         * Partie du code à revoir servant à gérer le polyfiled pour éviter
         *
         */
//        if (!$this->polyfillLoaded && isset($_SERVER['HTTP_USER_AGENT'])) {
//            $userAgent = $_SERVER['HTTP_USER_AGENT'];
//            if (strpos($userAgent, 'Safari') && !strpos($userAgent, 'Chrome')) {
//                $this->polyfillLoaded = true;
//                $script = <<<HTML
//                    <script src="//unpkg.com/document-register-element" defer></script>
//                    $script
//                HTML;
//            }
//        }

        return $script;
    }

    /**
     * @throws \JsonException
     */
    private function getAssetPaths(): array
    {
        if ($this->paths === null) {
            $manifest = $this->assetPath . '/.vite/manifest.json';

            if (file_exists($manifest)) {
                // Charger et assigner les chemins d'assets à $this->paths
                $this->paths = json_decode(file_get_contents($manifest), true, 512, JSON_THROW_ON_ERROR);
            } else {
                $this->paths = [];  // Si le fichier n'existe pas, assigner un tableau vide
            }
        }

        // Retourner les chemins d'assets
        return $this->paths;
    }

    /**
     * @throws \JsonException
     */
    private function uri(string $name): string
    {
        if (!$this->isProduction) {
            return "http://localhost:3000/assets/{$name}";
        }

        if (strpos($name, '.css')) {
            $name = $this->getAssetPaths()['assets/ts/main.ts']['css'][0] ?? '';
        } else {
            $name = $this->getAssetPaths()['assets/ts/main.ts']['file'] ?? '';
        }

        return "assets/$name";
    }
}
