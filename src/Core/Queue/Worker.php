<?php

declare(strict_types=1);

namespace Katsarov\MailerliteTest\Core\Queue;

use Katsarov\MailerliteTest\Core\Cache;
use Katsarov\MailerliteTest\Core\Config;

final class Worker
{
    private const INT SLEEP_SECONDS = 10;
    private \PDO $pdo;

    public function __construct(private readonly Cache $cache, private readonly Config $config)
    {
        $host = $this->config->get('DB_HOST');
        $db = $this->config->get('DB_NAME');
        $user = $this->config->get('DB_USER');
        $pass = $this->config->get('DB_PASS');
        $dsn = "mysql:host=$host;dbname=$db;charset=UTF8";

        $this->pdo = new \PDO($dsn, $user, $pass);
    }

    /**
     * @throws \ReflectionException
     * @throws \RedisException
     * @throws \JsonException
     */
    public function work(): void
    {
        while (true) {
            $job = $this->cache->pop('jobs');
            if (!$job) {
                sleep(self::SLEEP_SECONDS);
                continue;
            }
            $jobEntity = unserialize($job, [true]);
            $reflection = new \ReflectionClass($jobEntity);
            if ($reflection->hasMethod('handle')) {
                $jobEntity->handle();
                continue;
            }
            $jobClassName = $jobEntity::class;
            $jobHandlerClass = $jobClassName . 'Handler';
            if (class_exists($jobHandlerClass)) {
                $jobHandler = new $jobHandlerClass($this->pdo);
                try {
                    /**
                     * @phpstan-ignore-next-line
                     */
                    $jobHandler->handle($jobEntity);
                } catch (\Throwable $exception) {
                    $this->cache->put('failed', json_encode([
                        'job' => $job,
                        'message' => $exception->getMessage()
                    ], JSON_THROW_ON_ERROR));
                }
            }
            if (defined('PHPUNIT_RUNNING')) {
                return;
            }
        }
    }
}
