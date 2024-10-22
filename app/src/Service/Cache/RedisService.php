<?php

namespace App\Service\Cache;

use Predis\Client;
use function Symfony\Component\DependencyInjection\Loader\Configurator\env;

class RedisService implements CacheServiceInterface
{
    private Client $client;

    public function __construct(string $redisUrl)
    {
        $this->client = new Client($redisUrl);
    }

    public function set(string $key, mixed $value, int $lifetime = 0): void
    {
        $this->client->set($key, $value);
        if ($lifetime > 0) {
            $this->client->expire($key, $lifetime);
        }
    }

    public function get(string $key)
    {
        return $this->client->get($key);
    }

    public function delete(string $key): void
    {
        $this->client->del([$key]);
    }
}