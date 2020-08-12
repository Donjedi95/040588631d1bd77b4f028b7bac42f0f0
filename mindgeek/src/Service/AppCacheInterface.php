<?php

namespace App\Service;

interface AppCacheInterface
{
    public const CACHE_SHOWCASE_JSON_KEY = 'app_showcase_response';

    public const CACHE_NAMESPACE = 'app_cache';
    public const CACHE_DEFAULT_LIFE_TIME = 3600; // 1 hour
    public const CACHE_DIRECTORY = 'var/cache/app';
}