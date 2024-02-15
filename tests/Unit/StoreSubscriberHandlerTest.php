<?php

declare(strict_types=1);

namespace Unit;

use Katsarov\MailerliteTest\Core\Cache;
use Katsarov\MailerliteTest\Core\Config;
use Katsarov\MailerliteTest\Jobs\StoreSubscriber;
use Katsarov\MailerliteTest\Jobs\StoreSubscriberHandler;
use Katsarov\MailerliteTest\SubscriptionService;
use PDO;
use PHPUnit\Framework\TestCase;
use Redis;
use RedisException;

final class StoreSubscriberHandlerTest extends TestCase
{
    private PDO $pdo;

    protected function setUp(): void
    {
        $this->pdo = new PDO('sqlite::memory:');
        /** @var string $tableQuery */
        $tableQuery = file_get_contents(__DIR__ . '/../stubs/subscribers.sql');
        $this->pdo->exec($tableQuery);
        parent::setUp();
    }

    /**
     * @throws RedisException
     */
    public function testHandler(): void
    {
        $config = new Config();
        $job = new StoreSubscriber(
            'example@gmail.com',
            'Nikola',
            'Katsarov',
            'active'
        );
        $handler = new StoreSubscriberHandler($this->pdo);
        $handler->handle($job);

        $service = new SubscriptionService(
            $config,
            new Cache(new Redis(), $config)
        );

        /** @var array<string|string> $result */
        $result = $service->listSubscriptions();
        $this->assertCount(1, $result);
    }
}
