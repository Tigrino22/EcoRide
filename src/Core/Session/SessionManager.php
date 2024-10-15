<?php

namespace Tigrino\Core\Session;

use Tigrino\Core\Session\SessionManagerInterface;

class SessionManager implements SessionManagerInterface
{
    public function set(string $key, mixed $value): void
    {
        $this->startSession();
        $_SESSION[$key] = $value;
    }

    public function get(string $key, mixed $default = null): mixed
    {
        $this->startSession();
        return $_SESSION[$key] ?? $default;
    }

    public function has(string $key): bool
    {
        $this->startSession();
        return isset($_SESSION[$key]);
    }

    public function remove(string $key): void
    {
        $this->startSession();
        unset($_SESSION[$key]);
        if (empty($_SESSION)) {
            session_destroy();
        }
    }

    private function startSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
}
