<?php

namespace Tigrino\Core\Renderer\Extensions;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class TwigSessionExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('session', [$this, 'session']),
        ];
    }

    public function session(string $key = null)
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        if ($key) {
            return $_SESSION[$key] ?? null;
        }
        return $_SESSION;
    }
}
