<?php

declare(strict_types=1);

namespace Katsarov\MailerliteTest\Core\Queue;

use Katsarov\MailerliteTest\Core\Cache;

final readonly class Queue
{
    public function __construct(private Cache $cache)
    {
    }

    /**
     * @throws \RedisException
     */
    public function store(mixed $job): void
    {
        $this->cache->put('jobs', serialize($job));
    }
}
