<?php

declare(strict_types=1);

namespace Katsarov\MailerliteTest\Core;

final readonly class Cache
{
    /**
     * @throws \RedisException
     */
    public function __construct(private \Redis $redis, private Config $config)
    {
        $this->redis->connect(
            host: $this->config->get('REDIS_HOST'),
        );
    }

    /**
     * @throws \RedisException
     */
    public function put(string $key, string $data): void
    {
        $this->redis->rPush($key, $data);
    }

    /**
     * @throws \RedisException
     */
    public function get(string $key): mixed
    {
        return $this->redis->get($key);
    }

    /**
     * @throws \RedisException
     */
    public function pop(string $key): string|false
    {
        return $this->redis->lPop($key);
    }
}
