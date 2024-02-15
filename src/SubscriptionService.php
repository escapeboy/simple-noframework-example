<?php

declare(strict_types=1);

namespace Katsarov\MailerliteTest;

use Katsarov\MailerliteTest\Core\Cache;
use Katsarov\MailerliteTest\Core\Config;
use Katsarov\MailerliteTest\Jobs\StoreSubscriber;
use PDO;

final class SubscriptionService
{
    private PDO $pdo;

    public function __construct(
        private readonly Config $config,
        private readonly Cache $cache
    ) {
        $host = $this->config->get('DB_HOST');
        $db = $this->config->get('DB_NAME');
        $user = $this->config->get('DB_USER');
        $pass = $this->config->get('DB_PASS');
        $dsn = "mysql:host=$host;dbname=$db;charset=UTF8";

        $this->pdo = new PDO($dsn, $user, $pass);
    }

    /**
     * @throws \RedisException
     */
    public function storeSubscriber(
        string $email,
        string $firstName,
        string $lastName,
        string $status
    ): void {
        $job = new StoreSubscriber($email, $firstName, $lastName, $status);
        $this->cache->put('jobs', serialize($job));
    }

    public function deleteSubscriber(int $id): void
    {
        $this->pdo->prepare('DELETE FROM subscribers where `id` = :id')
            ->execute(['id' => $id]);
    }

    /**
     * @param int $page
     * @param int $perPage
     * @return false|array<string|string>
     */
    public function listSubscriptions(int $page = 1, int $perPage = 1): false|array
    {
        $offset = ($perPage * $page) - $perPage;
        $query = $this->pdo->query('SELECT * FROM subscribers LIMIT ' . $perPage . ' OFFSET ' . $offset);
        if (!$query instanceof \PDOStatement) {
            return false;
        }
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function checkIfEmailExists(string $email): bool
    {
        $query = $this->pdo->prepare('SELECT id FROM subscribers WHERE email = :email');
        $query->execute(['email' => $email]);

        return $query->rowCount() > 0;
    }

    public function getSubscribersCount(): int
    {
        $query = $this->pdo->query('SELECT count(id) FROM subscribers');
        if (!$query instanceof \PDOStatement) {
            return 0;
        }
        /**
         * @phpstan-ignore-next-line
         */
        return $query->fetchColumn();
    }
}
