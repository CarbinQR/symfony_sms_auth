<?php

namespace App\Service\Cache;

interface CacheServiceInterface
{
    public function set(string $key, mixed $value, int $lifetime = 0): void;

    public function get(string $key);

    public function delete(string $key): void;
}