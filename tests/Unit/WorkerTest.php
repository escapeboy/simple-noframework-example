<?php

declare(strict_types=1);

namespace Unit;

use JsonException;
use Katsarov\MailerliteTest\Core\Cache;
use Katsarov\MailerliteTest\Core\Config;
use Katsarov\MailerliteTest\Core\Queue\Worker;
use Katsarov\MailerliteTest\SubscriptionService;
use PDO;
use PHPUnit\Framework\TestCase;
use Redis;
use RedisException;
use ReflectionException;

final class WorkerTest extends TestCase
{
    protected function setUp(): void
    {
        $pdo = new PDO('sqlite::memory:');
        /** @var string $tableQuery */
        $tableQuery = file_get_contents(__DIR__ . '/../stubs/subscribers.sql');
        $pdo->exec($tableQuery);
        parent::setUp();
    }

    /**
     * @throws RedisException
     * @throws ReflectionException
     * @throws JsonException
     */
    public function testWorker(): void
    {
        $config = new Config();
        $cache = new Cache(
            new Redis(),
            $config
        );
        $service = new SubscriptionService(
            $config,
            $cache
        );

        $service->storeSubscriber(
            'example1@gmail.com',
            'Nikola',
            'Katsarov',
            'active'
        );
        $worker = new Worker($cache, $config);
        $worker->work();
        /** @var array<string|string> $result */
        $result = $service->listSubscriptions();
        $this->assertCount(1, $result);
        $this->assertTrue($service->checkIfEmailExists('example@gmail.com'));
    }
}
