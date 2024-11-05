<?php

namespace Tigrino\Services;

use Tigrino\Core\Session\SessionManagerInterface;

class CSRFService
{
    private SessionManagerInterface $sessionManager;

    public function __construct(SessionManagerInterface $sessionManager)
    {
        $this->sessionManager = $sessionManager;
    }

    /**
     * Génère et stocke un token CSRF pour un formulaire donné.
     *
     * @param string $name Le nom unique du formulaire.
     * @return string Le token CSRF généré.
     */
    public function generateToken(string $name): string
    {
        $token = bin2hex(random_bytes(32));
        $this->sessionManager->set('csrf_' . $name, $token);
        return $token;
    }

    /**
     * Vérifie le token CSRF reçu et le supprime s'il est valide.
     *
     * @param string $name Le nom unique du formulaire.
     * @param string $token Le token CSRF reçu.
     * @return bool
     */
    public function validateToken(string $name, string $token): bool
    {
        $sessionToken = $this->sessionManager->get('csrf_' . $name);

        if ($sessionToken && hash_equals($sessionToken, $token)) {
            $this->sessionManager->remove('csrf_' . $name);
            return true;
        }

        return false;
    }
}
