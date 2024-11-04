<?php

namespace Tigrino\Services;

use Tigrino\Core\Session\SessionManager;

class FlashService
{
    private SessionManager $sessionManager;

    public function __construct(SessionManager $sessionManager)
    {
        $this->sessionManager = $sessionManager;
    }

    /**
     * Ajoute un message flash dans la session.
     *
     * @param string $type Le type de message (par exemple 'success', 'error').
     * @param string $message Le contenu du message.
     */
    public function add(string $type, string $message): void
    {
        $flashMessages = $this->sessionManager->get('flash', []);

        $flashMessages[$type][] = $message;

        $this->sessionManager->set('flash', $flashMessages);
    }

    /**
     * Récupère tous les messages flash et les supprime de la session.
     *
     * @return array Les messages flash organisés par type.
     */
    public function getMessages(): array
    {
        $flashMessages = $this->sessionManager->get('flash', []);
        $this->sessionManager->remove('flash');

        return $flashMessages;
    }
}
