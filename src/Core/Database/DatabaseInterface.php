<?php

namespace Tigrino\Core\Database;

interface DatabaseInterface
{
    /**
     * Exection d'une requète non préparée et sans paramètres.
     *
     * @param string $query
     * @param array $params
     * @return mixed
     */
    public function query(string $query, array $params): mixed;

    /**
     * Execute une requete pour insertion notamment.
     *
     * @param string $query
     * @param array $params
     * @return bool
     */
    public function execute(string $query, array $params = []): bool;

    /**
     * Début d'une transaction
     *
     * @return bool
     */
    public function beginTransaction(): bool;

    /**
     * Fin d'une transaction
     *
     * @return bool
     */
    public function commit(): bool;

    /**
     * Annuler une transaction.
     *
     * @return bool
     */
    public function rollback(): bool;

    /**
     * Récupération du dernier élément inséré
     *
     * @return string
     */
    public function lastInsertId(): string;

    /**
     * Retourne l'instance de PDO en cours
     *
     * @return \PDO
     */
    public function getConnection(): \PDO;
}
