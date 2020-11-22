<?php

namespace App\Contracts\Adapters;

interface CacheAdapterInterface
{
    /**
     * @param string $key
     *
     * @return mixed
     */
    public function get(string $key);

    /**
     * @param string $key
     * @param $value
     *
     * @return mixed
     */
    public function set(string $key, $value);

    /**
     * @param string $key
     *
     * @return mixed
     */
    public function del(string $key);
}