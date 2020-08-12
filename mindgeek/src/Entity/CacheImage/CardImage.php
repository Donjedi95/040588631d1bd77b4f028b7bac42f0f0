<?php

namespace App\Entity\CacheImage;

use App\Entity\AbstractCacheImage;

class CardImage extends AbstractCacheImage
{
    public const CARD_IMAGES_ARRAY_KEY = 'cardImages';
    public const CARD_IMAGE_DIRECTORY = 'images/{id}/cardImages';
    public const CARD_IMAGE_URL_KEY = 'url';
    public const CARD_IMAGE_CACHE_KEY = '{id}_cardImage_{imageName}';

    public function getImageUrlKey(): string
    {
        return self::CARD_IMAGE_URL_KEY;
    }

    public function getArrayKey(): string
    {
        return self::CARD_IMAGES_ARRAY_KEY;
    }

    public function getCacheKey($imageName): string
    {
        return strtr(
            self::CARD_IMAGE_CACHE_KEY,
            [
                '{id}' => $this->getMovieId(),
                '{imageName}' => $imageName
            ]
        );
    }

    public function getDirectory(): string
    {
        return strtr(
            self::CARD_IMAGE_DIRECTORY,
            [
                '{id}' => $this->getMovieId()
            ]
        );
    }
}