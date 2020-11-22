<?php

namespace App\Adapters;

use App\Contracts\Adapters\CacheAdapterInterface;
use Exception;
use Redis;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class RedisAdapter implements CacheAdapterInterface
{
    const TIMEOUT = 60 * 60;

    const REDIS_HOST_PARAMETER_KEY = 'redis_host';
    const REDIS_PORT_PARAMETER_KEY = 'redis_port';
    const REDIS_DATABASE_ID_PARAMETER_KEY = 'redis_database_id';
    const REDIS_PREFIX_PARAMETER_KEY = 'redis_prefix';

    /**
     * @var string
     */
    protected $prefix;

    /**
     * @var Redis
     */
    private $redis;

    /**
     * @var string
     */
    private $host;

    /**
     * @var int
     */
    private $port;

    /**
     * @var string
     */
    private $databaseId;

    /**
     * RedisAdapter constructor.
     *
     * @param ParameterBagInterface $parameterBag
     */
    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->host = $parameterBag->get(self::REDIS_HOST_PARAMETER_KEY);
        $this->port = $parameterBag->get(self::REDIS_PORT_PARAMETER_KEY);
        $this->databaseId = $parameterBag->get(self::REDIS_DATABASE_ID_PARAMETER_KEY);
        $this->prefix = $parameterBag->get(self::REDIS_PREFIX_PARAMETER_KEY);
    }

    /**
     * @param string $key
     *
     * @return mixed
     */
    public function get(string $key)
    {
        return $this->getRedis()->get($key);
    }

    /**
     * @return Redis|CacheAdapterInterface
     */
    public function getRedis()
    {
        if ($this->redis === null) {
            try {
                $this->redis = new Redis();
                $this->redis->connect($this->host, $this->port);
                $this->redis->select($this->databaseId);
                $this->redis->setOption(Redis::OPT_PREFIX, $this->prefix);
            } catch (Exception $exception) {
                $this->redis = new DummyCacheAdapter();
            }
        }

        return $this->redis;
    }

    /**
     * @param string $key
     * @param $value
     * @return bool
     */
    public function set(string $key, $value)
    {
        return $this->getRedis()->set($key, $value, self::TIMEOUT);
    }

    /**
     * @param string $key
     *
     * @return int
     */
    public function del(string $key)
    {
        return $this->getRedis()->del($key);
    }

    /**
     * @return string
     */
    public function getPrefix(): string
    {
        return $this->prefix;
    }
}
