<?php

namespace App\Service\Cache;

use Predis\Client;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class RedisService implements CacheServiceInterface
{
    private Client $client;

    public function __construct(string $redisUrl)
    {
        $this->client = new Client($redisUrl);
    }

    public function set(string $key, mixed $value, int $lifetime = 0): void
    {
        $this->tryCallback(function () use ($key, $value, $lifetime) {
            $this->client->set($key, $value);
            if ($lifetime > 0) {
                $this->client->expire($key, $lifetime);
            }
        });
    }

    public function get(string $key): ?array
    {
        $cacheData = $this->tryCallback(function () use ($key) {
            return $this->client->get($key);
        });

        return (!$cacheData)
            ? null
            : json_decode($cacheData, true, 512, JSON_THROW_ON_ERROR);
    }

    public function delete(string $key): void
    {
        $this->tryCallback(function () use ($key) {
            $this->client->del($key);
        });
    }

    protected function tryCallback(callable $callback): mixed
    {
        try {
            return $callback();
        } catch (\Exception $e) {
            throw new HttpException(Response::HTTP_BAD_REQUEST, $e->getMessage());
        }
    }
}