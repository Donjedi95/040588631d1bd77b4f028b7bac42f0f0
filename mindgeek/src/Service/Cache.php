<?php

namespace App\Service;

use Psr\Cache\InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Contracts\Cache\ItemInterface;

class Cache
{
    private LoggerInterface $logger;
    private FilesystemAdapter $cache;
    private Mindgeek $mindgeekService;
    private ParameterBagInterface $parameterBag;

    public function __construct(LoggerInterface $logger, ParameterBagInterface $parameterBag, Mindgeek $mindgeekService)
    {
        $this->logger = $logger;
        $this->parameterBag = $parameterBag;
        $this->mindgeekService = $mindgeekService;
        $this->cache = new FilesystemAdapter(
            $this->parameterBag->get('cache_namespace'),
            $this->parameterBag->get('cache_default_life_time'),
            $this->parameterBag->get('kernel.project_dir') . '/' . $this->parameterBag->get('cache_directory')
        );
    }

    /**
     * @param string $key
     * @param array $imageData
     * @param string $id
     * @param string $directory
     * @return mixed
     * @throws InvalidArgumentException
     */
    public function getCachedImage(string $key, array $imageData, string $id, string $directory)
    {
        return $this->cache->get($key, function(ItemInterface $item) use ($key, $imageData, $id, $directory) {
            $this->logger->info("cache key {$key} not found on the server");
            $item->expiresAfter(3600);
            return $this->mindgeekService->getImageFromUrl($imageData, $directory);
        });
    }

    /**
     * @param $key
     * @return bool
     * @throws InvalidArgumentException
     */
    public function deleteCache($key): bool
    {
        return $this->cache->delete($key);
    }
}
