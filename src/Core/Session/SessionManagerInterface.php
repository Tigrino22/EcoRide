<?php

namespace Tigrino\Core\Session;

use Ramsey\Uuid\Uuid;

interface SessionManagerInterface
{
    public function createSession(string $userId): string;

    public function destroySession(string $sessionToken): bool;

    public function validateSession(string $sessionToken): bool;

    public function getUserFromSession(string $sessionToken): ?int;
}
