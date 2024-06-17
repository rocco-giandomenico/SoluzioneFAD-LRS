<?php

namespace Trax\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Trax\Auth\Stores\Accesses\AccessService;

class Caching
{
    /**
     * Check if the cache is enabled.
     *
     * @return bool
     */
    public static function redisEnabled()
    {
        return config('cache.default') == 'redis';
    }

    /**
     * Check and return a message.
     *
     * @return string
     */
    public static function checkRedis(): string
    {
        if (config('cache.default', 'file') != 'redis') {
            return 'Redis cache is not activated. You should add CACHE_DRIVER=redis to your .env file...';
        }
    
        return self::checkRedisIO()
            ? 'Redis cache is up and running!'
            : 'Redis cache does not work. Please, check the doc :(';
    }

    /**
     * Check and return a boolean.
     *
     * @return bool
     */
    public static function checkRedisIO(): bool
    {
        Cache::store('redis')->put('check', 'check_value', 1);
        return Cache::store('redis')->get('check') == 'check_value';
    }

    /**
     * Get the access from the cache first.
     *
     * @param  string  $source
     * @param  string  \Trax\Auth\Stores\Accesses\AccessService
     * @return \Trax\Auth\Stores\Accesses\Access
     */
    public static function getAccess(string $source, AccessService $accesses)
    {
        // Always cache the access, be it in a file.

        // We keep the access 5 seconds in cache to speed series of requests.
        // The cache is not reset when the access is modified or deleted,
        // so there will be a 5 delay to reflect changes.

        return Cache::remember("access_$source", 5, function () use ($source, $accesses) {
            return $accesses->findByUuid($source);
        });
    }
}
