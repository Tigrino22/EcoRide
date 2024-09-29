<?php

namespace Tigrino\Core\Session;

use Ramsey\Uuid\Uuid;
use Random\RandomException;
use Tigrino\Core\Database\DatabaseInterface;

class SessionManager implements SessionManagerInterface
{
    private DatabaseInterface $db;

    public function __construct(DatabaseInterface $db)
    {
        $this->db = $db;
    }

    /**
     * Crée un session pour l'utilisateur.
     *
     * @param int $userId
     * @return string
     * @throws RandomException
     */
    public function createSession(string $userId): string
    {
        $sessionToken = bin2hex(random_bytes(32));
        // Sauvegarde en base avec l'ID de l'utilisateur
        $this->db->execute(
            "INSERT INTO sessions (user_id, session_token) VALUES (?, ?)",
            [$userId, $sessionToken]
        );
        return $sessionToken;
    }

    /**
     * Sert à détruire l'utilisateur en session,
     * NON la session elle même.
     * Le jeton en BDD sera supprimé
     *
     * @param string $sessionToken
     * @return bool
     */
    public function destroySession(string $sessionToken): bool
    {
        return $this->db->execute(
            "DELETE FROM sessions WHERE session_token = ?",
            [$sessionToken]
        );
    }

    /**
     * Permet de vérifier si la session est bien valide
     *
     * @param string $sessionToken
     * @return bool
     */
    public function validateSession(string $sessionToken): bool
    {
        $result = $this->db->query(
            "SELECT user_id FROM sessions WHERE session_token = ?",
            [$sessionToken]
        );
        return !empty($result);
    }

    /**
     * Récupère l'utilisateur depuis un token de session
     *
     * @param string $sessionToken
     * @return int|null
     */
    public function getUserFromSession(string $sessionToken): ?int
    {
        $result = $this->db->query(
            query: "SELECT user_id FROM sessions WHERE session_token = ?",
            params: [$sessionToken]
        );
        return $result ? (int)$result['user_id'] : null;
    }
}
