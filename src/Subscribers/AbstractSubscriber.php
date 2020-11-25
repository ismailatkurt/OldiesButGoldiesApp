<?php

namespace App\Subscribers;

use App\Adapters\Cache\RedisAdapter;

abstract class AbstractSubscriber
{
    /**
     * @var RedisAdapter
     */
    protected RedisAdapter $cacheAdapter;

    /**
     * @param RedisAdapter $cacheAdapter
     */
    public function __construct(RedisAdapter $cacheAdapter)
    {
        $this->cacheAdapter = $cacheAdapter;
    }

    /**
     * @param string $pattern
     */
    protected function deleteWithPattern(string $pattern)
    {
        $keys = $this->cacheAdapter->getRedis()->keys($pattern);
        foreach ($keys as $key => $redisKey) {
            $this->cacheAdapter->del(substr($redisKey, strlen($this->cacheAdapter->getPrefix())));
        }
    }
}
