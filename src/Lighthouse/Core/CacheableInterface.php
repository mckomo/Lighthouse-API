<?php

namespace Lighthouse\Core;

interface CacheableInterface
{
    public function cacheKey();
    public function cacheValue();
}
