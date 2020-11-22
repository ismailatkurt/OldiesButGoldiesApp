<?php

namespace App\Adapters;

use App\Contracts\Adapters\CacheAdapterInterface;

class DummyCacheAdapter implements CacheAdapterInterface
{
    /**
     * @param string $key
     *
     * @return mixed
     */
    public function get(string $key)
    {
        return null;
    }

    /**
     * @param string $key
     * @param $value
     * @return bool
     */
    public function set(string $key, $value)
    {
        return null;
    }

    /**
     * @param string $key
     *
     * @return mixed|null
     */
    public function del(string $key)
    {
        return null;
    }
}
