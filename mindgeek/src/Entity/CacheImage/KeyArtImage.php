<?php

namespace App\Entity\CacheImage;

use App\Entity\AbstractCacheImage;

class KeyArtImage extends AbstractCacheImage
{
    public const KEY_ART_IMAGES_ARRAY_KEY = 'keyArtImages';
    public const KEY_ART_IMAGE_URL_KEY = 'url';
    public const KEY_ART_IMAGE_DIRECTORY = 'images/{id}/keyArtImages';
    public const KEY_ART_IMAGE_CACHE_KEY = '{id}_keyArtImage_{imageName}';

    public function getImageUrlKey(): string
    {
        return self::KEY_ART_IMAGE_URL_KEY;
    }

    public function getArrayKey(): string
    {
        return self::KEY_ART_IMAGES_ARRAY_KEY;
    }

    public function getCacheKey($imageName): string
    {
        return strtr(
            self::KEY_ART_IMAGE_CACHE_KEY,
            [
                '{id}' => $this->movie['id'],
                '{imageName}' => $imageName
            ]
        );
    }

    public function getDirectory(): string
    {
        return strtr(
            self::KEY_ART_IMAGE_DIRECTORY,
            [
                '{id}' => $this->movie['id']
            ]
        );
    }
}