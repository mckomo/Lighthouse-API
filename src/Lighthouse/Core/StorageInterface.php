<?php

namespace Lighthouse\Core;

interface StorageInterface
{
    /**
     * @param string $key
     *
     * @return mixed
     */
    public function has($key);

    /**
     * @param string $key
     *
     * @return mixed
     */
    public function get($key);

    /**
     * @param string $key
     * @param mixed  $value
     *
     * @return mixed
     */
    public function put($key, $value);
}
