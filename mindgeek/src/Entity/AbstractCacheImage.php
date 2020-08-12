<?php

namespace App\Entity;

use App\Service\Cache;
use Psr\Cache\InvalidArgumentException;

abstract class AbstractCacheImage
{
    private Cache $cache;

    public array $movie;

    public function __construct(Cache $cache, array $movie)
    {
        $this->cache = $cache;
        $this->movie = $movie;
    }

    /**
     * @return array
     * @throws InvalidArgumentException
     */
    public function processImage(): array
    {
        $movie = $this->movie;
        $arrayKey = $this->getArrayKey();
        $movieId = $this->getMovieId();
        $imageCounter = 0;

        if (isset($movie[$arrayKey])) {
            foreach ($movie[$arrayKey] as &$image) {
                $imageUrlKey = $this->getImageUrlKey();
                if (isset($image[$imageUrlKey])) {
                    $imageName = $this->getImageName($image[$imageUrlKey]);
                    $cacheKey = $this->getCacheKey($imageName);
                    $directory = $this->getDirectory();

                    $cachedImage = $this->cache->getCachedImage($cacheKey, $image, $movieId, $directory);
                    if (file_exists($cachedImage)) {
                        $imageCounter++;
                        $image[$imageUrlKey] = $cachedImage;
                    } else {
                        $image[$imageUrlKey] = null;
                    }
                }
            }
        }

        return  $movie;
    }

    public function hasAnImage()
    {
        $hasImage = false;

        foreach ($this->movie[$this->getArrayKey()] as $key => $image)
        {
            $imageName = $this->getImageName($image[$this->getImageUrlKey()]);
            $directory = $this->getDirectory();
            if (!empty($imageName)) {
                if (file_exists($directory . '/' . $imageName)) {
                    $hasImage = true;
                    break;
                }
            }
        }

        return $hasImage;
    }

    protected function getImageName($imageUrl): string
    {
        $imageParts = explode('/', $imageUrl);
        return $imageParts[count($imageParts) - 1];
    }

    protected function getMovieId(): string
    {
        return $this->movie['id'];
    }

    abstract function getImageUrlKey(): string;

    abstract function getArrayKey(): string;

    abstract function getCacheKey($imageName): string;

    abstract function getDirectory(): string;
}