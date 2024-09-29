<?php

namespace Tigrino\Core\Database;

use PDO;
use PDOException;
use Tigrino\Core\Database\DatabaseInterface;

class Database implements DatabaseInterface
{
    private PDO $pdo;
    public function __construct()
    {
        // Récupération les informations de connexion depuis les variables d'environnement
        $host = getenv('DB_HOST');
        $dbname = getenv('DB_NAME');
        $user = getenv('DB_USER');
        $password = getenv('DB_PASSWORD');
        $port = getenv('DB_PORT') ?: 3306;

        $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4;port=$port";

        try {
            $this->pdo = new PDO($dsn, $user, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ]);
        } catch (PDOException $e) {
            throw new \RuntimeException("Erreur de connexion à la base de données : " . $e->getMessage());
        }
    }

    /**
     * @inheritDoc
     */
    public function query(string $query, array $params = []): mixed
    {
        try {
            $stmt = $this->pdo->prepare($query);

            if ($stmt->execute($params)) {
                return $stmt->fetchAll();
            }
        } catch (PDOException $e) {
            return false;
        }

        return false;
    }

    /**
     * Execute une requete pour insertion notamment.
     *
     * @param string $query
     * @param array $params
     * @return bool
     */
    public function execute(string $query, array $params = []): bool
    {
        $stmt = $this->pdo->prepare($query);
        return $stmt->execute($params);
    }

    /**
     * @inheritDoc
     */
    public function beginTransaction(): bool
    {
        return $this->pdo->beginTransaction();
    }

    /**
     * @inheritDoc
     */
    public function commit(): bool
    {
        return $this->pdo->commit();
    }

    /**
     * @inheritDoc
     */
    public function rollback(): bool
    {
        return $this->pdo->rollBack();
    }

    /**
     * @inheritDoc
     */
    public function lastInsertId(): string
    {
        return $this->pdo->lastInsertId();
    }

    /**
     * @inheritDoc
     */
    public function getConnection(): PDO
    {
        return $this->pdo;
    }
}
